<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Assign Movie
        </h2>
    </x-slot>

    <div x-data="assignmentForm()" class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6">

                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-6">Create Movie Assignment</h3>

                <form action="{{ route('admin.assignments.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- VENUE --}}
                    <div>
                        <label class="block font-medium mb-1 text-gray-700 dark:text-gray-300">Venue</label>

                        <select x-model="venue_id" @change="fetchScreensAndReset" name="venue_id"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded"
                            required>
                            <option value="">Select Venue</option>
                            @foreach ($venues as $venue)
                                <option value="{{ $venue->id }}">{{ $venue->name }}</option>
                            @endforeach
                        </select>

                        @error('venue_id')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- SCREEN --}}
                    <div>
                        <label class="block font-medium mb-1 text-gray-700 dark:text-gray-300">Screen</label>

                        <select name="screen_id" x-model="screen_id" @change="fetchAvailableSlots"
                            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded"
                            required>

                            <option value="">Select Screen</option>

                            <template x-for="screen in screens" :key="screen.id">
                                <option :value="screen.id" x-text="screen.name"></option>
                            </template>

                        </select>

                        @error('screen_id')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- DAY --}}
                    <div>
                        <label class="block font-medium mb-1 text-gray-700 dark:text-gray-300">Day (1–7)</label>

                        <input type="number" name="day" min="1" max="7"
                            x-model="day" @change="fetchAvailableSlots"
                            class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"
                            required>

                        @error('day')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- SLOT --}}
                    <div>
                        <label class="block font-medium mb-1 text-gray-700 dark:text-gray-300">Available Slot</label>

                        <select name="slot_id" x-model="slot_id"
                            class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"
                            required>

                            <option value="">Select Slot</option>

                            <template x-for="slot in slots" :key="slot.id">
                                <option :value="slot.id" x-text="formatTime(slot.start_time)"></option>
                            </template>

                        </select>

                        @error('slot_id')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- MOVIE --}}
                    <div>
                        <label class="block font-medium mb-1 text-gray-700 dark:text-gray-300">Movie Name</label>

                        <input type="text" name="movie" value="{{ old('movie') }}"
                            class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"
                            required>

                        @error('movie')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- STATUS --}}
                    <div>
                        <label class="block font-medium mb-1 text-gray-700 dark:text-gray-300">Status</label>

                        <select name="status"
                            class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>


                    <button class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-semibold">
                        Save Assignment
                    </button>

                </form>

            </div>

        </div>
    </div>


    {{-- Alpine.js Logic --}}
    <script>
        function assignmentForm() {
            return {
                venue_id: "",
                screen_id: "",
                day: "",
                slot_id: "",

                screens: [],
                slots: [],

                // Step 1 — load screens when venue selected, reset rest
                async fetchScreensAndReset() {
                    this.screen_id = "";
                    this.day = "";
                    this.slot_id = "";
                    this.slots = [];

                    if (!this.venue_id) return;

                    const res = await fetch("{{ route('admin.assignments.venue-data') }}?venue_id=" + this.venue_id);
                    const data = await res.json();

                    this.screens = data.screens;
                },

                // Step 2 — load only available (unassigned) slots
                async fetchAvailableSlots() {
                    if (!this.screen_id || !this.day || !this.venue_id) return;

                    const url = `{{ route('admin.assignments.available-slots') }}?screen_id=${this.screen_id}&day=${this.day}&venue_id=${this.venue_id}`;

                    const res = await fetch(url);
                    const data = await res.json();

                    this.slots = data.slots;
                },

                // Formatting time to 12-hour format
                formatTime(time) {
                    const [h, m] = time.split(":");
                    const hour = (h % 12) || 12;
                    const ampm = h >= 12 ? "PM" : "AM";
                    return hour + ":" + m + " " + ampm;
                }
            }
        }
    </script>

</x-app-layout>
