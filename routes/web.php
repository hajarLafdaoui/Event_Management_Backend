<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\EventFeedbackController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes would also be here...

// Protected invitation routes (web interface)
Route::middleware(['auth'])->group(function () {
    // Show send invitations form
    Route::get('/events/{event}/invitations', [InvitationController::class, 'showSendForm'])
        ->name('invitations.show');

    // Send invitations
    Route::post('/events/{event}/invitations', [InvitationController::class, 'send'])
        ->name('invitations.send');
});

// Public RSVP routes (no auth required)
Route::get('/rsvp/{token}', [InvitationController::class, 'showRSVP'])
    ->name('rsvp.show');

Route::post('/rsvp/{token}', [InvitationController::class, 'processRSVP'])
    ->name('rsvp.submit');

// Feedback routes
Route::get('/feedback/{event_id}/{guest_id}', [EventFeedbackController::class, 'create'])->name('feedback.create');
Route::post('/feedback', [EventFeedbackController::class, 'store'])->name('feedback.store');