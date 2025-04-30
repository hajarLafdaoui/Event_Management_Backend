<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use OpenAI\Laravel\Facades\OpenAI;

Route::prefix('auth')->group(function () {    // Basic Authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:api');
    
    // Profile Management
    Route::put('/profile', [AuthController::class, 'updateProfile'])->middleware('auth:api');
    Route::put('/password', [AuthController::class, 'updatePassword'])->middleware('auth:api');
    
    // Password Reset
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    
    // Social Authentication
    Route::get('/social/{provider}', [AuthController::class, 'redirectToProvider']);
    Route::get('/social/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
    
    // Google/Facebook specific (if needed)
    Route::post('/google/callback', [AuthController::class, 'handleGoogleCallback']);
    Route::post('/facebook/callback', [AuthController::class, 'handleFacebookCallback']);
     // Email Verification Routes
     Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
     ->name('verification.verify');
     
 Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail']);
 Route::get('/test-mail', function() {
    try {
        \Mail::raw('Test email', function($message) {
            $message->to('test@example.com')->subject('Test');
        });
        return 'Email sent';
    } catch (\Exception $e) {
        return 'Error: '.$e->getMessage();
    }
});
});

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