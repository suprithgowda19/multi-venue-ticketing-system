<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Attendee\AttendeeController;
use App\Http\Controllers\Attendee\RegisterController;
use App\Http\Controllers\Admin\ScreenController;
use App\Http\Controllers\Admin\SlotController;
use App\Http\Controllers\Admin\MovieController;
use App\Http\Controllers\Admin\ScreenSlotAssignmentController;
use App\Http\Controllers\Admin\VenueController;
use App\Http\Controllers\Api\OtpController;

Route::post('/otp/send', [OtpController::class, 'send']);
Route::post('/otp/verify', [OtpController::class, 'verify']);


// Default welcome page
Route::get('/', function () {
    return view('welcome');
});
Route::get('/attendee/register', function () {
    return view('attendee.register');
})->name('attendee.register');

Route::prefix('attendee')->group(function () {
    Route::post('/send-otp', [RegisterController::class, 'sendOtp'])
        ->name('attendee.sendOtp');

    Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])
        ->name('attendee.verifyOtp');
});



Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', function () {
            return redirect()->route('admin.screens.index');
        })->name('home');

        // Slot dropdown AJAX
        Route::get('/available-slots', [ScreenSlotAssignmentController::class, 'availableSlots'])
            ->name('available-slots');
        // CRUD routes
        Route::resource('venues', VenueController::class)->except(['show']);
        Route::resource('screens', ScreenController::class)->except(['show']);
        Route::resource('slots', SlotController::class)->except(['show']);
        Route::resource('assignments', ScreenSlotAssignmentController::class)->except(['show']);

        Route::get('/venue-data', [ScreenSlotAssignmentController::class, 'venueData'])
            ->name('assignments.venue-data');
        Route::get('/available-slots', [ScreenSlotAssignmentController::class, 'availableSlots'])
            ->name('assignments.available-slots');

    });

require __DIR__ . '/auth.php';
