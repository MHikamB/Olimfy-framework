<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\OrganizerDashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\CompetitionDetailController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\ParticipantDashboardController;
use App\Http\Controllers\ProfileController;

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
        Route::get('/dashboard', [OrganizerDashboardController::class, 'index'])->name('dashboard');

        // CRUD Lomba
        Route::resource('lomba', CompetitionController::class)
            ->except(['show'])
            ->parameters(['lomba' => 'id']);

        // Validasi berkas pendaftaran tim
        Route::get('/validasi', [VerificationController::class, 'index'])->name('validasi.index');
        Route::patch('/validasi/{id}', [VerificationController::class, 'updateStatus'])->name('validasi.update');
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

// ZONA 3: HIKAM (Publik & Profil Peserta)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/explore', [ExploreController::class, 'index'])->name('explore');
Route::get('/lomba/{id}', [CompetitionDetailController::class, 'show'])->name('lomba.detail');

Route::middleware(['auth'])->prefix('peserta')->group(function () {
    Route::get('/dashboard', [ParticipantDashboardController::class, 'index'])->name('peserta.dashboard');
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks/{id}/toggle', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('peserta.profil.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('peserta.profil.update');
});