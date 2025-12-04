<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Movie Assignment
        </h2>
    </x-slot>

    <div x-data="assignmentForm({{ $assignment->venue_id }}, {{ $assignment->screen_id }}, {{ $assignment->slot_id }}, {{ $assignment->day }})"
         class="py-8">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg p-6">

                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-6">Update Assignment</h3>

                <form action="{{ route('admin.assignments.update', $assignment->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- VENUE --}}
                    <div>
                        <label class="block font-medium mb-1 text-gray-700 dark:text-gray-300">Venue</label>

                        <select name="venue_id"
                                x-model="venue_id"
                                @change="resetOnVenueChange"
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded"
                                required>

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

                        <select name="screen_id"
                                x-model="screen_id"
                                @change="fetchAvailableSlots"
                                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 rounded"
                                required>

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

                        <input type="number"
                               name="day"
                               x-model="day"
                               @change="fetchAvailableSlots"
                               min="1"
                               max="7"
                               class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"
                               required>

                        @error('day')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- SLOT --}}
                    <div>
                        <label class="block font-medium mb-1 text-gray-700 dark:text-gray-300">Available Slot</label>

                        <select name="slot_id"
                                x-model="slot_id"
                                class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"
                                required>

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

                        <input type="text" name="movie" value="{{ old('movie', $assignment->movie) }}"
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
                            <option value="active" {{ $assignment->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $assignment->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>


                    <button class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-semibold">
                        Update Assignment
                    </button>

                </form>

            </div>
        </div>
    </div>


    {{-- Alpine.js Logic --}}
    <script>
        function assignmentForm(initialVenue, initialScreen, initialSlot, initialDay) {
            return {
                venue_id: initialVenue,
                screen_id: initialScreen,
                slot_id: initialSlot,
                day: initialDay,

                screens: @json($screens),
                slots: @json($slots),

                // Reset and reload screens if venue is changed
                resetOnVenueChange() {
                    this.screen_id = "";
                    this.slot_id = "";
                    this.day = "";
                    this.slots = [];

                    this.fetchScreens();
                },

                // Fetch screens for selected venue
                async fetchScreens() {
                    const res = await fetch("{{ route('admin.assignments.venue-data') }}?venue_id=" + this.venue_id);
                    const data = await res.json();

                    this.screens = data.screens;
                },

                // Fetch only available slots for screen + day
                async fetchAvailableSlots() {
                    if (!this.screen_id || !this.day) return;

                    const url = `{{ route('admin.assignments.available-slots') }}?screen_id=${this.screen_id}&day=${this.day}&venue_id=${this.venue_id}`;

                    const res = await fetch(url);
                    const data = await res.json();

                    this.slots = data.slots;

                    // Keep previously assigned slot if still valid
                    if (!this.slots.find(s => s.id == this.slot_id)) {
                        this.slot_id = "";
                    }
                },

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
