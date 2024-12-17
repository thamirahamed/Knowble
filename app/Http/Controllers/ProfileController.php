<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\AvailableTime;
use App\Models\Course;
use App\Models\CourseLevel;
use App\Models\DegreeProgram;
use App\Models\Level;
use App\Models\SchoolOfStudy;
use App\Models\Semester;
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
use PhpParser\Node\Expr\Print_;


class ProfileController extends Controller
{
    public function store(Request $request)
    {
        // Validate the input data (only the fields that can be set during profile creation)
        $validatedData = $request->validate([
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Default profile picture
        $profilePictureName = 'https://knowblestorage.s3.ap-southeast-1.amazonaws.com/profile_pic/default.jpg';  // Set default picture
        $s3Url = null;  // Initialize variable for S3 URL


        // Handle profile picture upload
        if ($request->hasFile('profile_pic')) {
            $profilePicture = $request->file('profile_pic');
            $profilePictureName = time() . '.' . $profilePicture->extension();

            // Upload to S3
            $uploadS3 = $profilePicture->store('profile_pic', 's3');
            $s3Url = Storage::disk('s3')->url($uploadS3);
        }

        // Handle storing the validated data
        Profile::create([
            'user_id' => auth()->id(),
            'school_id' => $request->school,
            'degree_id' => $request->degree,
            'semester_id' => $request->semester,
            'level_id' => $request->level,
            'cb_number' => $request->cb_number, // Example based on email
            'profile_pic' => $s3Url ?: $profilePictureName,  // Use S3 URL or default image
        ]);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }


    public function getimage($filename) {
        $path = storage_path('app/private/public/path_images/'. $filename);

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
        $school = SchoolOfStudy::all();
        $degree = DegreeProgram::all();
        $level = Level::all();
        $semester = Semester::all();


        $profile = Profile::where('user_id', auth()->id())->firstOrFail();
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'profile' => $profile, // Include the user's profile data
            'school' => $school,
            'degree' => $degree,
            'level' => $level,
            'semester' => $semester,
        ]);
    }

    /**
     * Update the user's profile information.
     */

    public function update(Request $request)
    {
        // Validate the input data
        $validatedData = $request->validate([
            'school_id' => 'required|integer|exists:school_of_studies,id',
            'degree_id' => 'required|integer|exists:degree_programs,id',
            'semester_id' => 'required|integer|exists:semesters,id',
            'level_id' => 'required|integer|exists:levels,id',
            'cb_number' => 'required|string|max:255',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);



        // Get the authenticated user and their profile
        $user = $request->user();
        $profile = Profile::where('user_id', $user->id)->firstOrFail();

        // Default to the current profile picture
        $profilePictureUrl = $profile->profile_pic;

        // Handle profile picture upload if a new file is provided
        if ($request->hasFile('profile_pic')) {
            $profilePicture = $request->file('profile_pic');

            // Delete the old profile picture from S3 if it's not the default
            if ($profilePictureUrl !== 'https://knowblestorage.s3.ap-southeast-1.amazonaws.com/profile_pic/default.jpg') {
                Storage::disk('s3')->delete('profile_pic/' . basename($profilePictureUrl));
            }

            // Upload the new profile picture to S3
            $uploadS3 = $profilePicture->store('profile_pic', 's3');
            $profilePictureUrl = Storage::disk('s3')->url($uploadS3);
        } elseif ($request->profile_pic === null) {
            // If profile_pic is null, set it to the default URL
            $profilePictureUrl = 'https://knowblestorage.s3.ap-southeast-1.amazonaws.com/profile_pic/default.jpg';
        }

        // Update the profile data
        $profile->update([
            'school_id' => $validatedData['school_id'],
            'degree_id' => $validatedData['degree_id'],
            'level_id' => $validatedData['level_id'],
            'semester_id' => $validatedData['semester_id'],
            'cb_number' => $validatedData['cb_number'],
            'profile_pic' => $profilePictureUrl, // Save the new URL, default URL, or retain the old one
        ]);

        // Redirect back with a success message
        return Redirect::route('profile.show')->with('status', 'Profile updated successfully!');
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
        $tutor = Tutor::where('user_id', auth()->id())->first();

        $userdetails = auth()->user();
        $userschool = SchoolOfStudy::find($profile->school_id);
        $userlevel  = Level::find($profile->level_id);
        $usersemester = Semester::find($profile->semester_id);
        $usercourse = DegreeProgram::find($profile->degree_id);

        if($tutor){
            $tutoravailabletime = AvailableTime::where('tutor_id', $tutor->id)->get();
            $tutorselectedmodules = $tutor->selectedModules()->get();

            return Inertia::render('Profile/View',[
                'profile' => $profile,
                'user' => $userdetails,
                'school' => $userschool,
                'level' => $userlevel,
                'semester' => $usersemester,
                'course' => $usercourse,
                'tutoravailabletime' => $tutoravailabletime,
                'tutorselectedmodules' => $tutorselectedmodules,
            ]);
        }


        return Inertia::render('Profile/View',[
            'profile' => $profile,
            'user' => $userdetails,
            'school' => $userschool,
            'level' => $userlevel,
            'semester' => $usersemester,
            'course' => $usercourse,
        ]);
    }

    // New Method to create the profile
    public function create()
    {
        $profile = Profile::where('user_id', auth()->id())->first();

        if ($profile) {
            return back();
        }

        return Inertia::render('Profile/Create', [
            'SchoolOfStudy' => SchoolOfStudy::all(),
            'DegreeProgram' => DegreeProgram::all(),
            'Level' => Level::all(),
            'Semester' => Semester::all(),
        ]);
    }
}
