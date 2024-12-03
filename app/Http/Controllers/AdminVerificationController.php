<?php

namespace App\Http\Controllers;

use App\Mail\ApproveTutorMail;
use App\Models\Course;
use App\Models\CourseLevel;
use App\Models\DegreeProgram;
use App\Models\Level;
use App\Models\Profile;
use App\Models\SchoolOfStudy;
use App\Models\Semester;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class AdminVerificationController extends Controller
{
    public function index()
    {
        Tutor::create(
            [
                'user_id' => auth()->id(),
                'status' => 'pending'
            ]
        );

        $tutor = Tutor::where('user_id', auth()->id())->first();

        Mail::to('admin@example.com')->send(new ApproveTutorMail($tutor));

        return Inertia::render(route('profile.show'));
    }

    public function tutorDashboard()
    {
        return Inertia::render('Tutor/Dashboard');
    }

    public function adminDashboard()
    {
        $tutors = Tutor::all();
        $user = User::all();
        $profile = Profile::all();
        $school = SchoolOfStudy::all();
        $degree = DegreeProgram::all();
        $level = Level::all();
        $semester = Semester::all();


        return Inertia::render('Admin/Dashboard', [
            'tutors' => $tutors,
            'users' => $user,
            'profiles' => $profile,
            'schools' => $school,
            'degrees' => $degree,
            'levels' => $level,
            'semesters' => $semester
        ]
        );
    }

    public function approveTutor($id)
    {
        $tutor = Tutor::find($id);
        $tutor->status = 'approved';
        $tutor->save();

        return redirect()->route('admin.dashboard');
    }

    public function rejectTutor($id)
    {
        $tutor = Tutor::find($id);
        $tutor->status = 'rejected';
        $tutor->save();

        return redirect()->route('admin.dashboard');
    }

    public function deleteTutor($id)
    {
        $tutor = Tutor::find($id);
        $tutor->delete();

        return redirect()->route('admin.dashboard');
    }
}
