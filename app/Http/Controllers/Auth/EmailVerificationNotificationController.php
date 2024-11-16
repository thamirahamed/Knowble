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

        // Step 1: Check if the user's email is verified
        if (!$user->hasVerifiedEmail()) {
            // Send verification email if not already sent
            $user->sendEmailVerificationNotification();
            return back()->with('status', 'verification-link-sent');
        }

        // Step 2: Check if the user has a profile
        if (!$user->profile()->exists()) {
            return redirect()->route('profile.create')->with('status', 'profile-creation-required');
        }

        // Step 3: Redirect to the dashboard
        return redirect()->intended(route('dashboard', absolute: false));
    }
}
