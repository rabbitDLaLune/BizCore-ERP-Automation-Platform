<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Invoice Details
            </h2>

            <div class="flex gap-3">
                <a href="{{ route('sales.pdf', $sale) }}"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    Download PDF
                </a>

                <a href="{{ route('sales.index') }}"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('error'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col justify-between gap-4 md:flex-row">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ $sale->invoice_no }}
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Created on {{ $sale->created_at->format('d M Y, h:i A') }}
                        </p>
                    </div>

                    <div class="text-left md:text-right">
                        @if ($sale->payment_status === 'paid')
                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                Paid
                            </span>
                        @elseif ($sale->payment_status === 'partial')
                            <span class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700">
                                Partial
                            </span>
                        @else
                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                Unpaid
                            </span>
                        @endif

                        <p class="mt-2 text-sm text-gray-500">
                            Created by {{ $sale->user->name ?? '-' }}
                        </p>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="rounded-xl bg-gray-50 p-4">
                        <p class="text-sm font-semibold text-gray-700">
                            Customer
                        </p>

                        <p class="mt-2 text-gray-900">
                            {{ $sale->customer->name ?? 'Walk-in Customer' }}
                        </p>

                        @if ($sale->customer)
                            <p class="text-sm text-gray-500">{{ $sale->customer->phone ?? '-' }}</p>
                            <p class="text-sm text-gray-500">{{ $sale->customer->email ?? '-' }}</p>
                        @endif
                    </div>

                    <div class="rounded-xl bg-gray-50 p-4">
                        <p class="text-sm font-semibold text-gray-700">
                            Remarks
                        </p>

                        <p class="mt-2 text-gray-700 whitespace-pre-line">
                            {{ $sale->remarks ?? '-' }}
                        </p>
                    </div>
                </div>

                <div class="mt-8 overflow-hidden rounded-xl border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Product
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Unit Price
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Quantity
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">Total
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($sale->items as $item)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="font-medium">
                                            {{ $item->product->name ?? '-' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $item->product->sku ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        RM {{ number_format($item->unit_price, 2) }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $item->quantity }}
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
                    <div class="w-full max-w-sm space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-semibold text-gray-900">
                                RM {{ number_format($sale->subtotal, 2) }}
                            </span>
                        </div>

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Discount</span>
                            <span class="font-semibold text-gray-900">
                                RM {{ number_format($sale->discount, 2) }}
                            </span>
                        </div>

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax</span>
                            <span class="font-semibold text-gray-900">
                                RM {{ number_format($sale->tax, 2) }}
                            </span>
                        </div>

                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between text-lg">
                                <span class="font-bold text-gray-900">Total</span>
                                <span class="font-bold text-gray-900">
                                    RM {{ number_format($sale->total, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
