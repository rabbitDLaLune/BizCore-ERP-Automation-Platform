<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Purchase Request Report
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Review procurement requests, approval status, and supplier filtering.
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('reports.purchase-requests.pdf', request()->query()) }}"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    Export PDF
                </a>

                <a href="{{ route('reports.index') }}"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Total Requests</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $totalRequests }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Pending</p>
                    <p class="mt-2 text-3xl font-bold text-yellow-600">
                        {{ $pendingRequests }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Approved</p>
                    <p class="mt-2 text-3xl font-bold text-green-600">
                        {{ $approvedRequests }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Estimated Total</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        RM {{ number_format($estimatedTotal, 2) }}
                    </p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        Request Status Overview
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Purchase requests grouped by approval status.
                    </p>
                </div>

                <div class="mx-auto mt-6 max-w-md">
                    <canvas id="purchaseRequestChart"></canvas>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('reports.purchase-requests') }}"
                    class="grid grid-cols-1 gap-3 md:grid-cols-5">

                    <input type="date" name="date_from" value="{{ $dateFrom }}"
                        class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    <input type="date" name="date_to" value="{{ $dateTo }}"
                        class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    <select name="status"
                        class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Status</option>
                        <option value="pending" @selected($status === 'pending')>
                            Pending
                        </option>
                        <option value="approved" @selected($status === 'approved')>
                            Approved
                        </option>
                        <option value="rejected" @selected($status === 'rejected')>
                            Rejected
                        </option>
                        <option value="completed" @selected($status === 'completed')>
                            Completed
                        </option>
                    </select>

                    <select name="supplier_id"
                        class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Suppliers</option>

                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected((string) $supplierId === (string) $supplier->id)>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="w-full rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">
                            Filter
                        </button>

                        <a href="{{ route('reports.purchase-requests') }}"
                            class="w-full rounded-lg border border-gray-300 px-5 py-2.5 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Request No
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Supplier
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Requester
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                                    Estimated Total
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($purchaseRequests as $purchaseRequest)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                        <a href="{{ route('purchase-requests.show', $purchaseRequest) }}"
                                            class="hover:text-indigo-600">
                                            {{ $purchaseRequest->request_no }}
                                        </a>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $purchaseRequest->supplier->name ?? '-' }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $purchaseRequest->requester->name ?? '-' }}
                                    </td>

                                    <td class="px-6 py-4 text-sm">
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
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $purchaseRequest->created_at->format('d M Y, h:i A') }}
                                    </td>

                                    <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                        RM {{ number_format($purchaseRequest->estimated_total, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                                        No purchase request records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $purchaseRequests->links() }}
                </div>
            </div>

        </div>
    </div>

    <script>
        window.purchaseRequestReportChartData = {
            labels: @json($purchaseChartLabels),
            values: @json($purchaseChartValues),
        };
    </script>
</x-app-layout>
