<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// ===== Guest routes (ยังไม่ login) =====
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

// ===== Authenticated routes =====
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        return view('home');
    })->name('dashboard');

    Route::get('/admin/dashboard', function () {
        return 'Admin dashboard';
    })->name('admin.dashboard');
});