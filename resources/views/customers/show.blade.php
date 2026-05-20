<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Customer Details
            </h2>

            <div class="flex gap-3">
                <a href="{{ route('customers.edit', $customer) }}"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    Edit
                </a>

                <a href="{{ route('customers.index') }}"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="space-y-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Customer Name</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900">
                            {{ $customer->name }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Phone</p>
                            <p class="mt-1 text-gray-700">{{ $customer->phone ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="mt-1 text-gray-700">{{ $customer->email ?? '-' }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Address</p>
                        <p class="mt-1 text-gray-700 whitespace-pre-line">{{ $customer->address ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Status</p>

                        <div class="mt-2">
                            @if ($customer->status === 'active')
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
                        <p class="text-sm font-medium text-gray-500">Total Sales Records</p>
                        <p class="mt-1 text-gray-700">{{ $customer->sales_count }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Created At</p>
                        <p class="mt-1 text-gray-700">
                            {{ $customer->created_at->format('d M Y, h:i A') }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
