<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

// Redirect root to login
// Route::get('/', function () {
//     return redirect('/login');
// });

// Show landing page on root
Route::get('/', function () {
    return view('landingPage.landing');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes (Protected by admin middleware)
Route::middleware('admin')->group(function () {
    Route::get('/admin/dashboard', [AuthController::class, 'dashboard'])->name('admin.dashboard');

    // Admin CRUD routes (only accessible by superadmin)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('admins', AdminController::class);
    });
});
