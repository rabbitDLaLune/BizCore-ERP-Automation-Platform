<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Sales Invoice
            </h2>

            <a href="{{ route('sales.index') }}"
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

            <form method="POST" action="{{ route('sales.store') }}" class="space-y-6">
                @csrf

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Customer Information
                    </h3>

                    <div class="mt-5 grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="customer_id" class="block text-sm font-medium text-gray-700">
                                Customer
                            </label>

                            <select id="customer_id" name="customer_id"
                                class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Walk-in Customer</option>

                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>
                                        {{ $customer->name }} {{ $customer->phone ? '— ' . $customer->phone : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="payment_status" class="block text-sm font-medium text-gray-700">
                                Payment Status
                            </label>

                            <select id="payment_status" name="payment_status"
                                class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="unpaid" @selected(old('payment_status', 'unpaid') === 'unpaid')>Unpaid</option>
                                <option value="partial" @selected(old('payment_status') === 'partial')>Partial</option>
                                <option value="paid" @selected(old('payment_status') === 'paid')>Paid</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Invoice Items
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
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Stock
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Unit
                                        Price</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                        Quantity</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Line
                                        Total</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                                        Action</th>
                                </tr>
                            </thead>

                            <tbody id="items-table-body" class="divide-y divide-gray-200 bg-white">
                                <tr>
                                    <td class="px-4 py-4">
                                        <select name="items[0][product_id]"
                                            class="product-select w-72 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            onchange="updateRow(this)">
                                            <option value="">Select product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    data-price="{{ $product->selling_price }}"
                                                    data-stock="{{ $product->quantity }}">
                                                    {{ $product->name }} — {{ $product->sku }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        <span class="stock-text">-</span>
                                    </td>

                                    <td class="px-4 py-4 text-sm text-gray-700">
                                        RM <span class="price-text">0.00</span>
                                    </td>

                                    <td class="px-4 py-4">
                                        <input type="number" name="items[0][quantity]" min="1" value="1"
                                            class="quantity-input w-24 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            oninput="updateRow(this)">
                                    </td>

                                    <td class="px-4 py-4 text-sm font-semibold text-gray-900">
                                        RM <span class="line-total-text">0.00</span>
                                    </td>

                                    <td class="px-4 py-4 text-right">
                                        <button type="button" onclick="removeItemRow(this)"
                                            class="text-sm font-semibold text-red-600 hover:text-red-900">
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm lg:col-span-2">
                        <label for="remarks" class="block text-sm font-medium text-gray-700">
                            Remarks
                        </label>

                        <textarea id="remarks" name="remarks" rows="5" placeholder="Optional invoice remarks..."
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remarks') }}</textarea>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Invoice Summary
                        </h3>

                        <div class="mt-5 space-y-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold text-gray-900">RM <span id="subtotal-text">0.00</span></span>
                            </div>

                            <div>
                                <label for="discount" class="block text-sm font-medium text-gray-700">
                                    Discount
                                </label>

                                <input type="number" id="discount" name="discount" step="0.01" min="0"
                                    value="{{ old('discount', 0) }}"
                                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    oninput="calculateTotals()">
                            </div>

                            <div>
                                <label for="tax" class="block text-sm font-medium text-gray-700">
                                    Tax
                                </label>

                                <input type="number" id="tax" name="tax" step="0.01" min="0"
                                    value="{{ old('tax', 0) }}"
                                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    oninput="calculateTotals()">
                            </div>

                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between text-base">
                                    <span class="font-semibold text-gray-900">Total</span>
                                    <span class="font-bold text-gray-900">RM <span id="total-text">0.00</span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('sales.index') }}"
                        class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>

                    <button type="submit"
                        class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                        Create Invoice
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        let itemIndex = 1;

        function addItemRow() {
            const tbody = document.getElementById('items-table-body');
            const firstRow = tbody.querySelector('tr');
            const newRow = firstRow.cloneNode(true);

            newRow.querySelectorAll('select, input').forEach((input) => {
                input.name = input.name.replace(/\[\d+\]/, `[${itemIndex}]`);

                if (input.tagName === 'SELECT') {
                    input.value = '';
                }

                if (input.type === 'number') {
                    input.value = 1;
                }
            });

            newRow.querySelector('.stock-text').textContent = '-';
            newRow.querySelector('.price-text').textContent = '0.00';
            newRow.querySelector('.line-total-text').textContent = '0.00';

            tbody.appendChild(newRow);
            itemIndex++;
            calculateTotals();
        }

        function removeItemRow(button) {
            const tbody = document.getElementById('items-table-body');

            if (tbody.querySelectorAll('tr').length === 1) {
                alert('At least one item is required.');
                return;
            }

            button.closest('tr').remove();
            calculateTotals();
        }

        function updateRow(element) {
            const row = element.closest('tr');
            const select = row.querySelector('.product-select');
            const selectedOption = select.options[select.selectedIndex];

            const price = parseFloat(selectedOption.dataset.price || 0);
            const stock = selectedOption.dataset.stock || '-';
            const quantityInput = row.querySelector('.quantity-input');
            const quantity = parseInt(quantityInput.value || 0);

            row.querySelector('.stock-text').textContent = stock;
            row.querySelector('.price-text').textContent = price.toFixed(2);
            row.querySelector('.line-total-text').textContent = (price * quantity).toFixed(2);

            calculateTotals();
        }

        function calculateTotals() {
            let subtotal = 0;

            document.querySelectorAll('#items-table-body tr').forEach((row) => {
                const lineTotal = parseFloat(row.querySelector('.line-total-text').textContent || 0);
                subtotal += lineTotal;
            });

            const discount = parseFloat(document.getElementById('discount').value || 0);
            const tax = parseFloat(document.getElementById('tax').value || 0);
            const total = (subtotal - discount) + tax;

            document.getElementById('subtotal-text').textContent = subtotal.toFixed(2);
            document.getElementById('total-text').textContent = total.toFixed(2);
        }

        calculateTotals();
    </script>
</x-app-layout>
