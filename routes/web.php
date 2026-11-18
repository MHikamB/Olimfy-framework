<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RegistrationController;
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

        // Pendaftaran Tim & Pembayaran (Ashar)
        Route::get('/daftar/{competition_id}', [RegistrationController::class, 'create'])->name('daftar.create');
        Route::post('/daftar', [RegistrationController::class, 'store'])->name('daftar.store');
        Route::get('/pendaftaran', [RegistrationController::class, 'index'])->name('pendaftaran.index');
        Route::get('/invoice/{id}', [PaymentController::class, 'show'])->name('invoice.show');
        Route::post('/invoice/{id}/bayar', [PaymentController::class, 'simulasiBayar'])->name('invoice.bayar');
    });
