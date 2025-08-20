<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\SeedlingRequestController;
use App\Http\Controllers\FishRController;
use App\Http\Controllers\BoatRController;
use App\Http\Controllers\RsbsaController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SeedlingAnalyticsController;
use App\Http\Controllers\FishrAnalyticsController;
use App\Http\Controllers\BoatrAnalyticsController;
use App\Http\Controllers\RsbsaAnalyticsController;
use App\Http\Controllers\TrainingAnalyticsController;
use App\Http\Controllers\InventoryAnalyticsController;

// ==============================================
// PUBLIC ROUTES
// ==============================================

// Landing page
Route::get('/', function () {
    return view('landingPage.landing');
})->name('landing.page');

// CSRF Token refresh route
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
})->name('csrf.token');

// Application Form Submission Routes (Public)
Route::post('/apply/rsbsa', [ApplicationController::class, 'submitRsbsa'])->name('apply.rsbsa');
Route::post('/apply/seedlings', [ApplicationController::class, 'submitSeedlings'])->name('apply.seedlings');
Route::post('/apply/fishr', [ApplicationController::class, 'submitFishR'])->name('apply.fishr');
Route::post('/apply/boatr', [ApplicationController::class, 'submitBoatR'])->name('apply.boatr');
Route::post('/apply/training', [ApplicationController::class, 'submitTraining'])->name('apply.training');

// MAIN BoatR submission route (matches JavaScript call)
Route::post('/submit-boatr', [ApplicationController::class, 'submitBoatR'])->name('submit.boatr');

// Authentication Routes (Public)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Service routes for frontend navigation
Route::get('/services', function () {
    return view('landingPage.landing');
})->name('services');

Route::get('/services/{service}', function ($service) {
    $validServices = ['rsbsa', 'seedlings', 'fishr', 'boatr'];
    if (in_array($service, $validServices)) {
        return view('landingPage.landing');
    }
    return redirect()->route('landing.page');
})->name('services.show');

// API Routes for validation
Route::get('/api/validate-fishr/{number}', function($number) {
    try {
        $valid = \App\Models\FishrApplication::where('registration_number', $number)
            ->where('status', 'approved')
            ->exists();
        
        return response()->json([
            'valid' => $valid,
            'message' => $valid ? 'Valid FishR registration' : 'Invalid or non-approved FishR registration'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'valid' => false,
            'message' => 'Error validating FishR number'
        ], 500);
    }
})->name('api.validate-fishr');

// ==============================================
// ADMIN PROTECTED ROUTES
// ==============================================

Route::middleware('admin')->group(function () {
    // Dashboard
    Route::get('/admin/dashboard', [AuthController::class, 'dashboard'])->name('admin.dashboard');
    
    // ==============================================
    // RSBSA APPLICATIONS MANAGEMENT
    // ==============================================
    Route::prefix('admin/rsbsa-applications')->name('admin.rsbsa.')->group(function () {
        Route::get('/', [RsbsaController::class, 'index'])->name('applications');
        Route::get('/{id}', [RsbsaController::class, 'show'])->name('show');
        Route::patch('/{id}/status', [RsbsaController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{id}', [RsbsaController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/download', [RsbsaController::class, 'downloadDocument'])->name('download-document');
        Route::get('/export', [RsbsaController::class, 'export'])->name('export');
    });
    
    // ==============================================
    // FISHR REGISTRATIONS MANAGEMENT
    // ==============================================
    Route::prefix('admin/fishr-registrations')->name('admin.fishr.')->group(function () {
        Route::get('/', [FishRController::class, 'index'])->name('requests');
        Route::get('/{id}', [FishRController::class, 'show'])->name('show');
        Route::patch('/{id}/status', [FishRController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{id}', [FishRController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/download', [FishRController::class, 'downloadDocument'])->name('download-document');
        Route::get('/export', [FishRController::class, 'export'])->name('export');
        Route::post('/{id}/assign-fishr-number', [FishRController::class, 'assignFishRNumber'])
            ->name('assign-fishr-number');
    });
    
    // ==============================================
    // BOATR REGISTRATIONS MANAGEMENT - COMPLETE AND FIXED
    // ==============================================
    Route::prefix('admin/boatr')->name('admin.boatr.')->group(function () {
        // Main listing page
        Route::get('/requests', [BoatRController::class, 'index'])->name('requests');
        
        // Individual application routes
        Route::get('/requests/{id}', [BoatRController::class, 'show'])->name('show');
        Route::patch('/requests/{id}/status', [BoatRController::class, 'updateStatus'])->name('update-status');
        Route::post('/requests/{id}/complete-inspection', [BoatRController::class, 'completeInspection'])->name('complete-inspection');
        Route::delete('/requests/{id}', [BoatRController::class, 'destroy'])->name('destroy');
        
        // Document viewing routes - FIXED AND COMPLETE
        Route::get('/requests/{id}/view-document', [BoatRController::class, 'viewDocument'])->name('view-document');
        Route::post('/requests/{id}/document-preview', [BoatRController::class, 'documentPreview'])->name('document-preview');
        Route::get('/requests/{id}/download-document', [BoatRController::class, 'downloadDocument'])->name('download-document');
        
        // Export functionality
        Route::get('/export', [BoatRController::class, 'export'])->name('export');
    });

    // ==============================================
    // TRAINING REGISTRATIONS MANAGEMENT 
    // ==============================================
    Route::prefix('admin/training')->name('admin.training.')->group(function () {
        
        // Training Applications Management
        Route::get('/requests', [TrainingController::class, 'index'])->name('requests');
        Route::get('/requests/{id}', [TrainingController::class, 'show'])->name('requests.show');
        Route::patch('/requests/{id}/status', [TrainingController::class, 'updateStatus'])->name('requests.update-status');
        Route::delete('/requests/{id}', [TrainingController::class, 'destroy'])->name('requests.destroy');
        
        
    });

    // ==============================================
    // SEEDLING REQUESTS MANAGEMENT
    // ==============================================
    Route::prefix('admin/seedling-requests')->name('admin.seedling.')->group(function () {
        Route::get('/', [SeedlingRequestController::class, 'index'])->name('requests');
        Route::get('/{seedlingRequest}', [SeedlingRequestController::class, 'show'])->name('show');
        Route::patch('/{seedlingRequest}/status', [SeedlingRequestController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{seedlingRequest}', [SeedlingRequestController::class, 'destroy'])->name('destroy');
        Route::get('/export', [SeedlingRequestController::class, 'export'])->name('export');
    });
    // ==============================================
    // ANALYTICS ROUTES - SECTION
    // ==============================================
    Route::prefix('admin/analytics')->name('admin.analytics.')->group(function () {
        // SEEDLING ANALYTICS - EXISTING
        Route::get('/seedlings', [SeedlingAnalyticsController::class, 'index'])->name('seedlings');
        Route::get('/seedlings/export', [SeedlingAnalyticsController::class, 'export'])->name('seedlings.export');

         // RSBSA ANALYTICS - NEW SECTION
    Route::get('/rsbsa', [RsbsaAnalyticsController::class, 'index'])->name('rsbsa');
    Route::get('/rsbsa/export', [RsbsaAnalyticsController::class, 'export'])->name('rsbsa.export');
        
    // FISHR ANALYTICS - NEW SECTION
    Route::get('/fishr', [FishrAnalyticsController::class, 'index'])->name('fishr');
    Route::get('/fishr/export', [FishrAnalyticsController::class, 'export'])->name('fishr.export');

    // BOATR ANALYTICS - NEW SECTION
    Route::get('/boatr', [BoatrAnalyticsController::class, 'index'])->name('boatr');
    Route::get('/boatr/export', [BoatrAnalyticsController::class, 'export'])->name('boatr.export');

    // TRAINING ANALYTICS - NEW SECTION
    Route::get('/training', [TrainingAnalyticsController::class, 'index'])->name('training');
    Route::get('/training/export', [TrainingAnalyticsController::class, 'export'])->name('training.export');

    // INVENTORY ANALYTICS - NEW SECTION
    Route::get('/inventory', [InventoryAnalyticsController::class, 'index'])->name('inventory');
    Route::get('/inventory/export', [InventoryAnalyticsController::class, 'export'])->name('inventory.export');
    });

    
    
    // ==============================================
    // INVENTORY MANAGEMENT
    // ==============================================
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('inventory', InventoryController::class);
        Route::post('inventory/{inventory}/adjust-stock', [InventoryController::class, 'adjustStock'])
            ->name('inventory.adjust-stock');
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
    Route::get('/admin/reports/rsbsa', function () {
        return view('admin.reports.rsbsa');
    })->name('admin.reports.rsbsa');
    
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
// API ROUTES FOR AJAX REQUESTS
// ==============================================
Route::middleware('admin')->prefix('api/admin')->group(function () {
    // RSBSA API routes
    Route::prefix('rsbsa')->name('api.admin.rsbsa.')->group(function () {
        Route::get('/applications', [RsbsaController::class, 'index'])->name('applications');
        Route::get('/applications/{id}', [RsbsaController::class, 'show'])->name('applications.show');
        Route::patch('/applications/{id}/status', [RsbsaController::class, 'updateStatus'])->name('applications.update-status');
        Route::delete('/applications/{id}', [RsbsaController::class, 'destroy'])->name('applications.destroy');
    });
    
    // FishR API routes
    Route::prefix('fishr')->name('api.admin.fishr.')->group(function () {
        Route::get('/registrations', [FishRController::class, 'index'])->name('registrations');
        Route::get('/registrations/{id}', [FishRController::class, 'show'])->name('registrations.show');
        Route::patch('/registrations/{id}/status', [FishRController::class, 'updateStatus'])->name('registrations.update-status');
        Route::delete('/registrations/{id}', [FishRController::class, 'destroy'])->name('registrations.destroy');
    });
    
    // BoatR API routes
    Route::prefix('boatr')->name('api.admin.boatr.')->group(function () {
        Route::get('/applications', [BoatRController::class, 'index'])->name('applications');
        Route::get('/applications/{id}', [BoatRController::class, 'show'])->name('applications.show');
        Route::patch('/applications/{id}/status', [BoatRController::class, 'updateStatus'])->name('applications.update-status');
        Route::delete('/applications/{id}', [BoatRController::class, 'destroy'])->name('applications.destroy');
    });
});

// ==============================================
// DEBUGGING ROUTES (Remove in production)
// ==============================================
if (config('app.debug')) {
    Route::get('/debug/routes', function () {
        $routes = collect(Route::getRoutes())->map(function ($route) {
            return [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'controller' => $route->getActionName(),
            ];
        });
        
        return response()->json($routes->toArray());
    })->name('debug.routes');
    
    // Test RSBSA submission route
    Route::get('/debug/test-rsbsa', function () {
        return response()->json([
            'message' => 'RSBSA submission route is working',
            'routes' => [
                'POST /apply/rsbsa' => route('apply.rsbsa'),
            ],
            'csrf_token' => csrf_token()
        ]);
    })->name('debug.test-rsbsa');
    
    // Test BoatR submission route
    Route::get('/debug/test-boatr', function () {
        return response()->json([
            'message' => 'BoatR submission route is working',
            'routes' => [
                'POST /apply/boatr' => route('apply.boatr'),
                'POST /submit-boatr' => route('submit.boatr'),
            ],
            'csrf_token' => csrf_token()
        ]);
    })->name('debug.test-boatr');
}

// ==============================================
// FALLBACK ROUTE
// ==============================================
Route::fallback(function () {
    return redirect()->route('landing.page')->with('error', 'Page not found');
});