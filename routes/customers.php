<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/admin/customers', [AdminController::class, 'customersIndex'])->name('customers.index');
Route::get('/admin/customers/{customerId}', [AdminController::class, 'show'])->whereNumber('customerId')->name('customers.show');
Route::put('/admin/customers/{customerId}', [AdminController::class, 'update'])->whereNumber('customerId')->name('customers.update');
