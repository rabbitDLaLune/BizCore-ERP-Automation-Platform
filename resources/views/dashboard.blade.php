<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                FlowERP Dashboard
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Enterprise Inventory, Sales and Workflow Automation System
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Total Products</p>
                    <p class="mt-3 text-3xl font-bold text-gray-900">{{ $totalProducts }}</p>
                    <p class="mt-2 text-xs text-gray-500">Products registered in inventory</p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Low Stock Items</p>
                    <p class="mt-3 text-3xl font-bold {{ $lowStockProducts > 0 ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $lowStockProducts }}
                    </p>
                    <p class="mt-2 text-xs text-gray-500">Products below reorder level</p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Total Sales</p>
                    <p class="mt-3 text-3xl font-bold text-gray-900">
                        RM {{ number_format($totalSalesAmount, 2) }}
                    </p>
                    <p class="mt-2 text-xs text-gray-500">{{ $totalSales }} sales invoice(s)</p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Today Sales</p>
                    <p class="mt-3 text-3xl font-bold text-gray-900">
                        RM {{ number_format($todaySalesAmount, 2) }}
                    </p>
                    <p class="mt-2 text-xs text-gray-500">Sales generated today</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Customers</p>
                    <p class="mt-3 text-3xl font-bold text-gray-900">{{ $totalCustomers }}</p>
                    <a href="{{ route('customers.index') }}"
                        class="mt-2 inline-block text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                        View customers
                    </a>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Suppliers</p>
                    <p class="mt-3 text-3xl font-bold text-gray-900">{{ $totalSuppliers }}</p>
                    <a href="{{ route('suppliers.index') }}"
                        class="mt-2 inline-block text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                        View suppliers
                    </a>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Pending Requests</p>
                    <p
                        class="mt-3 text-3xl font-bold {{ $pendingPurchaseRequests > 0 ? 'text-yellow-600' : 'text-gray-900' }}">
                        {{ $pendingPurchaseRequests }}
                    </p>
                    <a href="{{ route('purchase-requests.index', ['status' => 'pending']) }}"
                        class="mt-2 inline-block text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                        Review requests
                    </a>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Approved Requests</p>
                    <p class="mt-3 text-3xl font-bold text-gray-900">{{ $approvedPurchaseRequests }}</p>
                    <a href="{{ route('purchase-requests.index', ['status' => 'approved']) }}"
                        class="mt-2 inline-block text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                        View approved
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Recent Sales
                        </h3>

                        <a href="{{ route('sales.index') }}"
                            class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                            View all
                        </a>
                    </div>

                    <div class="mt-5 overflow-hidden rounded-xl border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        Invoice</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        Customer</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        Payment</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500">Total
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($recentSales as $sale)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                                            <a href="{{ route('sales.show', $sale) }}" class="hover:text-indigo-600">
                                                {{ $sale->invoice_no }}
                                            </a>
                                        </td>

                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            {{ $sale->customer->name ?? 'Walk-in Customer' }}
                                        </td>

                                        <td class="px-4 py-3 text-sm">
                                            @if ($sale->payment_status === 'paid')
                                                <span
                                                    class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                                    Paid
                                                </span>
                                            @elseif ($sale->payment_status === 'partial')
                                                <span
                                                    class="rounded-full bg-yellow-100 px-2.5 py-1 text-xs font-semibold text-yellow-700">
                                                    Partial
                                                </span>
                                            @else
                                                <span
                                                    class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                                                    Unpaid
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">
                                            RM {{ number_format($sale->total, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500">
                                            No sales yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            System Summary
                        </h3>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <span class="text-sm text-gray-600">Users</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $totalUsers }}</span>
                        </div>

                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <span class="text-sm text-gray-600">Categories</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $totalCategories }}</span>
                        </div>

                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <span class="text-sm text-gray-600">Products</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $totalProducts }}</span>
                        </div>

                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <span class="text-sm text-gray-600">Customers</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $totalCustomers }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Suppliers</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $totalSuppliers }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Recent Stock Movements
                        </h3>

                        <a href="{{ route('stock-movements.index') }}"
                            class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                            View all
                        </a>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse ($recentStockMovements as $movement)
                            <div class="flex items-center justify-between rounded-xl bg-gray-50 p-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $movement->product->name ?? '-' }}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ $movement->reference_no ?? 'No reference' }}
                                        · {{ $movement->created_at->format('d M Y, h:i A') }}
                                    </p>
                                </div>

                                <div class="text-right">
                                    @if ($movement->type === 'stock_in')
                                        <span
                                            class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                            +{{ $movement->quantity }}
                                        </span>
                                    @else
                                        <span
                                            class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                            -{{ $movement->quantity }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No stock movements yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Recent Purchase Requests
                        </h3>

                        <a href="{{ route('purchase-requests.index') }}"
                            class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                            View all
                        </a>
                    </div>

                    <div class="mt-5 space-y-4">
                        @forelse ($recentPurchaseRequests as $purchaseRequest)
                            <div class="rounded-xl bg-gray-50 p-4">
                                <div class="flex items-center justify-between">
                                    <a href="{{ route('purchase-requests.show', $purchaseRequest) }}"
                                        class="text-sm font-semibold text-gray-900 hover:text-indigo-600">
                                        {{ $purchaseRequest->request_no }}
                                    </a>

                                    @if ($purchaseRequest->status === 'pending')
                                        <span
                                            class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                            Pending
                                        </span>
                                    @elseif ($purchaseRequest->status === 'approved')
                                        <span
                                            class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                            Approved
                                        </span>
                                    @elseif ($purchaseRequest->status === 'rejected')
                                        <span
                                            class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                            Rejected
                                        </span>
                                    @else
                                        <span
                                            class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                            Completed
                                        </span>
                                    @endif
                                </div>

                                <p class="mt-2 text-xs text-gray-500">
                                    {{ $purchaseRequest->supplier->name ?? '-' }}
                                    · RM {{ number_format($purchaseRequest->estimated_total, 2) }}
                                </p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No purchase requests yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
