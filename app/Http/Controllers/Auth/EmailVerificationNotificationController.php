<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Step 1: Ensure the user's email is verified
        if (!$user->hasVerifiedEmail()) {
            // Send a verification email if it hasn't been sent
            $user->sendEmailVerificationNotification();
            return back()->with('status', 'verification-link-sent');
        }

        // Step 2: Ensure the user has a profile
        if (!$user->profile()->exists()) {
            return redirect()->route('profile.create')->with('status', 'Please complete your profile before proceeding.');
        }

        // Step 3: Redirect the user to the dashboard
        return redirect()->intended(route('profile.show', absolute: false))->with('status', 'Welcome to your dashboard!');
    }

}
