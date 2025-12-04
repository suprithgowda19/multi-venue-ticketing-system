<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendeeController;
use App\Http\Controllers\Admin\ScreenController;
use App\Http\Controllers\Admin\SlotController;
use App\Http\Controllers\Admin\MovieController;
use App\Http\Controllers\Admin\ScreenSlotAssignmentController;
use App\Http\Controllers\Admin\CheckinController;
use App\Http\Controllers\Admin\VenueController;
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Default welcome page
Route::get('/', function () {
    return view('welcome');
});

// Event registration page
Route::get('/event/register', [AttendeeController::class, 'create'])
    ->name('attendee.register');

Route::post('/event/register', [AttendeeController::class, 'store'])
    ->name('attendee.store');

Route::get('/event/thank-you/{uuid}', [AttendeeController::class, 'thankyou'])
    ->name('attendee.thankyou');


/*
|--------------------------------------------------------------------------
| Dashboard (Breeze)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});




/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
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





        /*
        |--------------------------------------------------------------------------
        | Scanner Page + Check-In API (must be inside admin)
        |--------------------------------------------------------------------------
        */

        // Scanner UI
        Route::get('/scan', function () {
            return view('admin.scan');
        })->name('scan');

        // Check-in API
        Route::post('/checkin', [CheckinController::class, 'store'])
            ->middleware('throttle:60,1')
            ->name('checkin');
    });

require __DIR__ . '/auth.php';
