<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Services\AuditLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $paymentStatus = $request->query('payment_status');

        $sales = Sale::with(['customer', 'user'])
            ->when($search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('invoice_no', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($paymentStatus, function ($query, $paymentStatus) {
                $query->where('payment_status', $paymentStatus);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', compact('sales', 'search', 'paymentStatus'));
    }

    public function create()
    {
        $customers = Customer::where('status', 'active')
            ->orderBy('name')
            ->get();

        $products = Product::with('category')
            ->where('status', 'active')
            ->where('quantity', '>', 0)
            ->orderBy('name')
            ->get();

        return view('sales.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'payment_status' => ['required', Rule::in(['unpaid', 'partial', 'paid'])],
            'remarks' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $saleItemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];

                if ($product->quantity < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Insufficient stock for product: {$product->name}. Current stock: {$product->quantity}",
                    ]);
                }

                $unitPrice = $product->selling_price;
                $lineTotal = $unitPrice * $quantity;
                $subtotal += $lineTotal;

                $saleItemsData[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => $lineTotal,
                ];
            }

            $discount = $validated['discount'] ?? 0;
            $tax = $validated['tax'] ?? 0;
            $total = ($subtotal - $discount) + $tax;

            if ($total < 0) {
                throw ValidationException::withMessages([
                    'discount' => 'Discount cannot be greater than subtotal plus tax.',
                ]);
            }

            $sale = Sale::create([
                'invoice_no' => $this->generateInvoiceNo(),
                'customer_id' => $validated['customer_id'] ?? null,
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total' => $total,
                'payment_status' => $validated['payment_status'],
                'remarks' => $validated['remarks'] ?? null,
            ]);

            foreach ($saleItemsData as $saleItemData) {
                $product = $saleItemData['product'];
                $beforeQuantity = $product->quantity;
                $afterQuantity = $beforeQuantity - $saleItemData['quantity'];

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $saleItemData['quantity'],
                    'unit_price' => $saleItemData['unit_price'],
                    'total' => $saleItemData['total'],
                ]);

                $product->update([
                    'quantity' => $afterQuantity,
                ]);

                $movement = StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'stock_out',
                    'quantity' => $saleItemData['quantity'],
                    'before_quantity' => $beforeQuantity,
                    'after_quantity' => $afterQuantity,
                    'reference_no' => $sale->invoice_no,
                    'remarks' => 'Stock deducted from sales invoice.',
                ]);

                AuditLogService::record(
                    'Inventory',
                    'created',
                    'Stock deducted from sales invoice ' . $sale->invoice_no . ' for product: ' . $product->name,
                    $movement,
                    null,
                    $movement->toArray()
                );
            }

            AuditLogService::record(
                'Sales',
                'created',
                'Created sales invoice: ' . $sale->invoice_no,
                $sale,
                null,
                $sale->toArray()
            );
        });

        return redirect()
            ->route('sales.index')
            ->with('success', 'Sales invoice created successfully.');
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items.product.category']);

        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        return redirect()
            ->route('sales.show', $sale)
            ->with('error', 'Sales invoice editing is disabled to protect stock accuracy.');
    }

    public function update(Request $request, Sale $sale)
    {
        return redirect()
            ->route('sales.show', $sale)
            ->with('error', 'Sales invoice editing is disabled to protect stock accuracy.');
    }

    public function destroy(Sale $sale)
    {
        return redirect()
            ->route('sales.index')
            ->with('error', 'Sales invoice deletion is disabled to protect audit and stock history.');
    }

    private function generateInvoiceNo(): string
    {
        $prefix = 'INV-' . now()->format('Ymd') . '-';

        $latestSale = Sale::where('invoice_no', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        if (! $latestSale) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($latestSale->invoice_no, -4);
        $nextNumber = $lastNumber + 1;

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function pdf(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items.product.category']);

        $pdf = Pdf::loadView('sales.pdf', compact('sale'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($sale->invoice_no . '.pdf');
    }
}
