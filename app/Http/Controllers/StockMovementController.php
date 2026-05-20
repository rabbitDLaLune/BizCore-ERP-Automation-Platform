<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $type = $request->query('type');

        $movements = StockMovement::with(['product.category', 'user'])
            ->when($search, function ($query, $search) {
                $query->where(function ($mainQuery) use ($search) {
                    $mainQuery->whereHas('product', function ($productQuery) use ($search) {
                        $productQuery->where('sku', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    })
                        ->orWhere('reference_no', 'like', "%{$search}%");
                });
            })
            ->when($type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('stock-movements.index', compact('movements', 'search', 'type'));
    }

    public function create()
    {
        $products = Product::with('category')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('stock-movements.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', Rule::in(['stock_in', 'stock_out'])],
            'quantity' => ['required', 'integer', 'min:1'],
            'reference_no' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::lockForUpdate()->findOrFail($validated['product_id']);

            $beforeQuantity = $product->quantity;

            if ($validated['type'] === 'stock_in') {
                $afterQuantity = $beforeQuantity + $validated['quantity'];
            } else {
                if ($beforeQuantity < $validated['quantity']) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Stock out quantity cannot be greater than current stock.',
                    ]);
                }

                $afterQuantity = $beforeQuantity - $validated['quantity'];
            }

            $product->update([
                'quantity' => $afterQuantity,
            ]);

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'before_quantity' => $beforeQuantity,
                'after_quantity' => $afterQuantity,
                'reference_no' => $validated['reference_no'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
            ]);
        });

        return redirect()
            ->route('stock-movements.index')
            ->with('success', 'Stock movement recorded successfully.');
    }
}
