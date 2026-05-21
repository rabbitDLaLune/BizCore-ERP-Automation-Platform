<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\Supplier;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseRequestController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $purchaseRequests = PurchaseRequest::with(['supplier', 'requester', 'approver'])
            ->when($search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('request_no', 'like', "%{$search}%")
                        ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                            $supplierQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('contact_person', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('requester', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('purchase-requests.index', compact(
            'purchaseRequests',
            'search',
            'status'
        ));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 'active')
            ->orderBy('name')
            ->get();

        $products = Product::with('category')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('purchase-requests.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'reason' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.estimated_price' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated) {
            $estimatedTotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                $quantity = (int) $item['quantity'];
                $estimatedPrice = (float) $item['estimated_price'];
                $lineTotal = $quantity * $estimatedPrice;

                $estimatedTotal += $lineTotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'estimated_price' => $estimatedPrice,
                    'total' => $lineTotal,
                ];
            }

            $purchaseRequest = PurchaseRequest::create([
                'request_no' => $this->generateRequestNo(),
                'requested_by' => Auth::id(),
                'supplier_id' => $validated['supplier_id'],
                'estimated_total' => $estimatedTotal,
                'status' => 'pending',
                'reason' => $validated['reason'] ?? null,
                'approved_by' => null,
                'approved_at' => null,
                'rejection_reason' => null,
            ]);

            foreach ($itemsData as $itemData) {
                PurchaseRequestItem::create([
                    'purchase_request_id' => $purchaseRequest->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'estimated_price' => $itemData['estimated_price'],
                    'total' => $itemData['total'],
                ]);
            }

            AuditLogService::record(
                'Purchase Requests',
                'created',
                'Created purchase request: ' . $purchaseRequest->request_no,
                $purchaseRequest,
                null,
                $purchaseRequest->toArray()
            );
        });

        return redirect()
            ->route('purchase-requests.index')
            ->with('success', 'Purchase request created successfully.');
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->load([
            'supplier',
            'requester',
            'approver',
            'items.product.category',
        ]);

        return view('purchase-requests.show', compact('purchaseRequest'));
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'pending') {
            return redirect()
                ->route('purchase-requests.show', $purchaseRequest)
                ->with('error', 'Only pending purchase requests can be edited.');
        }

        $suppliers = Supplier::where('status', 'active')
            ->orderBy('name')
            ->get();

        $products = Product::with('category')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $purchaseRequest->load('items.product');

        return view('purchase-requests.edit', compact(
            'purchaseRequest',
            'suppliers',
            'products'
        ));
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'pending') {
            return redirect()
                ->route('purchase-requests.show', $purchaseRequest)
                ->with('error', 'Only pending purchase requests can be updated.');
        }

        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'reason' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.estimated_price' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated, $purchaseRequest) {
            $oldValues = $purchaseRequest->toArray();

            $estimatedTotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                $quantity = (int) $item['quantity'];
                $estimatedPrice = (float) $item['estimated_price'];
                $lineTotal = $quantity * $estimatedPrice;

                $estimatedTotal += $lineTotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'estimated_price' => $estimatedPrice,
                    'total' => $lineTotal,
                ];
            }

            $purchaseRequest->update([
                'supplier_id' => $validated['supplier_id'],
                'estimated_total' => $estimatedTotal,
                'reason' => $validated['reason'] ?? null,
            ]);

            $purchaseRequest->items()->delete();

            foreach ($itemsData as $itemData) {
                PurchaseRequestItem::create([
                    'purchase_request_id' => $purchaseRequest->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'estimated_price' => $itemData['estimated_price'],
                    'total' => $itemData['total'],
                ]);
            }

            AuditLogService::record(
                'Purchase Requests',
                'updated',
                'Updated purchase request: ' . $purchaseRequest->request_no,
                $purchaseRequest,
                $oldValues,
                $purchaseRequest->fresh()->toArray()
            );
        });

        return redirect()
            ->route('purchase-requests.show', $purchaseRequest)
            ->with('success', 'Purchase request updated successfully.');
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'pending') {
            return redirect()
                ->route('purchase-requests.index')
                ->with('error', 'Only pending purchase requests can be deleted.');
        }

        $requestNo = $purchaseRequest->request_no;
        $oldValues = $purchaseRequest->toArray();

        $purchaseRequest->delete();

        AuditLogService::record(
            'Purchase Requests',
            'deleted',
            'Deleted purchase request: ' . $requestNo,
            null,
            $oldValues,
            null
        );

        return redirect()
            ->route('purchase-requests.index')
            ->with('success', 'Purchase request deleted successfully.');
    }

    public function approve(PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'pending') {
            return redirect()
                ->route('purchase-requests.show', $purchaseRequest)
                ->with('error', 'Only pending purchase requests can be approved.');
        }

        $oldValues = $purchaseRequest->toArray();

        $purchaseRequest->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        AuditLogService::record(
            'Purchase Requests',
            'approved',
            'Approved purchase request: ' . $purchaseRequest->request_no,
            $purchaseRequest,
            $oldValues,
            $purchaseRequest->fresh()->toArray()
        );

        return redirect()
            ->route('purchase-requests.show', $purchaseRequest)
            ->with('success', 'Purchase request approved successfully.');
    }

    public function reject(Request $request, PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'pending') {
            return redirect()
                ->route('purchase-requests.show', $purchaseRequest)
                ->with('error', 'Only pending purchase requests can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $oldValues = $purchaseRequest->toArray();

        $purchaseRequest->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        AuditLogService::record(
            'Purchase Requests',
            'rejected',
            'Rejected purchase request: ' . $purchaseRequest->request_no,
            $purchaseRequest,
            $oldValues,
            $purchaseRequest->fresh()->toArray()
        );

        return redirect()
            ->route('purchase-requests.show', $purchaseRequest)
            ->with('success', 'Purchase request rejected successfully.');
    }

    public function markCompleted(PurchaseRequest $purchaseRequest)
    {
        if ($purchaseRequest->status !== 'approved') {
            return redirect()
                ->route('purchase-requests.show', $purchaseRequest)
                ->with('error', 'Only approved purchase requests can be marked as completed.');
        }

        $oldValues = $purchaseRequest->toArray();

        $purchaseRequest->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        AuditLogService::record(
            'Purchase Requests',
            'completed',
            'Completed purchase request: ' . $purchaseRequest->request_no,
            $purchaseRequest,
            $oldValues,
            $purchaseRequest->fresh()->toArray()
        );

        return redirect()
            ->route('purchase-requests.show', $purchaseRequest)
            ->with('success', 'Purchase request marked as completed.');
    }

    private function generateRequestNo(): string
    {
        $prefix = 'PR-' . now()->format('Ymd') . '-';

        $latestRequest = PurchaseRequest::where('request_no', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        if (! $latestRequest) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($latestRequest->request_no, -4);
        $nextNumber = $lastNumber + 1;

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
