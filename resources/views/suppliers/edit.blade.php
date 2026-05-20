<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Supplier
            </h2>

            <a href="{{ route('suppliers.index') }}"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Supplier Name
                        </label>

                        <input type="text" id="name" name="name" value="{{ old('name', $supplier->name) }}"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_person" class="block text-sm font-medium text-gray-700">
                            Contact Person
                        </label>

                        <input type="text" id="contact_person" name="contact_person"
                            value="{{ old('contact_person', $supplier->contact_person) }}"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                        @error('contact_person')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">
                                Phone
                            </label>

                            <input type="text" id="phone" name="phone"
                                value="{{ old('phone', $supplier->phone) }}"
                                class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                            @error('phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email
                            </label>

                            <input type="email" id="email" name="email"
                                value="{{ old('email', $supplier->email) }}"
                                class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">
                            Address
                        </label>

                        <textarea id="address" name="address" rows="4"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $supplier->address) }}</textarea>

                        @error('address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">
                            Status
                        </label>

                        <select id="status" name="status"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="active" @selected(old('status', $supplier->status) === 'active')>Active</option>
                            <option value="inactive" @selected(old('status', $supplier->status) === 'inactive')>Inactive</option>
                        </select>

                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('suppliers.index') }}"
                            class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>

                        <button type="submit"
                            class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                            Update Supplier
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
