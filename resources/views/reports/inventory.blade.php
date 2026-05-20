<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Inventory Report
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Review stock quantity, low-stock items, and inventory value.
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('reports.inventory.pdf', request()->query()) }}"
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
                    <p class="text-sm text-gray-500">Total Products</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $totalProducts }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Low Stock</p>
                    <p class="mt-2 text-3xl font-bold text-red-600">
                        {{ $lowStockProducts }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Total Quantity</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ $totalStockQuantity }}
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm text-gray-500">Stock Value</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        RM {{ number_format($totalStockValue, 2) }}
                    </p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        Top Stock Quantity
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Top 10 products by current stock quantity.
                    </p>
                </div>

                <div class="mt-6">
                    <canvas id="inventoryChart" height="100"></canvas>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('reports.inventory') }}"
                    class="grid grid-cols-1 gap-3 md:grid-cols-4">

                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Search SKU or product..."
                        class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    <select name="category_id"
                        class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Categories</option>

                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) $categoryId === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="stock_status"
                        class="rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Stock</option>
                        <option value="low_stock" @selected($stockStatus === 'low_stock')>
                            Low Stock
                        </option>
                        <option value="available" @selected($stockStatus === 'available')>
                            Available
                        </option>
                    </select>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="w-full rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">
                            Filter
                        </button>

                        <a href="{{ route('reports.inventory') }}"
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
                                    SKU
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Product
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Category
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                                    Qty
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                                    Reorder
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                                    Cost
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                                    Value
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($products as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                        {{ $product->sku }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $product->name }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $product->category->name ?? '-' }}
                                    </td>

                                    <td
                                        class="px-6 py-4 text-right text-sm font-semibold {{ $product->isLowStock() ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $product->quantity }}
                                    </td>

                                    <td class="px-6 py-4 text-right text-sm text-gray-600">
                                        {{ $product->reorder_level }}
                                    </td>

                                    <td class="px-6 py-4 text-right text-sm text-gray-600">
                                        RM {{ number_format($product->cost_price, 2) }}
                                    </td>

                                    <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                        RM {{ number_format($product->quantity * $product->cost_price, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                        No inventory records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $products->links() }}
                </div>
            </div>

        </div>
    </div>

    <script>
        window.inventoryReportChartData = {
            labels: @json($inventoryChartLabels),
            values: @json($inventoryChartValues),
        };
    </script>
</x-app-layout>
