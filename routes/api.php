<?php
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventTaskController;
use App\Http\Controllers\EventTypeController;
use App\Http\Controllers\TaskTemplateController;
use App\Http\Controllers\EventTemplateController;
use App\Http\Controllers\BookingRequestController;
use App\Http\Controllers\VendorPaymentController;

use App\Http\Controllers\Vendor\VendorController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Vendor\VendorServiceController;
use App\Http\Controllers\Vendor\VendorCategoryController;
use App\Http\Controllers\vendor\VendorPortfolioController;
use App\Http\Controllers\Vendor\VendorPricingPackageController;


// ─────────────────────────────────────────────────
// Public auth & verification
Route::prefix('auth')->group(function () {    // Basic Authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->name('verification.verify');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
        ->name('password.email');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->name('password.update');

    // ─────────────────────────────────────────────────
    // Protected user routes
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'updatePassword']);

        // ─────────────────────────────────────────────
        // Admin-only routes
        Route::prefix('admin')->group(function () {
            Route::get('/users', [AdminController::class, 'index']);
            Route::get('/users/{id}', [AdminController::class, 'show']);
            Route::put('/users/{id}', [AdminController::class, 'update']);
            Route::delete('/users/{id}', [AdminController::class, 'destroy']);
            Route::post('/users/{id}/restore', [AdminController::class, 'restore']);
            Route::delete('/users/{id}/force', [AdminController::class, 'forceDelete']);
        });
    });

    // ─────────────────────────────────────────────────
    // Social & notification routes…
    Route::get('/{provider}', [AuthController::class, 'redirectToProvider']);
    Route::get('/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
    Route::post('/google/callback', [AuthController::class, 'handleGoogleCallback']);
    Route::post('/facebook/callback', [AuthController::class, 'handleFacebookCallback']);
    Route::post(
        '/email/verification-notification',
        [VerifyEmailController::class, 'resend']
    )
        ->name('verification.send');
});


// Vendor Categories
Route::prefix('vendor-categories')->group(function () {
    Route::get('/', [VendorCategoryController::class, 'getVendorCategories']);
    Route::post('/', [VendorCategoryController::class, 'createVendorCategory']);
    Route::get('/{id}', [VendorCategoryController::class, 'getVendorCategory']);
    Route::put('/{id}', [VendorCategoryController::class, 'updateVendorCategory']);
    Route::delete('/{id}', [VendorCategoryController::class, 'deleteVendorCategory']);
});

// Vendor
Route::prefix('vendors')->group(function () {
    Route::get('/', [VendorController::class, 'getVendors']);
    Route::post('/', [VendorController::class, 'createVendor']);
    Route::get('/{id}', [VendorController::class, 'getVendor']);
    Route::put('/{id}', [VendorController::class, 'updateVendor']);
    Route::delete('/{id}', [VendorController::class, 'deleteVendor']);
    
});

// Vendor Portfolio Routes
Route::prefix('vendors/{vendor}/portfolio')->group(function () {
    Route::get('/', [VendorPortfolioController::class, 'getVendorPortfolios']);
    Route::post('/', [VendorPortfolioController::class, 'createPortfolioItem']);
    Route::get('/{portfolio}', [VendorPortfolioController::class, 'getPortfolioItem']);
    Route::put('/{portfolio}', [VendorPortfolioController::class, 'updatePortfolioItem']);
    Route::delete('/{portfolio}', [VendorPortfolioController::class, 'deletePortfolioItem']);
});

// Vendor Services Routes
Route::prefix('vendors/{vendor}/services')->group(function () {
    Route::get('/', [VendorServiceController::class, 'getVendorServices']);
    Route::post('/', [VendorServiceController::class, 'createVendorService']);
    Route::get('/{service}', [VendorServiceController::class, 'getVendorService']);
    Route::put('/{service}', [VendorServiceController::class, 'updateVendorService']);
    Route::delete('/{service}', [VendorServiceController::class, 'deleteVendorService']);
    
    // Pricing Packages Routes
    Route::prefix('/{service}/packages')->group(function () {
        Route::get('/', [VendorPricingPackageController::class, 'getPricingPackages']);
        Route::post('/', [VendorPricingPackageController::class, 'createPricingPackage']);
        Route::get('/{package}', [VendorPricingPackageController::class, 'getPricingPackage']);
        Route::put('/{package}', [VendorPricingPackageController::class, 'updatePricingPackage']);
        Route::delete('/{package}', [VendorPricingPackageController::class, 'deletePricingPackage']);
    });
});



// A standalone example if you ever need a “/user” endpoint:
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//--- EVENT MANAGEMENT ROUTES ---//

// Event Types Routes
Route::prefix('event-types')->middleware('auth:api')->group(function () {
    Route::get('/', [EventTypeController::class, 'index']);
    Route::post('/', [EventTypeController::class, 'store'])->middleware('role:admin');
    Route::put('/{eventType}', [EventTypeController::class, 'update'])->middleware('role:admin');
    Route::delete('/{eventType}', [EventTypeController::class, 'destroy'])->middleware('role:admin');
});

// Event Templates Routes
Route::prefix('event-templates')->middleware('auth:api')->group(function () {
    Route::get('/', [EventTemplateController::class, 'index']);
    Route::post('/', [EventTemplateController::class, 'store'])->middleware('role:admin');
    Route::get('/{template}', [EventTemplateController::class, 'show']);
    Route::put('/{template}', [EventTemplateController::class, 'update'])->middleware('role:admin');
    Route::delete('/{template}', [EventTemplateController::class, 'destroy'])->middleware('role:admin');

    // AI Generation Route
    Route::post('/generate-with-ai', [EventTemplateController::class, 'generateWithAI'])->middleware('role:admin');
});

// Events Routes
Route::prefix('events')->middleware('auth:api')->group(function () {
    Route::get('/', [EventController::class, 'index']);
    Route::post('/', [EventController::class, 'store']);
    Route::get('/{event}', [EventController::class, 'show']);
    Route::put('/{event}', [EventController::class, 'update']);
    Route::delete('/{event}', [EventController::class, 'destroy']);

    // Template and AI Generation Routes
    Route::post('/generate-from-template/{template}', [EventController::class, 'generateFromTemplate']);
    Route::post('/generate-with-ai', [EventController::class, 'generateWithAI']);
});


//--- TASK MANAGEMENT ROUTES ---//

// Task Templates Routes
Route::prefix('task-templates')->middleware('auth:api')->group(function () {
    Route::get('/', [TaskTemplateController::class, 'index']);
    Route::post('/', [TaskTemplateController::class, 'store'])->middleware('role:admin');
    Route::get('/{taskTemplate}', [TaskTemplateController::class, 'show']);
    Route::put('/{taskTemplate}', [TaskTemplateController::class, 'update'])->middleware('role:admin');
    Route::delete('/{taskTemplate}', [TaskTemplateController::class, 'destroy'])->middleware('role:admin');
    Route::post('/generate-with-ai', [TaskTemplateController::class, 'generateWithAI'])->middleware('role:admin');
});

// Event Tasks Routes
Route::prefix('event-tasks')->middleware('auth:api')->group(function () {
    Route::get('/', [EventTaskController::class, 'index']);
    Route::post('/', [EventTaskController::class, 'store']);
    Route::get('/{eventTask}', [EventTaskController::class, 'show']);
    Route::put('/{eventTask}', [EventTaskController::class, 'update']);
    Route::delete('/{eventTask}', [EventTaskController::class, 'destroy']);
    Route::post('/generate-from-template/{taskTemplate}', [EventTaskController::class, 'generateFromTemplate']);
});

//--- BOOKING & PAYMENT SYSTEM ---//

// Booking Request Routes
Route::prefix('booking-requests')->middleware('auth:api')->group(function () {
    Route::get('/', [BookingRequestController::class, 'index']);// Get a list of all booking requests
    Route::post('/', [BookingRequestController::class, 'store']);// Create a new booking request
    Route::get('/{id}', [BookingRequestController::class, 'show']);// Get a specific booking request by ID
    Route::put('/{id}', [BookingRequestController::class, 'update']);// Update an existing booking request by ID
    Route::delete('/{id}', [BookingRequestController::class, 'destroy']);// Delete a booking request by ID
});
// Vendor Payment Routes
Route::prefix('vendor-payments')->group(function () {
    Route::get('/', [VendorPaymentController::class, 'index']);  // Get all vendor payments
    Route::post('/', [VendorPaymentController::class, 'store']);  // Store a new vendor payment
    Route::get('{payment_id}', [VendorPaymentController::class, 'show']);  // Get a specific vendor payment
    Route::put('{payment_id}', [VendorPaymentController::class, 'update']);  // Update a specific vendor payment
    Route::delete('{payment_id}', [VendorPaymentController::class, 'destroy']);  // Delete a specific vendor payment
});


