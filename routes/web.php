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
use App\Http\Controllers\SeedlingAnalyticsController;
use App\Http\Controllers\FishrAnalyticsController;
use App\Http\Controllers\BoatrAnalyticsController;
use App\Http\Controllers\RsbsaAnalyticsController;
use App\Http\Controllers\TrainingAnalyticsController;
use App\Http\Controllers\UserRegistrationAnalyticsController;
use App\Http\Controllers\SupplyManagementAnalyticsController;
use App\Http\Controllers\UserRegistrationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\SeedlingCategoryItemController;
use App\Http\Controllers\UserApplicationsController;
use App\Http\Controllers\DSSController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ActivityLogApiController;
use App\Http\Controllers\SlideshowController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\RecycleBinController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// ==============================================
// PUBLIC ROUTES
// ==============================================

// Landing page
Route::get('/', [HomeController::class, 'index'])->name('landing.page');

// CSRF Token refresh route
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
})->name('csrf.token');

// download barangay cert
Route::get('/download/{file}', function($file) {
    // Whitelist allowed files (security measure)
    $allowedFiles = [
        'Barangay-Certification'

    ];

    // Check if file is in whitelist
    if (!in_array($file, $allowedFiles)) {
        abort(403, 'Unauthorized file');
    }

    $filePath = storage_path('app/public/documents/' . $file . '.pdf');

    if (!file_exists($filePath)) {
        abort(404, 'File not found');
    }

    return response()->download($filePath);
})->name('download');

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

// Email Verification Routes (NO AUTH REQUIRED)
Route::get('/email/verify', function () {
    // If user is logged in AND email is verified
    if (auth()->check() && auth()->user()->hasVerifiedEmail()) {
        // AUTO-REDIRECT (page closes)
        return redirect('/login')
            ->with('info', 'Your email is already verified.');
    }
    
    // If email not verified yet
    // Show the page (page stays open)
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function ($id, $hash, Request $request) {
    $user = \App\Models\User::findOrFail($id);
    
    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        throw new \Illuminate\Auth\Access\AuthorizationException();
    }
    
    if ($user->markEmailAsVerified()) {
        event(new \Illuminate\Auth\Events\Verified($user));
    }
    
    return redirect('/login')->with('success', 'Email verified successfully! You can now login.');
})->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

Route::post('/email/verification-notification', function () {
    auth()->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Password Change Verification Route (PUBLIC - no auth required)
Route::get('/password/change/verify/{user}', [AdminProfileController::class, 'verifyPasswordChange'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('password.change.verify');

// Email Change Confirmation Route (PUBLIC - no auth required)
Route::get('/email/change/confirm/{user}', [AdminProfileController::class, 'confirmEmailChange'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('email.change.confirm');

// SuperAdmin routes - NO verification required
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');
    Route::resource('admins', AdminController::class);
    Route::post('admins/{admin}/resend-verification', [AdminController::class, 'resendVerificationEmail'])
        ->name('admins.resend-verification');
});


// Service routes for frontend navigation
Route::get('/services', [HomeController::class, 'index'])->name('services');

Route::get('/services/{service}', function ($service) {
    $validServices = ['rsbsa', 'seedlings', 'fishr', 'boatr', 'training'];
    if (in_array($service, $validServices)) {
        return app(HomeController::class)->index();
    }
    return redirect()->route('landing.page');
})->name('services.show');

// validte
Route::get('/validate-fishr/{fishrNumber}', [BoatRController::class, 'validateFishrNumber'])
    ->where('fishrNumber', '.*')
    ->name('validate.fishr');
        // ==============================================
        // ADMIN PROTECTED ROUTES
        // ==============================================

    Route::middleware('admin')->group(function () {
        // Dashboard
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');

    // edit admin profile

    Route::get('/admin/profile/edit', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::put('/admin/profile/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    Route::delete('/admin/profile/photo', [AdminProfileController::class, 'deletePhoto'])->name('admin.profile.deletePhoto');

     Route::resource('admins', AdminController::class);
    Route::post('admins/{admin}/resend-verification', [AdminController::class, 'resendVerificationEmail'])
        ->name('admins.resend-verification');

    // ==============================================
    // RSBSA APPLICATIONS MANAGEMENT
    // ==============================================
    Route::prefix('admin/rsbsa-applications')->name('admin.rsbsa.')->group(function () {
        // List applications
    Route::get('/', [RsbsaController::class, 'index'])->name('applications');

    // Export applications (must be before /{id} to avoid conflict)
    Route::get('/export', [RsbsaController::class, 'export'])->name('export');

    // Create new registration
    Route::post('/create', [RsbsaController::class, 'store'])->name('store');

    // View single application
    Route::get('/{id}', [RsbsaController::class, 'show'])->name('show');

    // Update application (personal info) - PUT method
    Route::put('/{id}', [RsbsaController::class, 'update'])->name('update');

    // Update status only - PATCH method (different from PUT)
    Route::patch('/{id}/status', [RsbsaController::class, 'updateStatus'])->name('update-status');

    // Delete application
    Route::delete('/{id}', [RsbsaController::class, 'destroy'])->name('destroy');

    // Download document
    Route::get('/{id}/download', [RsbsaController::class, 'downloadDocument'])->name('download-document');
    });

    // ==============================================
    // FISHR REGISTRATIONS MANAGEMENT
    // ==============================================
    Route::prefix('admin/fishr-registrations')->name('admin.fishr.')->group(function () {
       // Static routes FIRST
        Route::get('/export', [FishRController::class, 'export'])->name('export');

        // create
        Route::post('/create', [FishRController::class, 'store'])->name('store');

        // DELETE route BEFORE GET/{id}
        Route::delete('/{id}', [FishRController::class, 'destroy'])->name('destroy');

        // edit
        Route::put('/{id}', [FishRController::class, 'update'])->name('update');

        // Index route
        Route::get('/', [FishRController::class, 'index'])->name('requests');

        // GET by ID
        Route::get('/{id}', [FishRController::class, 'show'])->name('show');

        // Other routes
        Route::patch('/{id}/status', [FishRController::class, 'updateStatus'])->name('update-status');
        Route::get('/{id}/download', [FishRController::class, 'downloadDocument'])->name('download-document');
        Route::post('/{id}/assign-fishr-number', [FishRController::class, 'assignFishRNumber'])
            ->name('assign-fishr-number');



        // Annexes routes
        Route::get('/{id}/annexes', [FishRController::class, 'getAnnexes'])->name('annexes.index');
        Route::post('/{id}/annexes', [FishRController::class, 'uploadAnnex'])->name('annexes.upload');
        Route::get('/{id}/annexes/{annexId}/preview', [FishRController::class, 'previewAnnex'])->name('annexes.preview');
        Route::get('/{id}/annexes/{annexId}/download', [FishRController::class, 'downloadAnnex'])->name('annexes.download');
        Route::delete('/{id}/annexes/{annexId}', [FishRController::class, 'deleteAnnex'])->name('annexes.delete');
    });

    // ==============================================
    // BOATR REGISTRATIONS MANAGEMENT - COMPLETE AND FIXED
    // ==============================================
    Route::prefix('admin/boatr')->name('admin.boatr.')->group(function () {

        // Main listing page
        Route::get('/requests', [BoatRController::class, 'index'])->name('requests');

        // Individual application routes
        Route::get('/requests/{id}', [BoatRController::class, 'show'])->name('show');

        // Add registration
        Route::post('/requests/create', [BoatRController::class, 'store'])->name('store');
        // edit
         Route::put('/requests/{id}', [BoatRController::class, 'update'])->name('update');
        // UPDATE STATUS
        Route::patch('/requests/{id}/status', [BoatRController::class, 'updateStatus'])
            ->name('status.update');

        Route::post('/requests/{id}/complete-inspection', [BoatRController::class, 'completeInspection'])
            ->name('complete-inspection');

        Route::delete('/requests/{id}', [BoatRController::class, 'destroy'])->name('destroy');


        // Document viewing routes
        Route::get('/requests/{id}/view-document', [BoatRController::class, 'viewDocument'])
            ->name('view-document');
        Route::post('/requests/{id}/document-preview', [BoatRController::class, 'documentPreview'])
            ->name('document-preview');
        Route::get('/requests/{id}/download-document', [BoatRController::class, 'downloadDocument'])
            ->name('download-document');

        // Annexes routes
        Route::get('/requests/{id}/annexes', [BoatRController::class, 'getAnnexes'])
            ->name('annexes.index');
        Route::post('/requests/{id}/annexes', [BoatRController::class, 'uploadAnnex'])
            ->name('annexes.upload');
        Route::get('/requests/{id}/annexes/{annexId}/preview', [BoatRController::class, 'previewAnnex'])
            ->name('annexes.preview');
        Route::get('/requests/{id}/annexes/{annexId}/download', [BoatRController::class, 'downloadAnnex'])
            ->name('annexes.download');
        Route::delete('/requests/{id}/annexes/{annexId}', [BoatRController::class, 'deleteAnnex'])
            ->name('annexes.delete');

        // Export functionality
        Route::get('/export', [BoatRController::class, 'export'])->name('export');

        // // FishR Validation
        // Route::get('/validate-fishr/{fishrNumber}', [BoatRController::class, 'validateFishrNumber'])
        // ->name('validate-fishr')
        // ->where('fishrNumber', '.*'); // Allow special characters like dashes
    });
   // ✅ FishR validation for BoatR form (AUTHENTICATED USERS ONLY)
Route::middleware('auth')->get('/validate-fishr/{fishrNumber}', [BoatRController::class, 'validateFishrNumber'])
    ->where('fishrNumber', '.*')
    ->name('validate.fishr');

    // ==============================================
    // TRAINING REGISTRATIONS MANAGEMENT
    // ==============================================
    Route::prefix('admin/training')->name('admin.training.')->group(function () {

        // Training Applications Management
        Route::get('/requests', [TrainingController::class, 'index'])->name('requests');
        Route::get('/requests/{id}', [TrainingController::class, 'show'])->name('requests.show');
        // add
        Route::post('/requests/create', [TrainingController::class, 'store'])->name('requests.store');
        // edit
        Route::put('/requests/{id}', [TrainingController::class, 'update'])->name('requests.update');

        Route::patch('/requests/{id}/status', [TrainingController::class, 'updateStatus'])->name('requests.update-status');
        Route::delete('/requests/{id}', [TrainingController::class, 'destroy'])->name('requests.destroy');

        // Export functionality
        Route::get('/export', [TrainingController::class, 'export'])->name('export');

    });

  // ==============================================
    // EVENT MANAGEMENT
    // ==============================================
    Route::prefix('admin/events')->name('admin.event.')->group(function () {
        // General routes first
        Route::get('/', [EventController::class, 'index'])->name('index');
        Route::post('/', [EventController::class, 'store'])->name('store');

        // Special/Specific routes BEFORE generic {id} routes
        Route::get('/management/archived', [EventController::class, 'archivedEvents'])->name('archived');
        Route::get('/statistics/all', [EventController::class, 'getStatistics'])->name('statistics');

        // Generic routes with {event} parameter LAST
        Route::get('/{event}', [EventController::class, 'show'])->name('show');
        Route::match(['put', 'patch'], '/{event}', [EventController::class, 'update'])->name('update');
        Route::post('/{event}/update', [EventController::class, 'update'])->name('update.post');
        Route::post('/{event}/archive', [EventController::class, 'archive'])->name('archive');
        Route::post('/{event}/unarchive', [EventController::class, 'unarchive'])->name('unarchive');
        Route::delete('/{event}', [EventController::class, 'destroy'])->name('destroy');
        Route::patch('/{event}/toggle-status', [EventController::class, 'toggleStatus'])->name('toggle');
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


// Seedling Requests Routes
Route::prefix('admin/seedlings')->name('admin.seedlings.')->middleware(['auth'])->group(function () {
     Route::get('/requests', [SeedlingRequestController::class, 'index'])->name('requests');
    Route::get('/requests/create', [SeedlingRequestController::class, 'create'])->name('create');
    Route::post('/requests', [SeedlingRequestController::class, 'store'])->name('store');
    // Route::get('/requests/{seedlingRequest}', [SeedlingRequestController::class, 'show'])->name('show');

    // In your admin routes group
    Route::patch('/requests/{seedlingRequest}/mark-claimed',
        [SeedlingRequestController::class, 'markAsClaimed'])
        ->name('mark-claimed');
    // export csv
    Route::get('/requests/export', [SeedlingRequestController::class, 'export'])->name('export');

    Route::get('/requests/{seedlingRequest}/edit', [SeedlingRequestController::class, 'edit'])->name('edit');
    Route::put('/requests/{seedlingRequest}', [SeedlingRequestController::class, 'update'])->name('update');
    Route::delete('/requests/{seedlingRequest}', [SeedlingRequestController::class, 'destroy'])->name('destroy');
    Route::patch('/requests/{seedlingRequest}/status', [SeedlingRequestController::class, 'updateStatus'])->name('update-status');
    Route::patch('/requests/{seedlingRequest}/items', [SeedlingRequestController::class, 'updateItems'])->name('update-items');
    Route::get('/requests/{seedlingRequest}/supply-status', [SeedlingRequestController::class, 'getSupplyStatus'])->name('supply-status');
    Route::get('/category-stats', [SeedlingRequestController::class, 'getCategoryStats'])->name('category-stats');

    // Supply Management
    Route::get('/supply-management', [SeedlingCategoryItemController::class, 'indexCategories'])->name('supply-management.index');
    Route::get('/supply-management/{category}', [SeedlingCategoryItemController::class, 'showCategory'])->name('supply-management.show');
    Route::post('/supply-management', [SeedlingCategoryItemController::class, 'storeCategory'])->name('supply-management.store');
    Route::put('/supply-management/{category}', [SeedlingCategoryItemController::class, 'updateCategory'])->name('supply-management.update');
    Route::delete('/supply-management/{category}', [SeedlingCategoryItemController::class, 'destroyCategory'])->name('supply-management.destroy');
    Route::post('/supply-management/{category}/toggle', [SeedlingCategoryItemController::class, 'toggleCategoryStatus'])->name('supply-management.toggle');

  // Item Management - PUT THE MORE SPECIFIC ROUTES FIRST
    Route::get('/items', [SeedlingCategoryItemController::class, 'indexCategories'])->name('supply-management.items');
    Route::post('/items', [SeedlingCategoryItemController::class, 'storeItem'])->name('items.store');
    Route::post('/items/{item}/toggle', [SeedlingCategoryItemController::class, 'toggleItemStatus'])->name('items.toggle');
    Route::put('/items/{item}', [SeedlingCategoryItemController::class, 'updateItem'])->name('items.update');
    Route::get('/items/{item}', [SeedlingCategoryItemController::class, 'showItem'])->name('items.show');
    Route::delete('/items/{item}', [SeedlingCategoryItemController::class, 'destroyItem'])->name('items.destroy');

    Route::post('/seedlings/stock-status', [SeedlingCategoryItemController::class, 'getStockStatus']);
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
    // NOTIFICATION ROUTES
    // ==============================================
Route::prefix('admin/notifications')->name('admin.notifications.')->middleware(['auth'])->group(function () {
    // Get unread count for badge
    Route::get('/unread-count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])
        ->name('unread-count');

    // Get unread notifications for dropdown (max 10)
    Route::get('/unread', [App\Http\Controllers\NotificationController::class, 'unread'])
        ->name('unread');

    // Get ALL notifications with pagination and filtering (for dedicated page)
    Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])
        ->name('index');

    // Mark single notification as read (doesn't delete)
    Route::post('/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])
        ->name('mark-read');

    // Mark all notifications as read (doesn't delete)
    Route::post('/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])
        ->name('mark-all-read');

    // Delete single notification
    Route::delete('/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])
        ->name('destroy');

    // Clear/delete all read notifications
    Route::delete('/clear-read', [App\Http\Controllers\NotificationController::class, 'clearRead'])
        ->name('clear-read');

    // Delete all notifications
    Route::delete('/clear-all', [App\Http\Controllers\NotificationController::class, 'clearAll'])
        ->name('clear-all');
});

 Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Activity Logs
    Route::get('activity-logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/{id}', [App\Http\Controllers\ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::get('activity-logs/export/csv', [App\Http\Controllers\ActivityLogController::class, 'export'])->name('activity-logs.export');

});


    // ==============================================
    // SLIDESHOW MANAGEMENT
    // ==============================================
    Route::prefix('slideshow')->name('admin.slideshow.')->group(function () {
        Route::get('/', [SlideshowController::class, 'index'])->name('index');
        Route::post('/', [SlideshowController::class, 'store'])->name('store');
        Route::put('/{id}', [SlideshowController::class, 'update'])->name('update');
        Route::delete('/{id}', [SlideshowController::class, 'destroy'])->name('destroy');
        Route::post('/update-order', [SlideshowController::class, 'updateOrder'])->name('update-order');
        Route::post('/{id}/toggle-status', [SlideshowController::class, 'toggleStatus'])->name('toggle-status');
    });
    // ==============================================
    // ANALYTICS ROUTES - SECTION
    // ==============================================
    Route::prefix('admin/analytics')->name('admin.analytics.')->middleware(['auth', 'admin'])->group(function () {
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


/*
|--------------------------------------------------------------------------
| Admin Routes (Enhanced for managing user registrations)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {

    // Main user management interface
    Route::get('/users', [UserRegistrationController::class, 'index'])->name('registrations.index');

    // Create new user account
    Route::post('/registrations/create', [UserRegistrationController::class, 'createUser'])->name('admin.registrations.create');
    // edit user account
    // Update with session sync
    Route::put('/registrations/{id}', [UserRegistrationController::class, 'update'])->name('registrations.update');
    // Individual registration management
    Route::get('/registrations/{id}/details', [UserRegistrationController::class, 'getRegistration'])->name('registrations.details');
    Route::delete('/registrations/{id}', [UserRegistrationController::class, 'destroy'])->name('registrations.destroy');

    // Enhanced status management with auto-refresh
    Route::post('/registrations/{id}/approve', [UserRegistrationController::class, 'approve'])->name('registrations.approve');
    Route::post('/registrations/{id}/reject', [UserRegistrationController::class, 'reject'])->name('registrations.reject');

    // Status update with session sync
    Route::post('/registrations/{id}/update-status', [UserRegistrationController::class, 'updateStatus'])->name('registrations.update-status');

    // Enhanced document viewing - supports images and files
    Route::get('/registrations/{id}/document/{type}', [UserRegistrationController::class, 'viewDocument'])
        ->name('registrations.document')
        ->where('type', 'location|id_front|id_back');

    // Statistics
    Route::get('/registrations/statistics', [UserRegistrationController::class, 'getStatistics'])->name('registrations.statistics');

    // Export functionality
    Route::get('/registrations/export', [UserRegistrationController::class, 'export'])->name('registrations.export');

    // Bulk operations (optional future enhancement)
    Route::post('/registrations/bulk-approve', [UserRegistrationController::class, 'bulkApprove'])->name('registrations.bulk-approve');
    Route::post('/registrations/bulk-reject', [UserRegistrationController::class, 'bulkReject'])->name('registrations.bulk-reject');
});

// Register middleware alias for newer Laravel versions
Route::aliasMiddleware('user.session', UserSession::class);

/*
|--------------------------------------------------------------------------
| Main Landing Page Route (handled above)
|--------------------------------------------------------------------------
*/
// Landing page route is defined earlier in the file with HomeController

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

    // Username availability checking
    Route::post('/check-username', [UserRegistrationController::class, 'checkUsername'])->name('auth.check.username');

    // Contact number availability checking
    Route::post('/check-contact', [UserRegistrationController::class, 'checkContactNumber'])->name('auth.check.contact');

    // UPDATED: Enhanced profile verification with file uploads - FIXED MIDDLEWARE AND ROUTE
    Route::post('/verify-profile', [UserRegistrationController::class, 'submitVerification'])
        ->middleware('web') // Use web middleware for session and CSRF protection
        ->name('auth.verify.profile');

    // Forgot Password with SMS OTP
    Route::post('/forgot-password/send-otp', [ForgotPasswordController::class, 'sendOtp'])->name('auth.forgot.send-otp');
    Route::post('/forgot-password/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('auth.forgot.verify-otp');
    Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword'])->name('auth.forgot.reset');
    Route::post('/forgot-password/resend-otp', [ForgotPasswordController::class, 'resendOtp'])->name('auth.forgot.resend-otp');

    // view document not needed
    // Route::get('/registrations/{id}/document/{type}', [UserRegistrationController::class, 'serveDocument'])
    //     ->name('registrations.document')
    //     ->where('type', 'location|id_front|id_back')
    //     ->middleware('auth');
});


// ==============================================
// SESSION CHECK ENDPOINT - CRITICAL PLACEMENT
// ==============================================
//
// Used by JavaScript session manager for periodic validation
// of user sessions. Must be accessible even if session might
// be expired or invalid for checking
//
Route::get('/api/user/session-check', function (\Illuminate\Http\Request $request) {
    // Check if user session exists
    if (!$request->session()->has('user')) {
        return response()->json([
            'success' => false,
            'authenticated' => false,
            'message' => 'No active session'
        ], 401);
    }

    // Get user from session
    $user = $request->session()->get('user');

    // Validate session data
    if (!is_array($user) || empty($user['id'])) {
        // Session is corrupted
        $request->session()->flush();
        $request->session()->regenerate();

        return response()->json([
            'success' => false,
            'authenticated' => false,
            'message' => 'Invalid session data'
        ], 401);
    }

    // Update last activity timestamp
    // Important: This resets the inactivity counter
    $request->session()->put('last_activity', time());

    // Session is valid
    return response()->json([
        'success' => true,
        'authenticated' => true,
        'message' => 'Session is valid',
        'user' => [
            'id' => $user['id'] ?? null,
            'username' => $user['username'] ?? null,
            'status' => $user['status'] ?? null,
        ]
    ], 200);
})->middleware('web')->name('api.user.session-check');

// ==============================================
// RECYCLE BIN MANAGEMENT
// ==============================================

Route::prefix('admin/recycle-bin')->name('admin.recycle-bin.')->middleware(['auth', 'admin'])->group(function () {
    // Bulk operations
    Route::post('/bulk/restore', [RecycleBinController::class, 'bulkRestore'])->name('bulk.restore');
    Route::post('/bulk/delete', [RecycleBinController::class, 'bulkDestroy'])->name('bulk.delete');
    Route::post('/bulk/permanently-delete', [RecycleBinController::class, 'bulkPermanentlyDelete'])->name('bulk.permanently-delete');

    // Empty entire bin
    Route::post('/empty', [RecycleBinController::class, 'empty'])->name('empty');

    // Generic routes
    Route::get('/', [RecycleBinController::class, 'index'])->name('index');
    Route::get('/{id}', [RecycleBinController::class, 'show'])->name('show');
    Route::post('/{id}/restore', [RecycleBinController::class, 'restore'])->name('restore');
    Route::delete('/{id}/permanently-delete', [RecycleBinController::class, 'permanentlyDelete'])->name('permanently-delete');
    Route::delete('/{id}', [RecycleBinController::class, 'destroy'])->name('destroy');
});
/*
|--------------------------------------------------------------------------
| User Dashboard Routes (Protected by UserSession middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['web',App\Http\Middleware\UserSession::class,App\Http\Middleware\CheckSessionExpiration::class])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('user.dashboard');

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


        // NEW: Explicit session refresh endpoint
        Route::post('/refresh-session', [UserRegistrationController::class, 'refreshUserSession'])->name('api.user.refresh-session');


        // New endpoint to fetch all applications (RSBSA, Seedlings, FishR, BoatR, Training) in my applications modal
        Route::get('/applications/all', [UserApplicationsController::class, 'getAllApplications'])
            ->name('api.user.applications.all');

        // change pass word route
        Route::post('/change-password', [UserRegistrationController::class, 'changePassword'])->name('api.user.change-password');

        //  Session update endpoint for polling (keeps session synced with DB)
        Route::post('/update-session', [UserRegistrationController::class, 'updateSession'])->name('api.user.update-session');
    });
});

// ========================================
// PUBLIC API ROUTES (No authentication)
// ========================================

Route::prefix('api')->name('api.')->group(function () {
    // Get all active events (for landing page)
    Route::get('/events', [EventController::class, 'getEvents'])
        ->name('events.public');
});


// ========================================
// LANDING PAGE PRIVACY POLICY AND TOS ROUTES
// ========================================

Route::get('/privacy-policy', function () {
    return view('landingPage.privacy-policy');
})->name('privacy-policy');

Route::get('/terms-of-service', function () {
    return view('landingPage.terms-of-service');
})->name('terms-of-service');

// ========================================
// EVENT ROUTES MOVED TO MAIN ADMIN GROUP ABOVE
// ========================================

// /*
// |--------------------------------------------------------------------------
// | Public API Routes (for AJAX calls)
// |--------------------------------------------------------------------------
// */
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


// ==============================================
// STORAGE FILE SERVING ROUTE (Fallback for hosts without symlink support)
// ==============================================
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);

    if (!file_exists($fullPath)) {
        abort(404);
    }

    $mimeType = mime_content_type($fullPath);

    return response()->file($fullPath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('storage.serve');

// ==============================================
// API ROUTES
// ==============================================
// Get active slideshow images for landing page
Route::get('/api/slideshow/active', [SlideshowController::class, 'getActiveSlides'])->name('api.slideshow.active');

// ==============================================
// SMS NOTIFICATION ROUTES
// ==============================================
Route::prefix('api/sms')->group(function () {
    // Admin only routes
    Route::middleware(['auth.session', 'check.admin'])->group(function () {
        Route::post('/send-notification', [\App\Http\Controllers\SmsNotificationController::class, 'sendNotification'])->name('api.sms.send-notification');
        Route::get('/service-status', [\App\Http\Controllers\SmsNotificationController::class, 'getServiceStatus'])->name('api.sms.service-status');
    });
});

// ==============================================
// FALLBACK ROUTE
// ==============================================
Route::fallback(function () {
    return redirect()->route('landing.page')->with('error', 'Page not found');
});
