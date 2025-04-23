<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request)
    {
        $request->fulfill();
        
        // For API response:
        return response()->json(['message' => 'Email verified successfully']);
        
        // OR for web redirect:
        // return redirect('/?verified=1');
    }

    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        
        // For API response:
        return response()->json(['message' => 'Verification link resent']);
        
        // OR for web:
        // return back()->with('message', 'Verification link sent!');
    }
}