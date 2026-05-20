<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Product
            </h2>

            <a href="{{ route('products.index') }}"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">
                                Category
                            </label>

                            <select id="category_id" name="category_id"
                                class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select category</option>

                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('category_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-700">
                                SKU
                            </label>

                            <input type="text" id="sku" name="sku" value="{{ old('sku', $product->sku) }}"
                                placeholder="Example: PRD-001"
                                class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                            @error('sku')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Product Name
                        </label>

                        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
                            placeholder="Example: Wireless Mouse"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Description
                        </label>

                        <textarea id="description" name="description" rows="4" placeholder="Write product description..."
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $product->description) }}</textarea>

                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="cost_price" class="block text-sm font-medium text-gray-700">
                                Cost Price
                            </label>

                            <input type="number" id="cost_price" name="cost_price" step="0.01" min="0"
                                value="{{ old('cost_price', $product->cost_price) }}"
                                class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                            @error('cost_price')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="selling_price" class="block text-sm font-medium text-gray-700">
                                Selling Price
                            </label>

                            <input type="number" id="selling_price" name="selling_price" step="0.01" min="0"
                                value="{{ old('selling_price', $product->selling_price) }}"
                                class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                            @error('selling_price')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700">
                                Quantity
                            </label>

                            <input type="number" id="quantity" name="quantity" min="0"
                                value="{{ old('quantity', $product->quantity) }}"
                                class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                            @error('quantity')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="reorder_level" class="block text-sm font-medium text-gray-700">
                                Reorder Level
                            </label>

                            <input type="number" id="reorder_level" name="reorder_level" min="0"
                                value="{{ old('reorder_level', $product->reorder_level) }}"
                                class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                            @error('reorder_level')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">
                            Status
                        </label>

                        <select id="status" name="status"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="active" @selected(old('status', $product->status) === 'active')>
                                Active
                            </option>

                            <option value="inactive" @selected(old('status', $product->status) === 'inactive')>
                                Inactive
                            </option>
                        </select>

                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <p class="text-sm font-medium text-gray-700">
                            Current Stock Status
                        </p>

                        <div class="mt-2">
                            @if ($product->isLowStock())
                                <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                    Low Stock: {{ $product->quantity }} left
                                </span>
                            @else
                                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                    Available: {{ $product->quantity }} in stock
                                </span>
                            @endif
                        </div>

                        <p class="mt-2 text-xs text-gray-500">
                            A product is considered low stock when its quantity is less than or equal to the reorder
                            level.
                        </p>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('products.index') }}"
                            class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>

                        <button type="submit"
                            class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                            Update Product
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
