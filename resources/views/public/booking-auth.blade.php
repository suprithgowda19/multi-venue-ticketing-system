<x-guest-layout>

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="max-w-md mx-auto p-6" x-data="otpPage()" x-init="init()">

    <h1 class="text-2xl font-bold mb-4">Verify to Start Booking</h1>

    <!-- STEP 1: ENTER EMAIL + MOBILE -->
    <div x-show="step === 1" x-cloak>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" x-model="form.email" @input="clearErrors('email')"
                   class="w-full border px-3 py-2 rounded">
            <p class="text-red-500 text-sm" x-text="errors.email"></p>
        </div>

        <div class="mb-3">
            <label>Mobile</label>
            <input type="text" maxlength="10" 
                   x-model="form.mobile" 
                   @input="sanitizeMobile(); clearErrors('mobile')"
                   class="w-full border px-3 py-2 rounded">
            <p class="text-red-500 text-sm" x-text="errors.mobile"></p>
        </div>

        <p class="text-red-500 text-sm" x-text="errors.general"></p>

        <button 
            @click="sendOtp()"
            :disabled="loading"
            class="bg-blue-600 text-white px-4 py-2 rounded w-full flex justify-center items-center gap-2 disabled:opacity-50">

            <template x-if="loading">
                <span class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
            </template>

            <span x-text="loading ? 'Sending...' : 'Send OTP'"></span>
        </button>
    </div>


    <!-- STEP 2: ENTER OTP -->
    <div x-show="step === 2" x-cloak>

        <p class="text-gray-600 mb-2">Enter the OTP sent to your email.</p>

        <input type="text" maxlength="6" x-model="otp" @input="clearErrors('otp')"
               class="w-full border px-3 py-2 rounded">

        <p class="text-red-500 text-sm" x-text="errors.otp"></p>

        <button 
            @click="verifyOtp()"
            :disabled="loading"
            class="bg-green-600 text-white px-4 py-2 rounded w-full mt-3 flex justify-center items-center gap-2 disabled:opacity-50">

            <template x-if="loading">
                <span class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
            </template>

            <span x-text="loading ? 'Verifying...' : 'Verify OTP'"></span>
        </button>
    </div>


    <!-- STEP 3: SELECT DAY -->
    <div x-show="step === 3" x-cloak>

        <h2 class="font-bold text-xl mb-4">Select Day</h2>

        <select x-model="selectedDay" @change="loadVenues(true)"
                class="w-full border px-3 py-2 rounded">
            <option value="">-- Select Day --</option>
            <template x-for="d in 7">
                <option :value="d">Day <span x-text="d"></span></option>
            </template>
        </select>
    </div>


    <!-- STEP 4: SELECT VENUE -->
    <div x-show="step === 4" x-cloak>

        <h2 class="font-bold text-xl mb-4">Select Venue</h2>

        <select x-model="selectedVenue" @change="loadMovies(true)"
                class="w-full border px-3 py-2 rounded">

            <option value="">-- Choose Venue --</option>

            <template x-for="v in venues">
                <option :value="v.venue_id" x-text="v.venue_name"></option>
            </template>

        </select>
    </div>


    <!-- STEP 5: MOVIE LIST -->
    <div x-show="step === 5" x-cloak>

        <h2 class="font-bold text-xl mb-3" x-text="venueName"></h2>

        <template x-for="movie in movies">
            <div class="border p-3 bg-white mt-3 rounded shadow hover:shadow-md transition">

                <p class="font-semibold text-lg" x-text="movie.movie"></p>

                <template x-for="show in movie.showtimes">
                    <div class="mt-2 pl-3 border-l-4 border-blue-500">
                        <p><strong>Screen:</strong> <span x-text="show.screen_name"></span></p>
                        <p><strong>Time:</strong> <span x-text="show.start_time"></span></p>
                    </div>
                </template>

            </div>
        </template>
    </div>

</div>



<script>
function otpPage() {
    return {

        /* ------------------ STATE ------------------ */
        step: 1,
        loading: false,
        otp: "",
        form: { email: "", mobile: "" },

        selectedDay: "",
        selectedVenue: "",
        venues: [],
        movies: [],
        venueName: "",

        errors: {},


        /* ------------------ INIT ------------------ */
        async init() {
            this.loadState();

            const res = await fetch('/public/check-session', {
                headers: { "Accept": "application/json" },
                credentials: 'same-origin'
            });

            const data = await res.json();

            if (!data.verified) {
                this.resetState();
                return;
            }

            if (this.step >= 3 && this.selectedDay) {
                await this.loadVenues(false);
            }

            if (this.step >= 4 && this.selectedVenue) {
                await this.loadMovies(false);
            }
        },


        /* ------------------ HELPERS ------------------ */
        sanitizeMobile() {
            this.form.mobile = this.form.mobile.replace(/\D/g, '');
        },

        clearErrors(field = null) {
            if (field) delete this.errors[field];
            else this.errors = {};
        },

        saveState() {
            localStorage.setItem("bookingState", JSON.stringify({
                step: this.step,
                selectedDay: this.selectedDay,
                selectedVenue: this.selectedVenue,
                venueName: this.venueName,
            }));
        },

        loadState() {
            const raw = localStorage.getItem("bookingState");
            if (!raw) return;
            Object.assign(this, JSON.parse(raw));
        },

        resetState() {
            this.step = 1;
            this.selectedDay = "";
            this.selectedVenue = "";
            this.venueName = "";
            this.venues = [];
            this.movies = [];
            this.errors = {};
            localStorage.removeItem("bookingState");
        },


        /* ------------------ STEP 1: SEND OTP ------------------ */
        async sendOtp() {
            this.loading = true;
            this.clearErrors();

            const res = await fetch("/public/booking/request-otp", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content
                },
                credentials: "same-origin",
                body: JSON.stringify(this.form)
            });

            const data = await res.json();
            this.loading = false;

            if (!data.success) {
                this.errors = data.errors || { general: "Unable to send OTP." };
                return;
            }

            this.step = 2;
        },


        /* ------------------ STEP 2: VERIFY OTP ------------------ */
        async verifyOtp() {
            this.loading = true;
            this.clearErrors();

            const res = await fetch("/public/booking/verify-otp", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content
                },
                credentials: "same-origin",
                body: JSON.stringify({ otp: this.otp })
            });

            const data = await res.json();
            this.loading = false;

            if (!data.success) {
                this.errors = data.errors || { otp: "Invalid OTP." };
                return;
            }

            this.step = 3;
            this.saveState();
        },


        /* ------------------ STEP 3 → 4: LOAD VENUES ------------------ */
        async loadVenues(updateStep = true) {
            this.venues = [];
            this.movies = [];

            if (updateStep) this.step = 4;

            const res = await fetch(`/public/booking/showtimes?day=${this.selectedDay}`);
            const data = await res.json();

            this.venues = data.venues || [];

            if (updateStep) {
                this.selectedVenue = "";
            }

            this.saveState();
        },


        /* ------------------ STEP 4 → 5: LOAD MOVIES ------------------ */
        async loadMovies(updateStep = true) {
            this.movies = [];

            const res = await fetch(`/public/booking/showtimes?day=${this.selectedDay}&venue_id=${this.selectedVenue}`);
            const data = await res.json();

            this.movies = data.movies || [];
            this.venueName = data.venue_name || "";

            if (updateStep) {
                this.step = 5;
            }

            this.saveState();
        }

    };
}
</script>

</x-guest-layout>
