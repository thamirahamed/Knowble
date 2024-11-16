<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;
use App\Models\Profile;

class ProfileController extends Controller
{
    /**
     * Return the valid options for school of study and year/semester combinations.
     *
     * @return array
     */
    public function validOptions()
    {
        return [
            'Business Foundation' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2',
            ],
            'Law Foundation' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2',
            ],
            'Computing Foundation' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2',
            ],
            'BSc (Hons) Computer Science' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2', 'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Computer Science (Cloud Technologies)' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2', 'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Computer Science (Internet and Web Management)' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2', 'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Computer Science (Network Computing)' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2', 'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Computer Science (Software Development)' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2', 'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Cyber Security' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2', 'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Accounting and Finance' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2', 'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Digital and Social Media Marketing' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2', 'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) International Business Management' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2', 'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Business Management (Sustainability)' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2', 'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Business Management (Human Resource Management)' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2', 'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Business Management (Innovation and Entrepreneurship)' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2', 'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'BSc (Hons) Business Management' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2', 'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'LLB (Hons) Law' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2', 'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'LLB (Hons) Law – Digital' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2', 'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
            'LLB (Hons) Law (Part-Time)' => [
                'Year 1 - Sem 1', 'Year 1 - Sem 2', 'Year 2 - Sem 1', 'Year 2 - Sem 2',
                'Year 3 - Sem 1', 'Year 3 - Sem 2',
            ],
        ];
    }

    public function store(Request $request)
    {
        $validOptions = $this->validOptions();

        // Validate the input data (only the fields that can be set during profile creation)
        $validatedData = $request->validate([
            'school_of_study' => 'required|string|in:' . implode(',', array_keys($validOptions)),
            'year_sem' => 'required|string|in:' . implode(',', $validOptions[$request->school_of_study] ?? []),
            'profile_pic' => 'nullable|image|max:5120', // Profile pic max size is 5MB
            'available_times' => 'nullable|array', // If available times are provided
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_pic')) {
            $validatedData['profile_pic'] = $request->file('profile_pic')->store('profile_pics', 'public');
        }

        // Handle storing the validated data
        Profile::create([
            'user_id' => auth()->id(),
            'school_of_study' => $validatedData['school_of_study'],
            'year_sem' => $validatedData['year_sem'],
            'cb_number' => substr(explode('@', auth()->user()->email)[0], 0, 10), // Example based on email
            'profile_pic' => $validatedData['profile_pic'] ?? 'default.svg', // default pic if not provided
            'available_times' => $validatedData['available_times'] ?? null,
            'role' => 'student', // Default to student
        ]);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'profile' => $request->user()->profile, // Include the user's profile data
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validOptions = $this->validOptions();

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // Validate the fields that can be updated (school of study, year_sem, profile_pic, available_times)
        $validatedData = $request->validate([
            'school_of_study' => 'nullable|string|in:' . implode(',', array_keys($validOptions)),
            'year_sem' => 'nullable|string|in:' . implode(',', $validOptions[$request->school_of_study] ?? []),
            'profile_pic' => 'nullable|image|max:5120', // Profile pic max size is 5MB
            'available_times' => 'nullable|array', // If available times are provided
        ]);

        // Handle profile picture upload (if exists)
        if ($request->hasFile('profile_pic')) {
            $validatedData['profile_pic'] = $request->file('profile_pic')->store('profile_pics', 'public');
        }

        // Get the current user's profile
        $profile = $request->user()->profile;

        // Update the profile data with the validated input
        if ($profile) {
            $profile->update($validatedData);
        }

        return Redirect::route('profile.edit')->with('status', 'Profile updated successfully!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    // New Method to display the profile
    public function show()
    {
        $profile = Profile::where('id', auth()->id())->firstOrFail();
        return Inertia::render('Profile/Show', compact('profile'));
    }

    // New Method to create the profile
    public function create()
    {
        $validInputs = $this->validOptions(); // Get the valid inputs

        return Inertia::render('CreateProfile', [
            'validInputs' => $validInputs, // Pass the valid options to the frontend
        ]);
    }
}
