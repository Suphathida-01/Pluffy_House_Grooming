<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\MyBookingsController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'services.index')->name('home');

Route::get('/services/{serviceSlug}', [ServiceController::class, 'show'])
    ->name('services.show');

Route::get('/my-bookings', [MyBookingsController::class, 'index'])
    ->name('bookings.index');

Route::get('/booking/{serviceSlug}/date-time', [BookingController::class, 'dateTime'])
    ->name('booking.datetime');

Route::get('/booking/{serviceSlug}', [BookingController::class, 'create'])
    ->name('booking.create');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
