<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Screen
        </h2>
    </x-slot>


    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6">

                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-6">
                    Update Screen
                </h3>

                <form action="{{ route('admin.screens.update', $screen->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')


                    {{-- Venue --}}
                    <div>
                        <label class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Venue</label>

                        <select name="venue_id"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"
                            required>

                            @foreach($venues as $venue)
                                <option value="{{ $venue->id }}" 
                                    {{ old('venue_id', $screen->venue_id) == $venue->id ? 'selected' : '' }}>
                                    {{ $venue->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('venue_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- Screen Name --}}
                    <div>
                        <label class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Screen Name</label>

                        <input type="text" name="name"
                            value="{{ old('name', $screen->name) }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"
                            required>

                        @error('name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- Capacity --}}
                    <div>
                        <label class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Capacity</label>

                        <input type="number" name="capacity" min="1"
                            value="{{ old('capacity', $screen->capacity) }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"
                            required>

                        @error('capacity')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    <div>
                        <button class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-semibold">
                            Update Screen
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>
