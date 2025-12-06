<x-guest-layout>

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="max-w-md mx-auto p-6" x-data="otpPage()">

    <h1 class="text-2xl font-bold mb-4">Verify to Start Booking</h1>

    <!-- STEP 1 -->
    <div x-show="step === 1" x-cloak>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" x-model="form.email" class="w-full border px-3 py-2 rounded">
            <p class="text-red-500 text-sm" x-text="errors.email"></p>
        </div>

        <div class="mb-3">
            <label>Mobile</label>
            <input type="text" maxlength="10" x-model="form.mobile"
                class="w-full border px-3 py-2 rounded">
            <p class="text-red-500 text-sm" x-text="errors.mobile"></p>
        </div>

        <p class="text-red-500 text-sm" x-text="errors.general"></p>

        <button @click="sendOtp()"
            class="bg-blue-600 text-white px-4 py-2 rounded w-full">
            Send OTP
        </button>
    </div>


    <!-- STEP 2 -->
    <div x-show="step === 2" x-cloak>

        <p class="text-gray-600">Enter the OTP sent to your email.</p>

        <input type="text" maxlength="6" x-model="otp"
            class="w-full border px-3 py-2 rounded mt-3">

        <p class="text-red-500 text-sm" x-text="errors.otp"></p>

        <button @click="verifyOtp()"
            class="bg-green-600 text-white px-4 py-2 rounded w-full mt-3">
            Verify OTP
        </button>
    </div>


    <!-- STEP 3 (SHOWTIME SELECTION STARTS AFTER OTP) -->
    <div x-show="step === 3" x-cloak>

        <h2 class="font-bold text-xl mb-4">Select Day</h2>

        <select class="w-full border px-3 py-2 rounded"
            x-model="selectedDay"
            @change="loadVenues()">
            <option value="">-- Select Day --</option>
            <template x-for="d in 7">
                <option :value="d">Day <span x-text="d"></span></option>
            </template>
        </select>

        <p class="text-red-500 text-sm" x-text="errors.general"></p>
    </div>


    <!-- STEP 4: SELECT VENUE -->
    <div x-show="step === 4" x-cloak>

        <h2 class="font-bold text-xl mb-4">Select Venue</h2>

        <select class="w-full border px-3 py-2 rounded"
            x-model="selectedVenue"
            @change="loadMovies()">

            <option value="">-- Choose Venue --</option>

            <template x-for="v in venues">
                <option :value="v.venue_id" x-text="v.venue_name"></option>
            </template>

        </select>

    </div>


    <!-- STEP 5: SHOW MOVIES -->
    <div x-show="step === 5" x-cloak>

        <h2 class="font-bold text-xl mb-4" x-text="venueName"></h2>

        <template x-for="movie in movies">
            <div class="border p-3 bg-white mt-3 rounded">

                <p class="font-semibold" x-text="movie.movie"></p>

                <template x-for="show in movie.showtimes">
                    <div class="mt-1 pl-2 border-l border-blue-500">
                        <p>Screen: <span x-text="show.screen_name"></span></p>
                        <p>Time: <span x-text="show.start_time"></span></p>
                    </div>
                </template>

            </div>
        </template>

    </div>

</div>


<script>
function otpPage() {
    return {
        step: 1,
        otp: "",
        form: { email: "", mobile: "" },
        errors: {},

        selectedDay: "",
        selectedVenue: "",
        venues: [],
        movies: [],
        venueName: "",

        clearErrors() { this.errors = {}; },

        async sendOtp() {
            this.clearErrors();

            const res = await fetch("/public/booking/request-otp", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content
                },
                body: JSON.stringify(this.form)
            });

            const data = await res.json();

            if (!data.success) {
                this.errors = data.errors || {};
                return;
            }

            this.step = 2;
        },

        async verifyOtp() {
            this.clearErrors();

            const res = await fetch("/public/booking/verify-otp", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content
                },
                body: JSON.stringify({ otp: this.otp })
            });

            const data = await res.json();

            if (!data.success) {
                this.errors = data.errors || {};
                return;
            }

            this.step = 3;
        },

        async loadVenues() {
            this.venues = [];
            this.movies = [];
            this.selectedVenue = "";

            if (!this.selectedDay) return;

            const res = await fetch(`/public/booking/showtimes?day=${this.selectedDay}`);
            const data = await res.json();

            this.venues = data.venues;
            this.step = 4;
        },

        async loadMovies() {
            if (!this.selectedVenue) return;

            const res = await fetch(`/public/booking/showtimes?day=${this.selectedDay}&venue_id=${this.selectedVenue}`);
            const data = await res.json();

            this.movies = data.movies;
            this.venueName = data.venue_name;
            this.step = 5;
        }
    };
}
</script>

</x-guest-layout>
