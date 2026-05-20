<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Categories
            </h2>

            <a href="{{ route('categories.create') }}"
               class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                Add Category
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('categories.index') }}" class="flex flex-col gap-3 md:flex-row">
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Search category name or description..."
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    <button type="submit"
                            class="rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">
                        Search
                    </button>

                    <a href="{{ route('categories.index') }}"
                       class="rounded-lg border border-gray-300 px-5 py-2.5 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Reset
                    </a>
                </form>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Description
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Actions
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($categories as $category)
                                <tr class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ $category->name }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $category->description ?? '-' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm">
                                        @if ($category->status === 'active')
                                            <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                                Active
                                            </span>
                                        @else
                                            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('categories.show', $category) }}"
                                               class="text-gray-600 hover:text-gray-900">
                                                View
                                            </a>

                                            <a href="{{ route('categories.edit', $category) }}"
                                               class="text-indigo-600 hover:text-indigo-900">
                                                Edit
                                            </a>

                                            <form method="POST"
                                                  action="{{ route('categories.destroy', $category) }}"
                                                  onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-900">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">
                                        No categories found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $categories->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
