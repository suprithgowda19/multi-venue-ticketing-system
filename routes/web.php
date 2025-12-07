<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Public\PublicBookingOtpController;
use App\Http\Controllers\Public\ShowtimeController;
use App\Http\Controllers\Attendee\RegisterController;
use App\Http\Controllers\Admin\VenueController;
use App\Http\Controllers\Admin\ScreenController;
use App\Http\Controllers\Admin\SlotController;
use App\Http\Controllers\Admin\MovieController;
use App\Http\Controllers\Admin\ScreenSlotAssignmentController;
use App\Http\Controllers\Api\OtpController;


/*
|--------------------------------------------------------------------------
| Public Booking Auth Page
|--------------------------------------------------------------------------
*/
Route::get('/public/booking/auth', function () {
    return view('public.booking-auth');
});


/*
|--------------------------------------------------------------------------
| PUBLIC SESSION CHECK (Required for Refresh Restore)
|--------------------------------------------------------------------------
*/
Route::get('/public/check-session', function () {
    return response()->json([
        'verified' => session('public_attendee_verified', false)
    ]);
});


/*
|--------------------------------------------------------------------------
| PUBLIC BOOKING ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('public/booking')->group(function () {

    // OTP Request + Verification
    Route::post('/request-otp', [PublicBookingOtpController::class, 'requestOtp'])
        ->name('public.booking.requestOtp');

    Route::post('/verify-otp', [PublicBookingOtpController::class, 'verifyOtp'])
        ->name('public.booking.verifyOtp');

    // Showtimes
    Route::get('/showtimes', [ShowtimeController::class, 'index'])
        ->name('public.booking.showtimes');
});


/*
|--------------------------------------------------------------------------
| SHARED OTP API (registration)
|--------------------------------------------------------------------------
*/
Route::post('/otp/send', [OtpController::class, 'send']);
Route::post('/otp/verify', [OtpController::class, 'verify']);


/*
|--------------------------------------------------------------------------
| Public Welcome & Registration
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

Route::get('/attendee/register', function () {
    return view('attendee.register');
})->name('attendee.register');

Route::prefix('attendee')->group(function () {
    Route::post('/send-otp', [RegisterController::class, 'sendOtp'])->name('attendee.sendOtp');
    Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])->name('attendee.verifyOtp');
});


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {

        Route::get('/', fn () => redirect()->route('admin.screens.index'))->name('home');

        Route::get('/available-slots', [ScreenSlotAssignmentController::class, 'availableSlots'])
            ->name('assignments.available-slots');

        Route::resource('venues', VenueController::class)->except(['show']);
        Route::resource('screens', ScreenController::class)->except(['show']);
        Route::resource('slots', SlotController::class)->except(['show']);
        Route::resource('assignments', ScreenSlotAssignmentController::class)->except(['show']);
    });
});

require __DIR__ . '/auth.php';
