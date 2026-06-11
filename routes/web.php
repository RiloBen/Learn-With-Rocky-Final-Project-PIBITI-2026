<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // settings
    Route::get('/settings', [AuthController::class, 'showSettings'])->name('settings');
    Route::post('/settings/password', [AuthController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/theme', [AuthController::class, 'updateTheme'])->name('settings.theme');

    // dashboard (placeholder for Milestone 1)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
