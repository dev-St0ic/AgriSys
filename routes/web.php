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
use App\Http\Controllers\UserRegistrationAnalyticsController;
use App\Http\Controllers\SupplyManagementAnalyticsController;
use App\Http\Controllers\UserRegistrationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SeedlingCategoryItemController;
use App\Http\Controllers\UserApplicationsController;
use App\Http\Controllers\DSSController;

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
    $validServices = ['rsbsa', 'seedlings', 'fishr', 'boatr', 'training'];
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

    // Seedling Requests Routes
// Route::prefix('admin/seedlings')->name('admin.seedlings.')->middleware(['auth'])->group(function () {
//     Route::get('/requests', [SeedlingRequestController::class, 'index'])->name('requests');
//     Route::get('/requests/{seedlingRequest}', [SeedlingRequestController::class, 'show'])->name('show');
//     Route::get('/seedlings/{seedlingRequest}/edit', [SeedlingRequestController::class, 'edit'])->name('admin.seedlings.edit');
//     Route::get('/seedlings/create', [SeedlingRequestController::class, 'create'])->name('admin.seedlings.create');
//     Route::delete('/seedlings/{seedlingRequest}', [SeedlingRequestController::class, 'destroy'])->name('admin.seedlings.destroy');
//     Route::patch('/requests/{seedlingRequest}/status', [SeedlingRequestController::class, 'updateStatus'])->name('update-status');
//     Route::patch('/requests/{seedlingRequest}/items', [SeedlingRequestController::class, 'updateItems'])->name('update-items');
//     Route::get('/requests/{seedlingRequest}/inventory-status', [SeedlingRequestController::class, 'getInventoryStatus'])->name('inventory-status');
//     Route::get('/category-stats', [SeedlingRequestController::class, 'getCategoryStats'])->name('category-stats');
// });




// Seedling Requests Routes
Route::prefix('admin/seedlings')->name('admin.seedlings.')->middleware(['auth'])->group(function () {
     Route::get('/requests', [SeedlingRequestController::class, 'index'])->name('requests');
    Route::get('/requests/create', [SeedlingRequestController::class, 'create'])->name('create');
    Route::post('/requests', [SeedlingRequestController::class, 'store'])->name('store');
    Route::get('/requests/{seedlingRequest}', [SeedlingRequestController::class, 'show'])->name('show');
    Route::get('/requests/{seedlingRequest}/edit', [SeedlingRequestController::class, 'edit'])->name('edit');
    Route::put('/requests/{seedlingRequest}', [SeedlingRequestController::class, 'update'])->name('update');
    Route::delete('/requests/{seedlingRequest}', [SeedlingRequestController::class, 'destroy'])->name('destroy');
    Route::patch('/requests/{seedlingRequest}/status', [SeedlingRequestController::class, 'updateStatus'])->name('update-status');
    Route::patch('/requests/{seedlingRequest}/items', [SeedlingRequestController::class, 'updateItems'])->name('update-items');
    Route::get('/requests/{seedlingRequest}/supply-status', [SeedlingRequestController::class, 'getSupplyStatus'])->name('supply-status');
    Route::get('/category-stats', [SeedlingRequestController::class, 'getCategoryStats'])->name('category-stats');

    // Category Management
    Route::get('/categories', [SeedlingCategoryItemController::class, 'indexCategories'])->name('categories.index');
    Route::get('/categories/{category}', [SeedlingCategoryItemController::class, 'showCategory'])->name('categories.show');
    Route::post('/categories', [SeedlingCategoryItemController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [SeedlingCategoryItemController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [SeedlingCategoryItemController::class, 'destroyCategory'])->name('categories.destroy');
    Route::post('/categories/{category}/toggle', [SeedlingCategoryItemController::class, 'toggleCategoryStatus'])->name('categories.toggle');

  // Item Management - PUT THE MORE SPECIFIC ROUTES FIRST
    Route::post('/items', [SeedlingCategoryItemController::class, 'storeItem'])->name('items.store');
    Route::post('/items/{item}/toggle', [SeedlingCategoryItemController::class, 'toggleItemStatus'])->name('items.toggle');
    Route::put('/items/{item}', [SeedlingCategoryItemController::class, 'updateItem'])->name('items.update');
    Route::get('/items/{item}', [SeedlingCategoryItemController::class, 'showItem'])->name('items.show');
    Route::delete('/items/{item}', [SeedlingCategoryItemController::class, 'destroyItem'])->name('items.destroy');

     // Stock Management
    // Route::post('/items/{item}/stock/add', [SeedlingCategoryItemController::class, 'addStock'])->name('items.stock.add');
    // Route::post('/items/{item}/stock/deduct', [SeedlingCategoryItemController::class, 'deductStock'])->name('items.stock.deduct');
    // Route::post('/items/{item}/stock/adjust', [SeedlingCategoryItemController::class, 'adjustStock'])->name('items.stock.adjust');
    // Route::get('/items/{item}/stock/logs', [SeedlingCategoryItemController::class, 'getStockLogs'])->name('items.stock.logs');
    // Route::post('/items/{item}/stock/check', [SeedlingCategoryItemController::class, 'checkStockAvailability'])->name('items.stock.check');
    // Route::get('/items/{item}/stock-history', [SeedlingCategoryItemController::class, 'getStockHistory'])->name('items.stock-history');

      // Supply Management Routes
    Route::post('/items/{item}/supply/add', [SeedlingCategoryItemController::class, 'addSupply'])->name('items.supply.add');
    Route::post('/items/{item}/supply/adjust', [SeedlingCategoryItemController::class, 'adjustSupply'])->name('items.supply.adjust');
    Route::post('/items/{item}/supply/loss', [SeedlingCategoryItemController::class, 'recordLoss'])->name('items.supply.loss');
    Route::get('/items/{item}/supply/logs', [SeedlingCategoryItemController::class, 'getSupplyLogs'])->name('items.supply.logs');
    Route::get('/supply/stats', [SeedlingCategoryItemController::class, 'getSupplyStats'])->name('supply.stats');
});

    // ==============================================
    // ANALYTICS ROUTES - SECTION
    // ==============================================
    Route::prefix('admin/analytics')->name('admin.analytics.')->group(function () {
        // SEEDLING ANALYTICS - EXISTING
        Route::get('/seedlings', [SeedlingAnalyticsController::class, 'index'])->name('seedlings');
        // Route::get('/seedlings/export', [SeedlingAnalyticsController::class, 'export'])->name('seedlings.export');


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

     // User Registration Analytics
    Route::get('/user-registration', [UserRegistrationAnalyticsController::class, 'index'])->name('user-registration');
    Route::get('/user-registration/export', [UserRegistrationAnalyticsController::class, 'export'])->name('user-registration.export');

     // User Registration Analytics
    Route::get('/supply-management', [SupplyManagementAnalyticsController::class, 'index'])->name('supply-management');
    Route::get('/supply-management/export', [SupplyManagementAnalyticsController::class, 'export'])->name('supply-management.export');
    });

    // ==============================================
    // DECISION SUPPORT SYSTEM (DSS)
    // ==============================================
    Route::prefix('admin/dss')->name('admin.dss.')->middleware(['auth'])->group(function () {
        Route::get('/preview', [DSSController::class, 'preview'])->name('preview');
        Route::get('/download-pdf', [DSSController::class, 'downloadPDF'])->name('download.pdf');
        Route::get('/download-word', [DSSController::class, 'downloadWord'])->name('download.word');
        Route::get('/refresh-data', [DSSController::class, 'refreshData'])->name('refresh.data');
        Route::get('/available-periods', [DSSController::class, 'getAvailablePeriods'])->name('available.periods');
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

// /*
// |--------------------------------------------------------------------------
// | Admin Routes (for managing user registrations)
// |--------------------------------------------------------------------------
// */
// Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {

//     // Main user management interface
//     Route::get('/users', [UserRegistrationController::class, 'index'])->name('registrations.index');

//     // Individual registration management
//     Route::get('/registrations/{id}/details', [UserRegistrationController::class, 'getRegistration'])->name('registrations.details');
//     Route::delete('/registrations/{id}', [UserRegistrationController::class, 'destroy'])->name('registrations.destroy');

//     // Status management
//     Route::post('/registrations/{id}/approve', [UserRegistrationController::class, 'approve'])->name('registrations.approve');
//     Route::post('/registrations/{id}/reject', [UserRegistrationController::class, 'reject'])->name('registrations.reject');

//     // Statistics and export
//     Route::get('/registrations/statistics', [UserRegistrationController::class, 'getStatistics'])->name('registrations.statistics');
//     Route::get('/registrations/export', [UserRegistrationController::class, 'export'])->name('registrations.export');

//     //view document
//     Route::get('/registrations/{id}/document/{type}', [UserRegistrationController::class, 'viewDocument'])->name('registrations.document');
// });


// // Register middleware alias for newer Laravel versions
// Route::aliasMiddleware('user.session', UserSession::class);

// /*
// |--------------------------------------------------------------------------
// | Main Landing Page Route
// |--------------------------------------------------------------------------
// */
// Route::get('/', function () {
//     $user = session('user', null);
//     return view('landingPage.landing', compact('user'));
// })->name('landing.page');

// /*
// |--------------------------------------------------------------------------
// | Authentication Routes
// |--------------------------------------------------------------------------
// // */
// // Route::post('/register', [UserRegistrationController::class, 'register'])->name('register');
// // Route::post('/login', [UserRegistrationController::class, 'login'])->name('login');
// // Route::post('/logout', [UserRegistrationController::class, 'logout'])->name('logout');
// // Route::post('/check-username', [UserRegistrationController::class, 'checkUsername'])->name('check.username');

// // // Auth prefixed routes (for your JavaScript calls)
// Route::prefix('auth')->group(function () {
//     Route::post('/register', [UserRegistrationController::class, 'register'])->name('auth.register');
//     Route::post('/login', [UserRegistrationController::class, 'login'])->name('auth.login');
//     Route::post('/logout', [UserRegistrationController::class, 'logout'])->name('auth.logout');
//     Route::post('/check-username', [UserRegistrationController::class, 'checkUsername'])->name('auth.check.username');
//     Route::post('/verify-profile', [UserRegistrationController::class, 'submitVerification'])->name('auth.verify.profile');
// });

// /*
// |--------------------------------------------------------------------------
// | User Dashboard Routes (Protected by UserSession middleware)
// |--------------------------------------------------------------------------
// */

// Route::middleware([App\Http\Middleware\UserSession::class])->group(function () {
//     // Main user dashboard
//     Route::get('/dashboard', function () {
//         $user = session('user', null);

//         if (!$user) {
//             return redirect('/')->with('error', 'Please log in to access this page.');
//         }

//         return view('landingPage.landing', compact('user'));
//     })->name('user.dashboard');

//     // User profile routes
//     Route::get('/profile', function () {
//         $user = session('user', null);
//         return view('user.profile', compact('user'));
//     })->name('user.profile');

//     // User applications history
//     Route::get('/my-applications', function () {
//         $user = session('user', null);
//         return view('user.applications', compact('user'));
//     })->name('user.applications');
// });


/*
|--------------------------------------------------------------------------
| Admin Routes (Enhanced for managing user registrations)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {

    // Main user management interface
    Route::get('/users', [UserRegistrationController::class, 'index'])->name('registrations.index');

    // Debug route to test authentication
    Route::get('/debug/auth', function () {
        return response()->json([
            'success' => true,
            'message' => 'Authentication working',
            'user' => auth()->user(),
            'timestamp' => now()->toISOString()
        ]);
    })->name('debug.auth');

    // Individual registration management
    Route::get('/registrations/{id}/details', [UserRegistrationController::class, 'getRegistration'])->name('registrations.details');
    Route::delete('/registrations/{id}', [UserRegistrationController::class, 'destroy'])->name('registrations.destroy');

    // Enhanced status management with auto-refresh
    Route::post('/registrations/{id}/approve', [UserRegistrationController::class, 'approve'])->name('registrations.approve');
    Route::post('/registrations/{id}/reject', [UserRegistrationController::class, 'reject'])->name('registrations.reject');
    Route::post('/registrations/{id}/update-status', [UserRegistrationController::class, 'updateStatus'])->name('registrations.update-status');

    // Enhanced document viewing - supports images and files
    Route::get('/registrations/{id}/document/{type}', [UserRegistrationController::class, 'viewDocument'])
        ->name('registrations.document')
        ->where('type', 'location|id_front|id_back');

    // Statistics and export
    Route::get('/registrations/statistics', [UserRegistrationController::class, 'getStatistics'])->name('registrations.statistics');
    Route::get('/registrations/export', [UserRegistrationController::class, 'export'])->name('registrations.export');

    // Bulk operations (optional future enhancement)
    Route::post('/registrations/bulk-approve', [UserRegistrationController::class, 'bulkApprove'])->name('registrations.bulk-approve');
    Route::post('/registrations/bulk-reject', [UserRegistrationController::class, 'bulkReject'])->name('registrations.bulk-reject');
});

// Register middleware alias for newer Laravel versions
Route::aliasMiddleware('user.session', UserSession::class);

/*
|--------------------------------------------------------------------------
| Main Landing Page Route
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $user = session('user', null);
    return view('landingPage.landing', compact('user'));
})->name('landing.page');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Enhanced for user registration system)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    // Basic authentication
    Route::post('/register', [UserRegistrationController::class, 'register'])->name('auth.register');
    Route::post('/login', [UserRegistrationController::class, 'login'])->name('auth.login');
    Route::post('/logout', [UserRegistrationController::class, 'logout'])->name('auth.logout');


    // Facebook Authentication
    Route::get('/facebook', [UserRegistrationController::class, 'redirectToFacebook'])->name('facebook.redirect');
    Route::get('/facebook/callback', [UserRegistrationController::class, 'handleFacebookCallback'])->name('facebook.callback');

    // Username availability checking
    Route::post('/check-username', [UserRegistrationController::class, 'checkUsername'])->name('auth.check.username');

    // UPDATED: Enhanced profile verification with file uploads - FIXED MIDDLEWARE AND ROUTE
    Route::post('/verify-profile', [UserRegistrationController::class, 'submitVerification'])
        ->middleware('web') // Use web middleware for session and CSRF protection
        ->name('auth.verify.profile');

    // Email verification routes (optional future enhancement)
    Route::get('/verify-email/{token}', [UserRegistrationController::class, 'verifyEmail'])->name('auth.verify.email');
    Route::post('/resend-verification', [UserRegistrationController::class, 'resendVerification'])->name('auth.resend.verification');

    // Add these to your routes file
    Route::post('/admin/users/{id}/ban', [UserRegistrationController::class, 'banUser']);
    Route::post('/admin/users/{id}/unban', [UserRegistrationController::class, 'unbanUser']);
    Route::post('/admin/users/bulk-ban', [UserRegistrationController::class, 'bulkBan']);

    // view document
    Route::get('/registrations/{id}/document/{type}', [UserRegistrationController::class, 'serveDocument'])
        ->name('registrations.document')
        ->where('type', 'location|id_front|id_back')
        ->middleware('auth');
});

/*
|--------------------------------------------------------------------------
| User Dashboard Routes (Protected by UserSession middleware)
|--------------------------------------------------------------------------
*/
Route::middleware([App\Http\Middleware\UserSession::class])->group(function () {
    // Main user dashboard
    Route::get('/dashboard', function () {
        $user = session('user', null);

        if (!$user) {
            return redirect('/')->with('error', 'Please log in to access this page.');
        }

        return view('landingPage.landing', compact('user'));
    })->name('user.dashboard');

    // User profile routes
    Route::get('/profile', function () {
        $user = session('user', null);
        return view('user.profile', compact('user'));
    })->name('user.profile');

    // User applications history
    Route::get('/my-applications', function () {
        $user = session('user', null);
        return view('user.applications', compact('user'));
    })->name('user.applications');

    // API endpoints for user data
    Route::prefix('api/user')->group(function () {
        Route::get('/profile', [UserRegistrationController::class, 'getUserProfile'])->name('api.user.profile');
        Route::get('/applications', [UserRegistrationController::class, 'getUserApplications'])->name('api.user.applications');
        Route::post('/update-profile', [UserRegistrationController::class, 'updateUserProfile'])->name('api.user.update-profile');

        // New endpoint to fetch all applications (RSBSA, Seedlings, FishR, BoatR, Training) in my applications modal
        Route::get('/applications/all', [UserApplicationsController::class, 'getAllApplications'])
            ->name('api.user.applications.all');

        // change pass word route
        Route::post('/change-password', [UserRegistrationController::class, 'changePassword'])->name('api.user.change-password');
    });
});

/*
|--------------------------------------------------------------------------
| Public API Routes (for AJAX calls)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    // Public information (no authentication required)
    Route::get('/registration-stats', [UserRegistrationController::class, 'getPublicStats'])->name('api.registration.stats');

    // Check system status
    Route::get('/system-status', function () {
        return response()->json([
            'success' => true,
            'status' => 'operational',
            'timestamp' => now()->toISOString(),
            'database' => 'connected'
        ]);
    })->name('api.system.status');
});

/*
|--------------------------------------------------------------------------
| Error Handling Routes
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

// Handle specific error codes
Route::get('/403', function () {
    return response()->view('errors.403', [], 403);
})->name('error.403');

Route::get('/500', function () {
    return response()->view('errors.500', [], 500);
})->name('error.500');

/*
|--------------------------------------------------------------------------
| Development/Testing Routes (Remove in production)
|--------------------------------------------------------------------------
*/
if (app()->environment('local', 'testing')) {
    Route::prefix('dev')->group(function () {
        // Test routes for development
        Route::get('/test-registration', function () {
            return view('dev.test-registration');
        });

        Route::get('/test-admin', function () {
            return view('dev.test-admin');
        });

        // Clear session for testing
        Route::get('/clear-session', function () {
            session()->flush();
            return redirect('/')->with('success', 'Session cleared for testing');
        });

        // ADDED: Test verification form (for development only)
        Route::get('/test-verification', function () {
            // Mock user session for testing
            session(['user' => [
                'id' => 1,
                'username' => 'testuser',
                'email' => 'test@example.com',
                'status' => 'unverified'
            ]]);

            return view('landingPage.landing')->with('user', session('user'));
        });
    });
}
// ==============================================
// FALLBACK ROUTE
// ==============================================
Route::fallback(function () {
    return redirect()->route('landing.page')->with('error', 'Page not found');
});
