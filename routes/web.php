<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/services', [AdminController::class, 'services'])->name('services');
Route::get('/services/create', [AdminController::class, 'createService'])->name('createService');
Route::post('/services', [AdminController::class, 'storeService'])->name('storeService');
Route::get('/services/{service}/edit', [AdminController::class, 'editService'])->name('editService');
Route::put('/services/{service}', [AdminController::class, 'updateService'])->name('updateService');
Route::delete('/services/{service}', [AdminController::class, 'deleteService'])->name('deleteService');
Route::get('/staff', [AdminController::class, 'staff'])->name('staff');
Route::post('/staff', [AdminController::class, 'storeStaff'])->name('storeStaff');
Route::post('/staff/schedules', [AdminController::class, 'storeSchedule'])->name('storeSchedule');
Route::post('/staff/closed-days', [AdminController::class, 'toggleStoreClosure'])->name('toggleStoreClosure');
Route::get('/admin/payments', [AdminController::class, 'payments'])->name('payments');
 