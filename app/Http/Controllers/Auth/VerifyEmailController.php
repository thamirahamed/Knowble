<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Mark the email as verified and fire the Verified event
        if (!$user->hasVerifiedEmail() && $user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Check if the user has a profile
        if (!$user->profile()->exists()) {
            return redirect()->route('profile.create')->with('status', 'verification-link-sent');
        }

        // Redirect to the dashboard with a verification success parameter
        return redirect()->intended(route('profile.show', absolute: false) . '?verified=1');
    }
}
