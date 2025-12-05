<x-guest-layout>

<div 
    x-data="registrationPage()" 
    class="min-h-screen bg-gradient-to-br from-blue-50 to-purple-50 flex items-center justify-center px-4 py-12"
>

    <div class="w-full max-w-md bg-white/90 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-200 p-8">

        <!-- HEADER -->
        <div class="text-center mb-8" x-show="step !== 'success'">
            <h1 class="text-3xl font-extrabold text-gray-900">
                🎟️ Festival Pass Registration
            </h1>
            <p class="text-gray-500 mt-1 text-sm">
                Get your digital QR entry pass instantly.
            </p>
        </div>

        <!-- STEP 1: REGISTRATION FORM -->
        <template x-if="step === 'form'">
            <div x-transition>

                <!-- NAME -->
                <div class="mb-5">
                    <label class="text-sm font-semibold text-gray-700 mb-1 block">Full Name</label>
                    <input x-model="form.name"
                        class="w-full rounded-xl border-gray-300 p-3 shadow-sm focus:ring-blue-400 focus:border-blue-400"
                        type="text">
                    <p class="text-red-600 text-xs mt-1" x-text="errors.name"></p>
                </div>

                <!-- EMAIL -->
                <div class="mb-5">
                    <label class="text-sm font-semibold text-gray-700 mb-1 block">Email</label>
                    <input x-model="form.email"
                        class="w-full rounded-xl border-gray-300 p-3 shadow-sm focus:ring-blue-400 focus:border-blue-400"
                        type="email">
                    <p class="text-red-600 text-xs mt-1" x-text="errors.email"></p>
                </div>

                <!-- MOBILE -->
                <div class="mb-5">
                    <label class="text-sm font-semibold text-gray-700 mb-1 block">Mobile Number</label>
                    <input x-model="form.mobile"
                        class="w-full rounded-xl border-gray-300 p-3 shadow-sm focus:ring-blue-400 focus:border-blue-400"
                        type="text">
                    <p class="text-red-600 text-xs mt-1" x-text="errors.mobile"></p>
                </div>

                <!-- CATEGORY -->
                <div class="mb-7">
                    <label class="text-sm font-semibold text-gray-700 mb-1 block">Category</label>
                    <select x-model="form.category"
                        class="w-full rounded-xl border-gray-300 p-3 shadow-sm focus:ring-blue-400 focus:border-blue-400">
                        <option value="">Choose Category</option>
                        <option value="Delegate">Delegate</option>
                        <option value="Public">Public</option>
                        <option value="Student">Student</option>
                    </select>
                    <p class="text-red-600 text-xs mt-1" x-text="errors.category"></p>
                </div>

                <button 
                    @click="sendOtp"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-xl py-3 text-lg font-semibold shadow-md transition active:scale-95">
                    Send OTP
                </button>

            </div>
        </template>

        <!-- OTP STEP -->
        <template x-if="step === 'otp'">
            <div x-transition>

                <div class="mb-5">
                    <label class="text-sm font-semibold text-gray-700 mb-1 block">Enter OTP</label>
                    <input x-model="otp"
                        class="w-full rounded-xl border-gray-300 p-3 shadow-sm focus:ring-green-400 focus:border-green-400"
                        type="text">
                    <p class="text-red-600 text-xs mt-1" x-text="errors.otp"></p>
                </div>

                <button 
                    @click="verifyOtp"
                    class="w-full bg-green-600 hover:bg-green-700 text-white rounded-xl py-3 text-lg font-semibold shadow-md transition active:scale-95 mb-3">
                    Verify OTP
                </button>

                <button 
                    @click="sendOtp"
                    :disabled="resendTimer > 0"
                    class="w-full bg-gray-500 text-white rounded-xl py-2 text-sm disabled:opacity-40">
                    Resend OTP (<span x-text="resendTimer"></span>)
                </button>

            </div>
        </template>

        <!-- SUCCESS -->
        <template x-if="step === 'success'">
            <div x-transition>

                <div id="registrationPass"
                    class="max-w-md mx-auto bg-white shadow-xl rounded-2xl border border-gray-200 p-6 text-center">

                    <h2 class="text-3xl font-extrabold text-gray-900 mb-1">🎫 Your Festival Pass</h2>
                    <p class="text-gray-500 text-sm mb-4">Show this at the entry gate.</p>

                    <!-- QR -->
                    <div class="flex justify-center mb-4">
                        <img :src="qrUrl" class="w-44 h-44 rounded-xl shadow-lg border border-gray-300">
                    </div>

                    <!-- DETAILS -->
                    <div class="bg-gray-50 rounded-xl border p-4 text-left text-sm">

                        <template x-for="field in detailFields">
                            <div class="flex justify-between mb-2">
                                <span class="font-semibold text-gray-700" x-text="field.label"></span>
                                <span class="text-gray-900" x-text="attendee[field.key]"></span>
                            </div>
                        </template>

                    </div>

                    <div class="mt-6 flex flex-col gap-3">
                        <button @click="downloadPass"
                            class="bg-indigo-600 text-white py-2.5 rounded-xl font-semibold shadow hover:scale-105 transition">
                            ⬇️ Download Pass (PNG)
                        </button>

                        <button @click="printPass"
                            class="bg-gray-800 text-white py-2.5 rounded-xl font-semibold shadow hover:scale-105 transition">
                            🖨 Print Pass
                        </button>
                    </div>

                </div>

            </div>
        </template>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/html2canvas"></script>

<script>
function registrationPage() {
    return {
        step: 'form',
        form: { name: '', email: '', mobile: '', category: '' },
        otp: '',
        errors: {},
        resendTimer: 0,
        qrUrl: '',
        attendee: {},
        detailFields: [
            {label:'Name:', key:'name'},
            {label:'Email:', key:'email'},
            {label:'Mobile:', key:'mobile'},
            {label:'Category:', key:'category'},
            {label:'Pass ID:', key:'pass_id'},
        ],

        async sendOtp() {
            this.errors = {};

            const res = await fetch("/attendee/send-otp", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name=\"csrf-token\"]').content,
                },
                body: JSON.stringify(this.form),
            });

            const data = await res.json();

            if (!data.success) {
                this.errors = data.errors || {};
                return;
            }

            this.step = "otp";
            this.startResendTimer();
        },

        async verifyOtp() {
            this.errors = {};

            const res = await fetch("/attendee/verify-otp", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name=\"csrf-token\"]').content,
                },
                body: JSON.stringify({ ...this.form, otp: this.otp }),
            });

            const data = await res.json();

            if (!data.success) {
                this.errors = data.errors || {};
                return;
            }

            this.qrUrl = data.qr_url;
            this.attendee = data.attendee;

            this.step = "success";
        },

        startResendTimer() {
            this.resendTimer = 60;
            let t = setInterval(() => {
                this.resendTimer--;
                if (this.resendTimer <= 0) clearInterval(t);
            }, 1000);
        },

        downloadPass() {
            const element = document.getElementById('registrationPass');
            html2canvas(element).then(canvas => {
                const link = document.createElement('a');
                link.download = 'Festival-Pass.png';
                link.href = canvas.toDataURL("image/png");
                link.click();
            });
        },

        printPass() {
            window.print();
        }
    };
}
</script>

</x-guest-layout>
