<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\PublicBookingOtpController;
use App\Http\Controllers\Public\ShowtimeController;

Route::prefix('public/booking')->group(function () {

    // Public OTP
    Route::post('/request-otp', [PublicBookingOtpController::class, 'requestOtp']);
    Route::post('/verify-otp',  [PublicBookingOtpController::class, 'verifyOtp']);

    // Protected
    Route::middleware('public.booking.verified')->group(function () {
        Route::get('/showtimes', [ShowtimeController::class, 'index']);
    });
});
