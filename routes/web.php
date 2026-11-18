<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Autentikasi
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Area Penyelenggara
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:penyelenggara'])
    ->prefix('penyelenggara')
    ->name('penyelenggara.')
    ->group(function () {
        Route::view('/dashboard', 'penyelenggara.dashboard')->name('dashboard');
    });

/*
|--------------------------------------------------------------------------
| Area Peserta
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:peserta'])
    ->prefix('peserta')
    ->name('peserta.')
    ->group(function () {
        Route::view('/dashboard', 'peserta.dashboard')->name('dashboard');
    });
