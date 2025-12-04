<x-app-layout>

    {{-- Page Header --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Movie Assignments
        </h2>
    </x-slot>


    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- SUCCESS MESSAGE --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-md">
                    {{ session('success') }}
                </div>
            @endif


            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6" x-data="{ search: '' }">

                {{-- Top Bar --}}
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-4">

                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                        All Movie Schedules
                    </h3>

                    {{-- Live Search --}}
                    <div class="flex items-center">
                        <input type="text"
                               x-model="search"
                               placeholder="Search..."
                               class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-700
                                      dark:bg-gray-900 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>

                    {{-- Create Button --}}
                    <a href="{{ route('admin.assignments.create') }}"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm font-semibold">
                        + Assign Movie
                    </a>

                </div>


                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border dark:border-gray-700 rounded-md">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="px-4 py-3 text-left">#</th>
                                <th class="px-4 py-3 text-left">Venue</th>
                                <th class="px-4 py-3 text-left">Screen</th>
                                <th class="px-4 py-3 text-left">Slot</th>
                                <th class="px-4 py-3 text-left">Movie</th>
                                <th class="px-4 py-3 text-left">Day</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y dark:divide-gray-700">

                            @forelse ($assignments as $item)

                                {{-- 🔥 Dynamic Filtering with Alpine --}}
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition"
                                    x-show="
                                        search === '' ||
                                        '{{ strtolower($item->venue->name) }}'.includes(search.toLowerCase()) ||
                                        '{{ strtolower($item->screen->name) }}'.includes(search.toLowerCase()) ||
                                        '{{ strtolower($item->slot->formatted_time ?? '') }}'.includes(search.toLowerCase()) ||
                                        '{{ strtolower($item->movie) }}'.includes(search.toLowerCase()) ||
                                        'day {{ strtolower($item->day) }}'.includes(search.toLowerCase()) ||
                                        '{{ strtolower($item->status) }}'.includes(search.toLowerCase())
                                    "
                                >

                                    {{-- Index --}}
                                    <td class="px-4 py-3">{{ $loop->iteration }}</td>

                                    {{-- Venue --}}
                                    <td class="px-4 py-3">{{ $item->venue->name }}</td>

                                    {{-- Screen --}}
                                    <td class="px-4 py-3">{{ $item->screen->name }}</td>

                                    {{-- Slot --}}
                                    <td class="px-4 py-3">
                                        {{ $item->slot->formatted_time ?? 'N/A' }}
                                    </td>

                                    {{-- Movie --}}
                                    <td class="px-4 py-3 font-semibold">{{ $item->movie }}</td>

                                    {{-- Day --}}
                                    <td class="px-4 py-3">Day {{ $item->day }}</td>

                                    {{-- Status --}}
                                    <td class="px-4 py-3">
                                        <span class="
                                            px-2 py-1 rounded text-xs
                                            {{ $item->status === 'active'
                                                ? 'bg-green-100 text-green-700'
                                                : 'bg-gray-300 text-gray-700' }}
                                        ">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-4 py-3 text-right space-x-2">

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.assignments.edit', $item->id) }}"
                                            class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md text-xs">
                                            Edit
                                        </a>

                                        {{-- Delete --}}
                                        <form action="{{ route('admin.assignments.destroy', $item->id) }}"
                                              method="POST"
                                              class="inline-block"
                                              onsubmit="return confirm('Delete this assignment?')">
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
                                    <td colspan="8" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                        No assignments found.
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
