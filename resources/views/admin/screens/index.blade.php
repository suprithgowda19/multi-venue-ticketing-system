<x-app-layout>
    {{-- HEADER --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Screens
        </h2>
    </x-slot>


    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- SUCCESS --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif


            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6">

                {{-- TOP BAR --}}
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200">All Screens</h3>

                    <a href="{{ route('admin.screens.create') }}"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm font-semibold">
                        + Add Screen
                    </a>
                </div>

                
                {{-- TABLE --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-md text-sm">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="px-4 py-3 text-left">#</th>
                                <th class="px-4 py-3 text-left">Screen Name</th>
                                <th class="px-4 py-3 text-left">Venue</th>
                                <th class="px-4 py-3 text-left">Capacity</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y dark:divide-gray-700">
                            @forelse ($screens as $screen)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                                    <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $screen->name }}</td>
                                    <td class="px-4 py-3">{{ $screen->venue->name }}</td>
                                    <td class="px-4 py-3">{{ $screen->capacity }}</td>

                                    <td class="px-4 py-3 text-right space-x-2">

                                        {{-- EDIT --}}
                                        <a href="{{ route('admin.screens.edit', $screen->id) }}"
                                            class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md text-xs">
                                            Edit
                                        </a>

                                        {{-- DELETE --}}
                                        <form action="{{ route('admin.screens.destroy', $screen->id) }}"
                                              method="POST"
                                              class="inline-block"
                                              onsubmit="return confirm('Delete this screen?')">

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-md text-xs">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                        No screens added yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
