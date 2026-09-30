<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;

Route::get('/admin/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('/admin/customers/{customerId}', [CustomerController::class, 'show'])->whereNumber('customerId')->name('customers.show');
Route::put('/admin/customers/{customerId}', [CustomerController::class, 'update'])->whereNumber('customerId')->name('customers.update');
