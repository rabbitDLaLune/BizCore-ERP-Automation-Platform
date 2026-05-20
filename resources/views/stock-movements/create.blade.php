<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Record Stock Movement
            </h2>

            <a href="{{ route('stock-movements.index') }}"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('stock-movements.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="product_id" class="block text-sm font-medium text-gray-700">
                            Product
                        </label>

                        <select id="product_id" name="product_id"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select product</option>

                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>
                                    {{ $product->name }} — {{ $product->sku }} — Current Stock: {{ $product->quantity }}
                                </option>
                            @endforeach
                        </select>

                        @error('product_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">
                                Movement Type
                            </label>

                            <select id="type" name="type"
                                class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select type</option>
                                <option value="stock_in" @selected(old('type') === 'stock_in')>Stock In</option>
                                <option value="stock_out" @selected(old('type') === 'stock_out')>Stock Out</option>
                            </select>

                            @error('type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700">
                                Quantity
                            </label>

                            <input type="number" id="quantity" name="quantity" min="1"
                                value="{{ old('quantity', 1) }}"
                                class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                            @error('quantity')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="reference_no" class="block text-sm font-medium text-gray-700">
                            Reference Number
                        </label>

                        <input type="text" id="reference_no" name="reference_no" value="{{ old('reference_no') }}"
                            placeholder="Example: GRN-001, ADJ-001, INV-001"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                        @error('reference_no')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="remarks" class="block text-sm font-medium text-gray-700">
                            Remarks
                        </label>

                        <textarea id="remarks" name="remarks" rows="4" placeholder="Write stock movement remarks..."
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remarks') }}</textarea>

                        @error('remarks')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                        <p class="text-sm font-semibold text-blue-800">
                            Stock Movement Rule
                        </p>
                        <p class="mt-1 text-sm text-blue-700">
                            Stock In will increase product quantity. Stock Out will reduce product quantity.
                            The system will prevent stock out if the quantity is greater than current stock.
                        </p>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('stock-movements.index') }}"
                            class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>

                        <button type="submit"
                            class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                            Save Movement
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
