<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\SeedlingRequestController;
use App\Http\Controllers\FishRController;
use App\Http\Controllers\BoatRController;
use App\Http\Controllers\InventoryController;

// ==============================================
// PUBLIC ROUTES
// ==============================================

// Landing page
Route::get('/', function () {
    return view('landingPage.landing');
})->name('landing.page');

// Application Form Submission Routes (Public)
Route::post('/apply/rsbsa', [ApplicationController::class, 'submitRsbsa'])->name('apply.rsbsa');
Route::post('/apply/seedlings', [ApplicationController::class, 'submitSeedlings'])->name('apply.seedlings');
Route::post('/apply/fishr', [ApplicationController::class, 'submitFishR'])->name('apply.fishr');
Route::post('/apply/boatr', [ApplicationController::class, 'submitBoatR'])->name('apply.boatr');

// Authentication Routes (Public)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==============================================
// ADMIN PROTECTED ROUTES
// ==============================================

Route::middleware('admin')->group(function () {
    // Dashboard
    Route::get('/admin/dashboard', [AuthController::class, 'dashboard'])->name('admin.dashboard');
    
    // ==============================================
    // FISHR REGISTRATIONS MANAGEMENT
    // ==============================================
    Route::get('/admin/fishr-registrations/export', [FishRController::class, 'export'])->name('admin.fishr.export');
    Route::get('/admin/fishr-registrations', [FishRController::class, 'index'])->name('admin.fishr.requests');
    Route::get('/admin/fishr-registrations/{id}', [FishRController::class, 'show'])->name('admin.fishr.show');
    Route::patch('/admin/fishr-registrations/{id}/status', [FishRController::class, 'updateStatus'])->name('admin.fishr.update-status');
    Route::delete('/admin/fishr-registrations/{id}', [FishRController::class, 'destroy'])->name('admin.fishr.destroy');
    Route::get('/admin/fishr-registrations/{id}/download', [FishRController::class, 'downloadDocument'])->name('admin.fishr.download-document');
    
    // ==============================================
    // BOATR REGISTRATIONS MANAGEMENT
    // ==============================================
    Route::get('/admin/boatr-applications/export', [BoatRController::class, 'export'])->name('admin.boatr.export');
    Route::get('/admin/boatr-applications', [BoatRController::class, 'index'])->name('admin.boatr.requests');
    Route::get('/admin/boatr-applications/{id}', [BoatRController::class, 'show'])->name('admin.boatr.show');
    Route::patch('/admin/boatr-applications/{id}/status', [BoatRController::class, 'updateStatus'])->name('admin.boatr.update-status');
    Route::post('/admin/boatr-applications/{id}/complete-inspection', [BoatRController::class, 'completeInspection'])->name('admin.boatr.complete-inspection');
    Route::get('/admin/boatr-applications/{id}/download', [BoatRController::class, 'downloadDocument'])->name('admin.boatr.download-document');
    
    // ==============================================
    // SEEDLING REQUESTS MANAGEMENT
    // ==============================================
    Route::get('/admin/seedling-requests', [SeedlingRequestController::class, 'index'])->name('admin.seedling.requests');
    Route::get('/admin/seedling-requests/{id}', [SeedlingRequestController::class, 'show'])->name('admin.seedling.show');
    Route::patch('/admin/seedling-requests/{id}/status', [SeedlingRequestController::class, 'updateStatus'])->name('admin.seedling.update-status');
    Route::delete('/admin/seedling-requests/{id}', [SeedlingRequestController::class, 'destroy'])->name('admin.seedling.destroy');
    Route::get('/admin/seedling-requests/export', [SeedlingRequestController::class, 'export'])->name('admin.seedling.export');
    
    // ==============================================
    // INVENTORY MANAGEMENT
    // ==============================================
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('inventory', InventoryController::class);
        Route::post('inventory/{inventory}/adjust-stock', [InventoryController::class, 'adjustStock'])->name('inventory.adjust-stock');
    });
    
    // ==============================================
    // ADMIN MANAGEMENT (SuperAdmin only)
    // ==============================================
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('admins', AdminController::class);
    });
    
    // ==============================================
    // REPORTS AND ANALYTICS
    // ==============================================
    Route::get('/admin/reports/fishr', function () {
        return view('admin.reports.fishr');
    })->name('admin.reports.fishr');
    
    Route::get('/admin/reports/boatr', function () {
        return view('admin.reports.boatr');
    })->name('admin.reports.boatr');
    
    Route::get('/admin/reports/seedlings', function () {
        return view('admin.reports.seedlings');
    })->name('admin.reports.seedlings');
});

// ==============================================
// FALLBACK ROUTE
// ==============================================
Route::fallback(function () {
    return redirect()->route('landing.page')->with('error', 'Page not found');
});