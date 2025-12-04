<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Venues
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Success --}}
            @if (session('success'))
                <div class="mb-4 p-4 text-green-800 bg-green-100 dark:bg-green-900 dark:text-green-200 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error --}}
            @if ($errors->any())
                <div class="mb-4 p-4 text-red-800 bg-red-100 dark:bg-red-900 dark:text-red-200 rounded-lg">
                    {{ $errors->first() }}
                </div>
            @endif


            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200">All Venues</h3>

                    <a href="{{ route('admin.venues.create') }}"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700">
                        + Add Venue
                    </a>
                </div>


                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                <th class="px-4 py-3 text-left">Sl No.</th>
                                <th class="px-4 py-3 text-left">Name</th>
                                <th class="px-4 py-3 text-left">Location</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y dark:divide-gray-700">
                            @forelse($venues as $venue)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $venue->name }}</td>
                                    <td class="px-4 py-3">{{ $venue->location ?? 'N/A' }}</td>

                                    <td class="px-4 py-3 text-right space-x-2">

                                        <a href="{{ route('admin.venues.edit', $venue->id) }}"
                                            class="px-3 py-1 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 text-xs">
                                            Edit
                                        </a>

                                        <form action="{{ route('admin.venues.destroy', $venue->id) }}"
                                              method="POST"
                                              class="inline-block"
                                              onsubmit="return confirm('Delete this venue?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 text-xs">
                                                Delete
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                        No venues found.
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
