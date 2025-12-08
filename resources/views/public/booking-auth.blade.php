<x-guest-layout>

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
/* BOOKMYSHOW LIGHT THEME */
.seat {
    width: 22px;
    height: 22px;
    border-radius: 4px;
    border: 1.6px solid #1fa31f;
    background-color: white;
    color: #1fa31f;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    margin: 2px;
    user-select: none;
    transition: .15s;
}
.seat.selected { background-color:#1fa31f; color:white; }
.seat.booked { border-color:#bdbdbd; color:#bdbdbd; }
.seat.locked { border-color:#f1c40f; color:#f1c40f; }

@media(max-width:480px){
    .seat { width:18px; height:18px; font-size:8px; }
}

.seat-scroll { overflow-x:auto; -webkit-overflow-scrolling:touch; }

.screen-bar{
    height:42px;
    border-radius:6px;
    background:linear-gradient(90deg,#e9f1ff,#d7e6ff);
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
    color:#0b3f91;
}
</style>


<div class="max-w-3xl mx-auto p-6" x-data="bookingPage()" x-init="init()">

    <h1 class="text-2xl font-bold mb-4">Book Tickets</h1>


    <!-- ========================= -->
    <!-- OTP SECTION -->
    <!-- ========================= -->
    <div x-show="!verified" x-cloak>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" class="w-full border px-3 py-2 rounded"
                   x-model="form.email">
        </div>

        <div class="mb-3">
            <label>Mobile</label>
            <input type="text" maxlength="10"
                   class="w-full border px-3 py-2 rounded"
                   x-model="form.mobile"
                   @input="form.mobile=form.mobile.replace(/\D/g,'')">
        </div>

        <div class="mb-3" x-show="otpRequested">
            <label>Enter OTP</label>
            <input type="text" maxlength="6" class="w-full border px-3 py-2 rounded"
                   x-model="otp">
        </div>

        <button @click="otpRequested ? verifyOtp() : sendOtp()"
                class="bg-blue-600 text-white px-4 py-2 rounded w-full">
            <span x-text="otpRequested ? 'Verify OTP' : 'Send OTP'"></span>
        </button>

    </div>




    <!-- ========================= -->
    <!-- MAIN BOOKING FLOW -->
    <!-- ========================= -->
    <div x-show="verified" x-cloak class="mt-6">

        <!-- DAY -->
        <label class="font-semibold">Select Day</label>
        <select class="w-full border px-3 py-2 rounded mb-4"
                x-model="selectedDay"
                @change="loadVenues()">
            <option value="">-- Select Day --</option>
            <template x-for="d in 7">
                <option :value="d">Day <span x-text="d"></span></option>
            </template>
        </select>


        <!-- VENUE -->
        <div x-show="selectedDay" x-cloak>
            <label class="font-semibold">Select Venue</label>
            <select class="w-full border px-3 py-2 rounded mb-4"
                    x-model="selectedVenue"
                    @change="loadMovies()">
                <option value="">-- Select Venue --</option>
                <template x-for="v in venues">
                    <option :value="v.venue_id" x-text="v.venue_name"></option>
                </template>
            </select>
        </div>


        <!-- MOVIE + SHOWTIMES -->
        <div x-show="selectedVenue" x-cloak>
            <template x-for="movie in movies">
                <div class="border p-4 bg-white rounded shadow mt-4">

                    <p class="font-semibold text-lg" x-text="movie.movie"></p>

                    <template x-for="show in movie.showtimes">
                        <div class="mt-3 p-3 border-l-4 border-blue-500 bg-gray-50 
                                    hover:bg-gray-100 cursor-pointer"
                             @click="openSeatSelection(movie.movie, show)">
                            <p><strong>Screen:</strong> <span x-text="show.screen_name"></span></p>
                            <p><strong>Time:</strong> <span x-text="show.start_time"></span></p>
                        </div>
                    </template>

                </div>
            </template>
        </div>




        <!-- ========================= -->
        <!-- SEAT SELECTION -->
        <!-- ========================= -->
        <div x-show="seatMode" x-cloak class="mt-10">

            <h2 class="text-xl font-bold">Select Seats</h2>

            <p class="text-gray-700 mb-4">
                <span x-text="selectedMovie"></span> • 
                Screen <span x-text="selectedScreen"></span> • 
                <span x-text="selectedTime"></span>
            </p>

            <div class="screen-bar">SCREEN</div>

            <div class="seat-scroll border rounded p-3 mt-4">
                <div class="min-w-[900px]">

                    <template x-for="row in groupedSeats">
                        <div class="flex items-center mb-2">

                            <div class="w-6 text-center font-semibold"
                                 x-text="row.row_label"></div>

                            <div class="flex flex-wrap gap-1 ml-3">

                                <template x-for="seat in row.seats">
                                    <div class="seat"
                                         :class="{
                                            'selected': selectedSeats.includes(seat.id),
                                            'booked'  : seat.status === 'booked',
                                            'locked'  : seat.status === 'locked'
                                         }"
                                         @click="toggleSeat(seat)"
                                         x-text="seat.seat_number"></div>
                                </template>

                            </div>

                        </div>
                    </template>

                </div>
            </div>


            <!-- FOOTER -->
            <div class="mt-6 flex items-center justify-between">
                <p class="text-sm">
                    Selected: <span x-text="selectedSeatCodes.join(', ') || '—'"></span>
                </p>

                <button class="bg-purple-600 text-white px-4 py-2 rounded"
                        @click="confirmBooking()">
                    Confirm Booking
                </button>
            </div>



            <!-- ========================= -->
            <!-- SUCCESS MESSAGE -->
            <!-- ========================= -->
            <div x-show="bookingSuccess" x-cloak
                 class="mt-8 p-6 bg-green-50 border border-green-300 rounded">

                <h2 class="text-xl font-bold text-green-700">Booking Confirmed!</h2>

                <p class="mt-2 text-gray-700">Booking ID:
                    <strong x-text="bookingId"></strong>
                </p>

                <p class="text-gray-600 mt-1">
                    Ticket (PDF) & QR emailed to you.
                </p>

                <!-- PDF -->
                <div class="mt-4">
                    <a :href="`/storage/tickets/booking_${bookingId}.pdf`"
                       class="bg-purple-600 text-white px-4 py-2 rounded inline-block"
                       download>
                        Download Ticket PDF
                    </a>
                </div>

                <!-- QR -->
                <div class="mt-4">
                    <p class="font-semibold text-sm text-gray-700">QR Code:</p>
                    <img :src="`/storage/qrs/booking_${bookingId}.png`"
                         class="w-40 mt-2 border rounded shadow">
                </div>

                <p class="text-xs mt-4 text-gray-600">Resetting…</p>
            </div>

        </div>

    </div>

</div>




<script>
function bookingPage(){
return {

    /* STATE */
    verified:false,
    otpRequested:false,
    otp:"",
    form:{email:"", mobile:""},

    selectedDay:"",
    selectedVenue:"",
    venues:[],
    movies:[],

    seatMode:false,
    assignmentId:null,
    selectedMovie:"",
    selectedScreen:"",
    selectedTime:"",
    seatMap:[],
    selectedSeats:[],


    /* SUCCESS */
    bookingSuccess:false,
    bookingId:null,


    /* INIT */
    async init(){
        const res = await fetch('/public/check-session');
        this.verified = (await res.json()).verified;
    },


    /* OTP */
    async sendOtp(){
        const res = await fetch('/public/booking/request-otp',{
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector("meta[name=csrf-token]").content},
            body:JSON.stringify(this.form)
        });
        const data = await res.json();
        if(data.success) this.otpRequested = true;
    },

    async verifyOtp(){
        const res = await fetch('/public/booking/verify-otp',{
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector("meta[name=csrf-token]").content},
            body:JSON.stringify({otp:this.otp})
        });
        const data = await res.json();
        if(data.success) this.verified = true;
    },


    /* LOAD DATA */
    async loadVenues(){
        const res = await fetch(`/public/booking/showtimes?day=${this.selectedDay}`);
        this.venues = (await res.json()).venues;
    },

    async loadMovies(){
        const res = await fetch(`/public/booking/showtimes?day=${this.selectedDay}&venue_id=${this.selectedVenue}`);
        this.movies = (await res.json()).movies;
    },


    /* OPEN SEAT MODE */
    openSeatSelection(movie, show){
        this.selectedMovie  = movie;
        this.selectedScreen = show.screen_name;
        this.selectedTime   = show.start_time;
        this.assignmentId   = show.assignment_id;

        this.seatMode = true;
        this.selectedSeats = [];
        this.loadSeatMap();
    },


    /* SEAT MAP */
    async loadSeatMap(){
        const res = await fetch(`/public/booking/assignment/${this.assignmentId}/seats-map`);
        this.seatMap = (await res.json()).seats;
    },

    get groupedSeats(){
        const rows = {};
        this.seatMap.forEach(s=>{
            if(!rows[s.row_label]) rows[s.row_label]=[];
            rows[s.row_label].push(s);
        });
        return Object.keys(rows).sort().map(r=>({
            row_label:r,
            seats:rows[r].sort((a,b)=>a.seat_number-b.seat_number)
        }));
    },


    /* SEAT SELECTION */
    toggleSeat(seat){
        if(seat.status!=='available') return;

        if(this.selectedSeats.includes(seat.id))
            this.selectedSeats = this.selectedSeats.filter(x=>x!==seat.id);
        else
            this.selectedSeats.push(seat.id);
    },

    get selectedSeatCodes(){
        return this.selectedSeats.map(id=>{
            const s=this.seatMap.find(x=>x.id===id);
            return s ? s.seat_code : id;
        });
    },


    /* CONFIRM BOOKING */
    async confirmBooking(){

        if(this.selectedSeats.length === 0){
            alert("Select at least 1 seat.");
            return;
        }

        const res = await fetch(`/public/booking/assignment/${this.assignmentId}/confirm`,{
            method:'POST',
            headers:{
                "Content-Type":"application/json",
                "X-CSRF-TOKEN":document.querySelector("meta[name=csrf-token]").content
            },
            body:JSON.stringify({ seats:this.selectedSeats })
        });

        const data = await res.json();

        if(!data.success){
            alert("Some seats were already booked: " + data.failed_seats.join(', '));
            await this.loadSeatMap();
            this.selectedSeats=[];
            return;
        }

        // SHOW SUCCESS
        this.bookingSuccess = true;
        this.bookingId = data.booking_id;

        // AUTO RESET after 3 seconds
        setTimeout(() => this.resetBookingFlow(), 3000);
    },


    /* FULL RESET AFTER BOOKING */
    resetBookingFlow(){

        this.bookingSuccess = false;
        this.seatMode = false;
        this.bookingId = null;

        this.selectedSeats = [];
        this.seatMap = [];

        this.selectedMovie = "";
        this.selectedScreen = "";
        this.selectedTime = "";

        this.selectedDay = "";
        this.selectedVenue = "";
        this.movies = [];
        this.venues = [];

        localStorage.removeItem("bookingState");

        this.$nextTick(() => {
            window.scrollTo({ top:0, behavior:"smooth" });
        });
    }

};
}
</script>

</x-guest-layout>
