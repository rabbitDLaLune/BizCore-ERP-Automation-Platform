<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Supplier Details
            </h2>

            <div class="flex gap-3">
                <a href="{{ route('suppliers.edit', $supplier) }}"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                    Edit
                </a>

                <a href="{{ route('suppliers.index') }}"
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
                        <p class="text-sm font-medium text-gray-500">Supplier Name</p>
                        <p class="mt-1 text-lg font-semibold text-gray-900">
                            {{ $supplier->name }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Contact Person</p>
                        <p class="mt-1 text-gray-700">
                            {{ $supplier->contact_person ?? '-' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Phone</p>
                            <p class="mt-1 text-gray-700">{{ $supplier->phone ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="mt-1 text-gray-700">{{ $supplier->email ?? '-' }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Address</p>
                        <p class="mt-1 text-gray-700 whitespace-pre-line">
                            {{ $supplier->address ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-500">Status</p>

                        <div class="mt-2">
                            @if ($supplier->status === 'active')
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
                        <p class="text-sm font-medium text-gray-500">Created At</p>
                        <p class="mt-1 text-gray-700">
                            {{ $supplier->created_at->format('d M Y, h:i A') }}
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
