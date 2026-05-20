<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Sales Invoices
            </h2>

            <a href="{{ route('sales.create') }}"
                class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                Create Invoice
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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

            <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('sales.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-3">
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Search invoice or customer..."
                        class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    <select name="payment_status"
                        class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Payment Status</option>
                        <option value="unpaid" @selected($paymentStatus === 'unpaid')>Unpaid</option>
                        <option value="partial" @selected($paymentStatus === 'partial')>Partial</option>
                        <option value="paid" @selected($paymentStatus === 'paid')>Paid</option>
                    </select>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="w-full rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">
                            Filter
                        </button>

                        <a href="{{ route('sales.index') }}"
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
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Invoice No</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Customer</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Total</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Payment</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Created By</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Date</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($sales as $sale)
                                <tr class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-gray-900">
                                        {{ $sale->invoice_no }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{ $sale->customer->name ?? 'Walk-in Customer' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-gray-900">
                                        RM {{ number_format($sale->total, 2) }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
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

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{ $sale->user->name ?? '-' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{ $sale->created_at->format('d M Y, h:i A') }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <a href="{{ route('sales.show', $sale) }}"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                        No sales invoices found.
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
</x-app-layout>
