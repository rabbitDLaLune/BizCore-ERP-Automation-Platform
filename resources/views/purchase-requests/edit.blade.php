<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Purchase Request
            </h2>

            <a href="{{ route('purchase-requests.show', $purchaseRequest) }}"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <p class="font-semibold">Please fix the following errors:</p>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('purchase-requests.update', $purchaseRequest) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Supplier Information
                    </h3>

                    <div class="mt-5">
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700">
                            Supplier
                        </label>

                        <select id="supplier_id" name="supplier_id"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select supplier</option>

                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected(old('supplier_id', $purchaseRequest->supplier_id) == $supplier->id)>
                                    {{ $supplier->name }}
                                    {{ $supplier->contact_person ? ' — ' . $supplier->contact_person : '' }}
                                </option>
                            @endforeach
                        </select>

                        @error('supplier_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Requested Items
                        </h3>

                        <button type="button" onclick="addItemRow()"
                            class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
                            Add Item
                        </button>
                    </div>

                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        Product</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        Current Stock</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        Quantity</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        Estimated Price</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Line
                                        Total</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                                        Action</th>
                                </tr>
                            </thead>

                            <tbody id="items-table-body" class="divide-y divide-gray-200 bg-white">
                                @foreach ($purchaseRequest->items as $index => $item)
                                    <tr>
                                        <td class="px-4 py-4">
                                            <select name="items[{{ $index }}][product_id]"
                                                class="product-select w-72 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                onchange="updateRow(this)">
                                                <option value="">Select product</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}"
                                                        data-stock="{{ $product->quantity }}"
                                                        data-cost="{{ $product->cost_price }}"
                                                        @selected(old("items.$index.product_id", $item->product_id) == $product->id)>
                                                        {{ $product->name }} — {{ $product->sku }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td class="px-4 py-4 text-sm text-gray-700">
                                            <span class="stock-text">{{ $item->product->quantity ?? '-' }}</span>
                                        </td>

                                        <td class="px-4 py-4">
                                            <input type="number" name="items[{{ $index }}][quantity]"
                                                min="1"
                                                value="{{ old("items.$index.quantity", $item->quantity) }}"
                                                class="quantity-input w-24 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                oninput="updateRow(this)">
                                        </td>

                                        <td class="px-4 py-4">
                                            <input type="number" name="items[{{ $index }}][estimated_price]"
                                                min="0" step="0.01"
                                                value="{{ old("items.$index.estimated_price", $item->estimated_price) }}"
                                                class="price-input w-32 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                oninput="updateRow(this)">
                                        </td>

                                        <td class="px-4 py-4 text-sm font-semibold text-gray-900">
                                            RM <span
                                                class="line-total-text">{{ number_format($item->total, 2, '.', '') }}</span>
                                        </td>

                                        <td class="px-4 py-4 text-right">
                                            <button type="button" onclick="removeItemRow(this)"
                                                class="text-sm font-semibold text-red-600 hover:text-red-900">
                                                Remove
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">
                        <label for="reason" class="block text-sm font-medium text-gray-700">
                            Reason
                        </label>

                        <textarea id="reason" name="reason" rows="5"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('reason', $purchaseRequest->reason) }}</textarea>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Request Summary
                        </h3>

                        <div class="mt-5">
                            <div class="flex justify-between text-base">
                                <span class="font-semibold text-gray-900">Estimated Total</span>
                                <span class="font-bold text-gray-900">
                                    RM <span
                                        id="estimated-total-text">{{ number_format($purchaseRequest->estimated_total, 2) }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('purchase-requests.show', $purchaseRequest) }}"
                        class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>

                    <button type="submit"
                        class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                        Update Request
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        let itemIndex = {{ $purchaseRequest->items->count() }};

        function addItemRow() {
            const tbody = document.getElementById('items-table-body');
            const firstRow = tbody.querySelector('tr');
            const newRow = firstRow.cloneNode(true);

            newRow.querySelectorAll('select, input').forEach((input) => {
                input.name = input.name.replace(/\[\d+\]/, `[${itemIndex}]`);

                if (input.tagName === 'SELECT') {
                    input.value = '';
                }

                if (input.classList.contains('quantity-input')) {
                    input.value = 1;
                }

                if (input.classList.contains('price-input')) {
                    input.value = 0;
                }
            });

            newRow.querySelector('.stock-text').textContent = '-';
            newRow.querySelector('.line-total-text').textContent = '0.00';

            tbody.appendChild(newRow);
            itemIndex++;
            calculateTotal();
        }

        function removeItemRow(button) {
            const tbody = document.getElementById('items-table-body');

            if (tbody.querySelectorAll('tr').length === 1) {
                alert('At least one item is required.');
                return;
            }

            button.closest('tr').remove();
            calculateTotal();
        }

        function updateRow(element) {
            const row = element.closest('tr');
            const select = row.querySelector('.product-select');
            const selectedOption = select.options[select.selectedIndex];

            const stock = selectedOption.dataset.stock || '-';
            const cost = parseFloat(selectedOption.dataset.cost || 0);

            const quantityInput = row.querySelector('.quantity-input');
            const priceInput = row.querySelector('.price-input');

            if (element.classList.contains('product-select')) {
                priceInput.value = cost.toFixed(2);
            }

            const quantity = parseInt(quantityInput.value || 0);
            const price = parseFloat(priceInput.value || 0);
            const lineTotal = quantity * price;

            row.querySelector('.stock-text').textContent = stock;
            row.querySelector('.line-total-text').textContent = lineTotal.toFixed(2);

            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;

            document.querySelectorAll('#items-table-body tr').forEach((row) => {
                const lineTotal = parseFloat(row.querySelector('.line-total-text').textContent || 0);
                total += lineTotal;
            });

            document.getElementById('estimated-total-text').textContent = total.toFixed(2);
        }

        calculateTotal();
    </script>
</x-app-layout>
