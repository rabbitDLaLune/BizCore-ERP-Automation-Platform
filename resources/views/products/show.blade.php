<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Product Details
            </h2>

            <div class="flex gap-3">
                <a href="{{ route('products.edit', $product) }}"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    Edit
                </a>

                <a href="{{ route('products.index') }}"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <p class="text-sm font-medium text-gray-500">SKU</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $product->sku }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Product Name</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $product->name }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Category</p>
                        <p class="mt-1 text-gray-700">{{ $product->category->name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Status</p>
                        <div class="mt-2">
                            @if ($product->status === 'active')
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                    Active
                                </span>
                            @else
                                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Cost Price</p>
                        <p class="mt-1 text-gray-700">RM {{ number_format($product->cost_price, 2) }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Selling Price</p>
                        <p class="mt-1 text-gray-700">RM {{ number_format($product->selling_price, 2) }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Quantity</p>
                        <p class="mt-1 text-gray-700">{{ $product->quantity }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Reorder Level</p>
                        <p class="mt-1 text-gray-700">{{ $product->reorder_level }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-sm font-medium text-gray-500">Stock Status</p>
                        <div class="mt-2">
                            @if ($product->isLowStock())
                                <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                    Low Stock
                                </span>
                            @else
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                    Available
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-sm font-medium text-gray-500">Description</p>
                        <p class="mt-1 text-gray-700">{{ $product->description ?? '-' }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
