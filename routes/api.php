<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    // ─────────────────────────────────────────────────
    // Public auth & verification
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
         ->name('verification.verify');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
         ->name('password.email');
    Route::post('/reset-password',  [AuthController::class, 'resetPassword'])
         ->name('password.update');

    // ─────────────────────────────────────────────────
    // Protected user routes
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout',  [AuthController::class, 'logout']);
        Route::get('/me',       [AuthController::class, 'me']);
        Route::put('/profile',  [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'updatePassword']);

        // ─────────────────────────────────────────────
        // Admin-only routes
        Route::prefix('admin')->group(function () {
            Route::get('/users',              [AdminController::class, 'index']);
            Route::get('/users/{id}',         [AdminController::class, 'show']);
            Route::put('/users/{id}',         [AdminController::class, 'update']);
            Route::delete('/users/{id}',      [AdminController::class, 'destroy']);
            Route::post('/users/{id}/restore',[AdminController::class, 'restore']);
            Route::delete('/users/{id}/force',[AdminController::class, 'forceDelete']);
        });
    });

    // ─────────────────────────────────────────────────
    // Social & notification routes…
    Route::get('/{provider}',              [AuthController::class, 'redirectToProvider']);
    Route::get('/{provider}/callback',     [AuthController::class, 'handleProviderCallback']);
    Route::post('/google/callback',        [AuthController::class, 'handleGoogleCallback']);
    Route::post('/facebook/callback',      [AuthController::class, 'handleFacebookCallback']);
    Route::post('/email/verification-notification',
                    [VerifyEmailController::class, 'resend'])
        ->name('verification.send');
});

// A standalone example if you ever need a “/user” endpoint:
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
