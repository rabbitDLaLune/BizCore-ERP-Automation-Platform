<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Reports
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                View business reports for sales, inventory, and purchase requests.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <a href="{{ route('reports.sales') }}"
                    class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm hover:border-indigo-300 hover:shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Sales Report
                    </h3>
                    <p class="mt-2 text-sm text-gray-600">
                        View invoices, sales totals, payment status, and date-based sales performance.
                    </p>
                </a>

                <a href="{{ route('reports.inventory') }}"
                    class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm hover:border-indigo-300 hover:shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Inventory Report
                    </h3>
                    <p class="mt-2 text-sm text-gray-600">
                        View product stock quantity, low-stock items, category filters, and stock value.
                    </p>
                </a>

                <a href="{{ route('reports.purchase-requests') }}"
                    class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm hover:border-indigo-300 hover:shadow-md">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Purchase Request Report
                    </h3>
                    <p class="mt-2 text-sm text-gray-600">
                        View procurement requests, approval status, suppliers, and estimated request totals.
                    </p>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
