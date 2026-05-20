<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Purchase Request Details
            </h2>

            <div class="flex gap-3">
                @if ($purchaseRequest->status === 'pending')
                    <a href="{{ route('purchase-requests.edit', $purchaseRequest) }}"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                        Edit
                    </a>
                @endif

                <a href="{{ route('purchase-requests.index') }}"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col justify-between gap-4 md:flex-row">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ $purchaseRequest->request_no }}
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Created on {{ $purchaseRequest->created_at->format('d M Y, h:i A') }}
                        </p>
                    </div>

                    <div class="text-left md:text-right">
                        @if ($purchaseRequest->status === 'pending')
                            <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                Pending
                            </span>
                        @elseif ($purchaseRequest->status === 'approved')
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                Approved
                            </span>
                        @elseif ($purchaseRequest->status === 'rejected')
                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                Rejected
                            </span>
                        @else
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">
                                Completed
                            </span>
                        @endif

                        <p class="mt-2 text-sm text-gray-500">
                            Requested by {{ $purchaseRequest->requester->name ?? '-' }}
                        </p>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="rounded-xl bg-gray-50 p-4">
                        <p class="text-sm font-semibold text-gray-700">Supplier</p>
                        <p class="mt-2 text-gray-900">{{ $purchaseRequest->supplier->name ?? '-' }}</p>
                        <p class="text-sm text-gray-500">{{ $purchaseRequest->supplier->contact_person ?? '-' }}</p>
                        <p class="text-sm text-gray-500">{{ $purchaseRequest->supplier->phone ?? '-' }}</p>
                    </div>

                    <div class="rounded-xl bg-gray-50 p-4">
                        <p class="text-sm font-semibold text-gray-700">Approval Information</p>

                        <p class="mt-2 text-sm text-gray-700">
                            Approved/Rejected By:
                            <span class="font-semibold">
                                {{ $purchaseRequest->approver->name ?? '-' }}
                            </span>
                        </p>

                        <p class="mt-1 text-sm text-gray-700">
                            Date:
                            <span class="font-semibold">
                                {{ $purchaseRequest->approved_at ? $purchaseRequest->approved_at->format('d M Y, h:i A') : '-' }}
                            </span>
                        </p>
                    </div>
                </div>

                @if ($purchaseRequest->reason)
                    <div class="mt-6 rounded-xl bg-gray-50 p-4">
                        <p class="text-sm font-semibold text-gray-700">Reason</p>
                        <p class="mt-2 text-gray-700 whitespace-pre-line">{{ $purchaseRequest->reason }}</p>
                    </div>
                @endif

                @if ($purchaseRequest->rejection_reason)
                    <div class="mt-6 rounded-xl bg-red-50 p-4">
                        <p class="text-sm font-semibold text-red-700">Rejection Reason</p>
                        <p class="mt-2 text-red-700 whitespace-pre-line">{{ $purchaseRequest->rejection_reason }}</p>
                    </div>
                @endif

                <div class="mt-8 overflow-hidden rounded-xl border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Product
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">Quantity
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">Estimated
                                    Price</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">Total
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($purchaseRequest->items as $item)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="font-medium">
                                            {{ $item->product->name ?? '-' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $item->product->sku ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-right text-sm text-gray-700">
                                        {{ $item->quantity }}
                                    </td>

                                    <td class="px-6 py-4 text-right text-sm text-gray-700">
                                        RM {{ number_format($item->estimated_price, 2) }}
                                    </td>

                                    <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                        RM {{ number_format($item->total, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-8 flex justify-end">
                    <div class="w-full max-w-sm">
                        <div class="flex justify-between border-t border-gray-200 pt-4 text-lg">
                            <span class="font-bold text-gray-900">Estimated Total</span>
                            <span class="font-bold text-gray-900">
                                RM {{ number_format($purchaseRequest->estimated_total, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            @if ($purchaseRequest->status === 'pending')
                <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Approval Action
                    </h3>

                    <div class="mt-5 flex flex-col gap-4 md:flex-row">
                        <form method="POST" action="{{ route('purchase-requests.approve', $purchaseRequest) }}"
                            onsubmit="return confirm('Approve this purchase request?');">
                            @csrf

                            <button type="submit"
                                class="rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-700">
                                Approve Request
                            </button>
                        </form>

                        <form method="POST" action="{{ route('purchase-requests.reject', $purchaseRequest) }}"
                            class="flex flex-1 flex-col gap-3 md:flex-row"
                            onsubmit="return confirm('Reject this purchase request?');">
                            @csrf

                            <input type="text" name="rejection_reason" placeholder="Enter rejection reason..."
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                required>

                            <button type="submit"
                                class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700">
                                Reject
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            @if ($purchaseRequest->status === 'approved')
                <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Completion Action
                    </h3>

                    <p class="mt-2 text-sm text-gray-600">
                        Mark this request as completed after items have been purchased or received.
                    </p>

                    <form method="POST" action="{{ route('purchase-requests.complete', $purchaseRequest) }}"
                        class="mt-4" onsubmit="return confirm('Mark this purchase request as completed?');">
                        @csrf

                        <button type="submit"
                            class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                            Mark as Completed
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
