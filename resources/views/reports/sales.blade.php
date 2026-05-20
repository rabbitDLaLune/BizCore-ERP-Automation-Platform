<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Sales Report
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Filter and review sales invoice performance.
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('reports.sales.pdf', request()->query()) }}"
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
                    <p class="text-sm text-gray-500">Total Invoices</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $totalInvoices }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Total Sales</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        RM {{ number_format($totalSalesAmount, 2) }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Paid Amount</p>
                    <p class="mt-2 text-3xl font-bold text-green-600">
                        RM {{ number_format($paidAmount, 2) }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Unpaid Amount</p>
                    <p class="mt-2 text-3xl font-bold text-red-600">
                        RM {{ number_format($unpaidAmount, 2) }}
                    </p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            Sales Trend
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Total sales amount grouped by invoice date.
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <canvas id="salesChart" height="100"></canvas>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('reports.sales') }}"
                    class="grid grid-cols-1 gap-3 md:grid-cols-4">

                    <input type="date" name="date_from" value="{{ $dateFrom }}"
                        class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    <input type="date" name="date_to" value="{{ $dateTo }}"
                        class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    <select name="payment_status"
                        class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Payment Status</option>
                        <option value="unpaid" @selected($paymentStatus === 'unpaid')>
                            Unpaid
                        </option>
                        <option value="partial" @selected($paymentStatus === 'partial')>
                            Partial
                        </option>
                        <option value="paid" @selected($paymentStatus === 'paid')>
                            Paid
                        </option>
                    </select>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="w-full rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">
                            Filter
                        </button>

                        <a href="{{ route('reports.sales') }}"
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
                                    Invoice
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Customer
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Payment
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                                    Total
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($sales as $sale)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                        <a href="{{ route('sales.show', $sale) }}" class="hover:text-indigo-600">
                                            {{ $sale->invoice_no }}
                                        </a>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $sale->customer->name ?? 'Walk-in Customer' }}
                                    </td>

                                    <td class="px-6 py-4 text-sm">
                                        @if ($sale->payment_status === 'paid')
                                            <span
                                                class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                                Paid
                                            </span>
                                        @elseif ($sale->payment_status === 'partial')
                                            <span
                                                class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                                Partial
                                            </span>
                                        @else
                                            <span
                                                class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                                Unpaid
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $sale->created_at->format('d M Y, h:i A') }}
                                    </td>

                                    <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                        RM {{ number_format($sale->total, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                        No sales records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $sales->links() }}
                </div>
            </div>

        </div>
    </div>

    <script>
        window.salesReportChartData = {
            labels: @json($salesChartLabels),
            values: @json($salesChartValues),
        };
    </script>
</x-app-layout>
