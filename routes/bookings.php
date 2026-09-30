<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

Route::get('/admin/bookings', [BookingController::class, 'index'])->name('bookings.index');
Route::get('/admin/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/admin/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::post('/admin/bookings/{bookingId}/status', [BookingController::class, 'updateStatus'])->whereNumber('bookingId')->name('bookings.status');
