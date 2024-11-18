<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Course;
use App\Models\CourseLevel;
use App\Models\Tutor;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    public function store(Request $request)
    {
        // Validate the input data (only the fields that can be set during profile creation)
        $validatedData = $request->validate([
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $profilePictureName = 'default.webp'; // Set default picture

        // Handle profile picture upload
        if ($request->hasFile('profile_pic')) {
            $profilePicture = $request->file('profile_pic');
            $profilePictureName = time() . '.' . $profilePicture->extension();
            $profilePicture->storeAs('public/path_images', $profilePictureName);
        }

        // Handle storing the validated data
        Profile::create([
            'user_id' => auth()->id(),
            'course_id' => $request->course,
            'level_id' => $request->level,
            'cb_number' => $request->cb_number, // Example based on email
            'profile_pic' => $profilePictureName,
        ]);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }

    public function getimage($filename) {
        $path = storage_path('app/private/public/path_images/' . $filename);

        // Check if the file exists
        if (!file_exists($path)) {
            abort(404, 'Image not found.');
        }

        // Serve the file as a response
        return response()->file($path);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $course = Course::all();
        $courselevel = CourseLevel::all();
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'profile' => $request->user()->profile, // Include the user's profile data
            'courses' => $course,
            'levels' => $courselevel
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        // Validate the incoming data
        $validatedData = $request->validate([
            'course_id' => 'required|integer',
            'level_id' => 'required|integer',
            'cb_number' => 'required|string|max:255',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Get the authenticated user and their profile
        $user = $request->user();
        $profile = $user->profile; // Use the existing profile

        // Handle profile picture upload
        $profilePictureName = $profile->profile_pic; // Default to the old picture name
        if ($request->hasFile('profile_pic')) {
            // Delete the old profile picture if it exists
            if ($profilePictureName) {
                Storage::delete('public/path_images/' . $profilePictureName);
            }

            // Store the new profile picture
            $profilePicture = $request->file('profile_pic');
            $profilePictureName = time() . '.' . $profilePicture->extension();
            $profilePicture->storeAs('public/path_images', $profilePictureName);
        }

        // Update the profile data
        $profile->update([
            'course_id' => $validatedData['course_id'],
            'level_id' => $validatedData['level_id'],
            'cb_number' => $validatedData['cb_number'],
            'profile_pic' => $profilePictureName,
        ]);

        // Redirect back with a success message
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
        $profile = Profile::where('user_id', auth()->id())->firstOrFail();
        $userdetails = auth()->user();
        $usercourse = Course::find($profile->course_id);
        $userlevel  = CourseLevel::find($profile->level_id);

        $tutorstatus = Tutor::where('user_id', auth()->id())->first();

        if ($tutorstatus == null){
            return Inertia::render('Profile/View',[
                'profile' => $profile,
                'user' => $userdetails,
                'course' => $usercourse,
                'level' => $userlevel,
                'tutor' => $tutorstatus
            ]);
        }
        return Inertia::render('Profile/View',[
            'profile' => $profile,
            'user' => $userdetails,
            'course' => $usercourse,
            'level' => $userlevel,
            'tutor' => $tutorstatus->status
        ]);
    }

    // New Method to create the profile
    public function create()
    {

        return Inertia::render('Profile/Create', [
            'courses' => Course::all(),
            'levels' => CourseLevel::all(),
        ]);
    }
}
