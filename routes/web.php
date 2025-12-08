<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Public\PublicBookingOtpController;
use App\Http\Controllers\Public\ShowtimeController;
use App\Http\Controllers\Public\SeatSelectionController;
use App\Http\Controllers\Public\BookingController;

/*
|--------------------------------------------------------------------------
| Attendee Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Attendee\RegisterController;

/*
|--------------------------------------------------------------------------
| Admin Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\VenueController;
use App\Http\Controllers\Admin\ScreenController;
use App\Http\Controllers\Admin\SlotController;
use App\Http\Controllers\Admin\MovieController;
use App\Http\Controllers\Admin\ScreenSlotAssignmentController;

/*
|--------------------------------------------------------------------------
| Shared OTP API
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Api\OtpController;



/*
|--------------------------------------------------------------------------
| PUBLIC BOOKING AUTH PAGE
|--------------------------------------------------------------------------
*/
Route::get('/public/booking/auth', function () {
    return view('public.booking-auth');
});


/*
|--------------------------------------------------------------------------
| CHECK SESSION (OTP verification status)
|--------------------------------------------------------------------------
*/
Route::get('/public/check-session', function () {
    return response()->json([
        'verified' => session('public_attendee_verified', false)
    ]);
});


/*
|--------------------------------------------------------------------------
| PUBLIC BOOKING ROUTES (OTP + Showtimes)
|--------------------------------------------------------------------------
*/
Route::prefix('public/booking')->group(function () {

    // Send OTP
    Route::post('/request-otp', [PublicBookingOtpController::class, 'requestOtp'])
        ->name('public.booking.requestOtp');

    // Verify OTP
    Route::post('/verify-otp', [PublicBookingOtpController::class, 'verifyOtp'])
        ->name('public.booking.verifyOtp');

    // List showtimes (day + venue)
    Route::get('/showtimes', [ShowtimeController::class, 'index'])
        ->name('public.booking.showtimes');

    // SEAT MAP (Correct Controller)
    Route::get('/assignment/{assignment}/seats-map',
        [SeatSelectionController::class, 'seats'])
        ->name('public.booking.seatsMap');

    // CONFIRM BOOKING (Correct Controller)
    Route::post('/assignment/{assignment}/confirm',
        [BookingController::class, 'confirm'])
        ->name('public.booking.confirm');
});



/*
|--------------------------------------------------------------------------
| SHARED OTP ROUTES (Attendee registration)
|--------------------------------------------------------------------------
*/
Route::post('/otp/send', [OtpController::class, 'send']);
Route::post('/otp/verify', [OtpController::class, 'verify']);



/*
|--------------------------------------------------------------------------
| PUBLIC WELCOME PAGES
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'));

Route::get('/attendee/register', fn () => view('attendee.register'))
    ->name('attendee.register');

Route::prefix('attendee')->group(function () {
    Route::post('/send-otp', [RegisterController::class, 'sendOtp'])
        ->name('attendee.sendOtp');

    Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])
        ->name('attendee.verifyOtp');
});



/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {

        Route::get('/', fn () => redirect()->route('admin.screens.index'))
            ->name('home');

        Route::get('/available-slots',
            [ScreenSlotAssignmentController::class, 'availableSlots'])
            ->name('assignments.available-slots');

        Route::resource('venues', VenueController::class)->except(['show']);
        Route::resource('screens', ScreenController::class)->except(['show']);
        Route::resource('slots', SlotController::class)->except(['show']);
        Route::resource('assignments', ScreenSlotAssignmentController::class)->except(['show']);
    });
});


require __DIR__ . '/auth.php';
