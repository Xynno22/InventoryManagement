<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\CheckRolePermissions;
use App\Http\Controllers\ProductCategoryController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
/*
|--------------------------------------------------------------------------
| Home Page Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'layouts.welcome');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Halaman Register dan Login
Route::view('/login', 'authentication.login');
Route::view('/login-user', 'authentication.login-user');
Route::view('/register', 'authentication.register');

// Proses Registrasi dan Login
Route::post('/register-company', [CompanyController::class, 'register']);
Route::post('/login-company', [CompanyController::class, 'login']);
Route::post('/login-user', [CompanyController::class, 'loginUser']);
Route::post('/logout', function () {
    auth('company')->logout(); // <- Logout dari guard 'company'
    return redirect('/login');  // Redirect ke halaman login perusahaan atau halaman awal
})->name('logout');
Route::post('/logoutUser', function () {
    auth('web')->logout(); // <- Logout dari guard 'company'
    return redirect('/login');  // Redirect ke halaman login perusahaan atau halaman awal
})->name('logoutUser');



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
| Password Reset Routes
|--------------------------------------------------------------------------
*/

Route::get('/forgot-password', [CompanyController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [CompanyController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [CompanyController::class, 'showResetForm'])->name('password.reset');
Route::post('/update-password', [CompanyController::class, 'reset'])->name('password.update');



Route::middleware(['auth:company,web', CheckRolePermissions::class])->group(function () {
/*
|--------------------------------------------------------------------------
| Dashboard Routes (Protected)
|--------------------------------------------------------------------------
*/
    Route::view('/dashboard', 'dashboard.dashboard');
    


/*
|--------------------------------------------------------------------------
| Product Category Routes
|--------------------------------------------------------------------------
*/

    Route::resource('categories', ProductCategoryController::class);

/*
|--------------------------------------------------------------------------
| Product Category Routes
|--------------------------------------------------------------------------
*/

    Route::resource('products', ProductController::class);

});


Route::middleware(['auth:company'])->group(function () {
    
    Route::delete('/profile-destroy', [CompanyController::class, 'destroy'])->name('company.destroy');

    /*
    |--------------------------------------------------------------------------
    | Profile Routes
    |--------------------------------------------------------------------------
    */
    
        Route::get('/profile', [ProfileController::class, 'profile'])->name('profile.index');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    
    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
        Route::put('/admin/{id}/update-role', [AdminController::class, 'updateRole'])->name('admin.updateRole');
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
        Route::post('/admin', [AdminController::class, 'store'])->name('admin.store');
        Route::delete('/admin/{admin}', [AdminController::class, 'destroy'])->name('admin.destroy');
    /*
    |--------------------------------------------------------------------------
    | Role Routes
    |--------------------------------------------------------------------------
    */
        Route::resource('roles', RoleController::class);
        Route::get('/roles/{role}/permissions', [RoleController::class, 'editPermissions'])->name('roles.permissions.edit');
        Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
});