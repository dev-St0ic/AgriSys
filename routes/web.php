<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\SeedlingRequestController;

// Redirect root to login
// Route::get('/', function () {
//     return redirect('/login');
// });

// Show landing page on root
Route::get('/', function () {
    return view('landingPage.landing');
})->name('landing.page');

// Application Form Submission Routes
Route::post('/apply/rsbsa', [ApplicationController::class, 'submitRsbsa'])->name('apply.rsbsa');
Route::post('/apply/seedlings', [ApplicationController::class, 'submitSeedlings'])->name('apply.seedlings');
Route::post('/apply/fishr', [ApplicationController::class, 'submitFishR'])->name('apply.fishr');
Route::post('/apply/boatr', [ApplicationController::class, 'submitBoatR'])->name('apply.boatr');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes (Protected by admin middleware)
Route::middleware('admin')->group(function () {
    Route::get('/admin/dashboard', [AuthController::class, 'dashboard'])->name('admin.dashboard');

    // Seedling Requests view
    Route::get('/admin/seedling-requests', [SeedlingRequestController::class, 'index'])->name('admin.seedling.requests');

    // Admin CRUD routes (only accessible by superadmin)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('admins', AdminController::class);
    });
});

// Seedling Requests Management
Route::get('/admin/seedling-requests', [SeedlingRequestController::class, 'index'])->name('admin.seedling.requests');
Route::get('/admin/seedling-requests/{seedlingRequest}', [SeedlingRequestController::class, 'show'])->name('admin.seedling.show');
Route::patch('/admin/seedling-requests/{seedlingRequest}/status', [SeedlingRequestController::class, 'updateStatus'])->name('admin.seedling.update-status');
