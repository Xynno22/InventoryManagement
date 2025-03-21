<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Halaman Register dan Login
Route::view('/login', 'authentication.login');
Route::view('/', 'welcome');
Route::view('/register', 'authentication.register');

// Proses Registrasi dan Login
Route::post('/register-company', [CompanyController::class, 'register']);
Route::post('/login-company', [CompanyController::class, 'login']);
Route::post('/logout', function () {
    auth('company')->logout(); // <- Logout dari guard 'company'
    return redirect('/login');  // Redirect ke halaman login perusahaan atau halaman awal
})->name('logout');

Route::delete('/profile-destroy', [CompanyController::class, 'destroy'])->name('company.destroy');

/*
|--------------------------------------------------------------------------
| Email Verification Routes
|--------------------------------------------------------------------------
*/

// Halaman Notifikasi Verifikasi Email
Route::get('/email/verify', function () {
    return view('verification.verify-email');
})->middleware('auth:company')->name('verification.notice');

// Halaman Sukses Verifikasi
Route::get('/success-verify', function () {
    return view('verification.successVerify');
})->middleware('auth:company')->name('verification.success');

// Proses Verifikasi Email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/success-verify');
})->middleware(['auth:company', 'signed'])->name('verification.verify');

// Kirim Ulang Email Verifikasi
Route::post('/email/verification-notification', function () {
    Auth::guard('company')->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Verification link sent!');
})->middleware(['auth:company', 'throttle:6,1'])->name('verification.send');

/*
|--------------------------------------------------------------------------
| Dashboard Routes (Protected)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:company', 'verified'])->group(function () {
    Route::view('/dashboard', 'dashboard.dashboard');
});

/*
|--------------------------------------------------------------------------
| Password Reset Routes
|--------------------------------------------------------------------------
*/

Route::get('/forgot-password', [CompanyController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [CompanyController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [CompanyController::class, 'showResetForm'])->name('password.reset');
Route::post('/update-password', [CompanyController::class, 'reset'])->name('password.update');

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:company', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});