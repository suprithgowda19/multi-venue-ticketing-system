<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Add Venue
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">

                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-6">
                    Create New Venue
                </h3>

                <form action="{{ route('admin.venues.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Venue Name --}}
                    <div>
                        <label class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Venue Name</label>
                        <input
                            type="text"
                            name="name"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"
                            value="{{ old('name') }}"
                            required
                        >
                        @error('name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Location --}}
                    <div>
                        <label class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                        <input
                            type="text"
                            name="location"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"
                            value="{{ old('location') }}"
                        >
                        @error('location')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <button
                            class="px-5 py-2 bg-indigo-600 text-white rounded-md font-semibold hover:bg-indigo-700">
                            Create Venue
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>
