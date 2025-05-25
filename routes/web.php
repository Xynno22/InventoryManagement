<?php

use App\Models\StockOpname;
use App\Models\OperationalCost;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\CheckRolePermissions;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\OperationalCostController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ReportTransactionController;
use App\Http\Controllers\TransactionDetailController;
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
Route::view('/login', 'authentication.login')->name('login');
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
/*
|--------------------------------------------------------------------------
| Promo & Discount Routes
|--------------------------------------------------------------------------
*/
    Route::resource('promo', PromoController::class);
/*
|--------------------------------------------------------------------------
| Stock Routes
|--------------------------------------------------------------------------
*/
    Route::resource('stocks', StockController::class);

/*
|--------------------------------------------------------------------------
| Transaction & Transaction Details Routes
|--------------------------------------------------------------------------
*/
    Route::get('/transaction/{id}/export-pdf', [TransactionController::class, 'exportPdf'])->name('transaction.export-pdf');
    Route::resource('transaction', TransactionController::class);
    Route::resource('transactionDetails', TransactionDetailController::class);

    Route::get('/get-transaction-detail/{voucher_code}', [TransactionController::class, 'getTransactionDetail'])
        ->where('voucher_code', '.*'); // Mengizinkan semua karakter termasuk '/'
/*
|--------------------------------------------------------------------------
| Operational Cost Routes
|--------------------------------------------------------------------------
*/
    Route::resource('operational', OperationalCostController::class);
/*
|--------------------------------------------------------------------------
|Stock Opname Routes
|--------------------------------------------------------------------------
*/
    Route::resource('opname', StockOpnameController::class);
    Route::get('/getSystemStock', [StockOpnameController::class, 'getSystemStock']);

    Route::get('/reports/sales', [ReportTransactionController::class, 'salesReport'])->name('reports.sales');
    // Rute untuk meng-export PDF
    Route::get('/export-pdf', [ReportTransactionController::class, 'exportPDF'])->name('transactions.export.pdf');

/*
|--------------------------------------------------------------------------
| Note Routes
|--------------------------------------------------------------------------
*/

    Route::get('/note/{id}/download-pdf', [NoteController::class, 'downloadPDF'])->name('note.download-pdf');
    Route::get('/note/{note}/compare', [NoteController::class, 'compare'])->name('note.compare');
    Route::resource('note', NoteController::class);
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
