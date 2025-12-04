<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Slots
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6">

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">All Slots</h3>

                    <a href="{{ route('admin.slots.create') }}"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-sm font-semibold">
                        + Add Slot
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm border dark:border-gray-700 rounded-md">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="px-4 py-3 text-left">Sl No.</th>
                                <th class="px-4 py-3 text-left">Venue Name</th>
                             
                                <th class="px-4 py-3 text-left">Slot</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y dark:divide-gray-700">
                            @forelse ($slots as $slot)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                                    <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $slot->venue->name }}</td>
                                   
                                    <td class="px-4 py-3">{{ $slot->formatted_time }}</td>


                                    <td class="px-4 py-3 text-right space-x-2">
                                        <a href="{{ route('admin.slots.edit', $slot->id) }}"
                                            class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md text-xs">
                                            Edit
                                        </a>

                                        <form action="{{ route('admin.slots.destroy', $slot->id) }}" method="POST"
                                            class="inline-block" onsubmit="return confirm('Delete this slot?')">
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
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                        No slots found.
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
