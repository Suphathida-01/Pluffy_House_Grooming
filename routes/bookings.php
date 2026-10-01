<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/admin/bookings', [AdminController::class, 'bookingsIndex'])->name('bookings.index');
Route::get('/admin/bookings/create', [AdminController::class, 'create'])->name('bookings.create');
Route::post('/admin/bookings', [AdminController::class, 'store'])->name('bookings.store');
Route::post('/admin/bookings/{bookingId}/status', [AdminController::class, 'updateStatus'])->whereNumber('bookingId')->name('bookings.status');
