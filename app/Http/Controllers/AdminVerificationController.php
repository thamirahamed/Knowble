<?php

namespace App\Http\Controllers;

use App\Mail\ApproveTutorMail;
use App\Models\Profile;
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

        dd($tutors);
        return Inertia::render('Admin/Dashboard');
    }
}
