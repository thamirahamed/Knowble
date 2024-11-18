<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|Response
    {
        $user = $request->user();

        // If the email is not verified, show the Verify Email page
        if (!$user->hasVerifiedEmail()) {
            return Inertia::render('Auth/VerifyEmail', ['status' => session('status')]);
        }

        // If the email is verified, check if the user has a profile
        if (!$user->profile()->exists()) {
            return redirect()->route('profile.create');
        }

        // If the user has a profile and their email is verified, go to the dashboard
        return redirect()->intended(route('profile.show', absolute: false));
    }
}
