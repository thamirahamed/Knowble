<?php

namespace App\Http\Controllers;

use App\Mail\ApproveTutorMail;
use App\Models\Tutor;
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
        $useremail = auth()->user()->email;

        if ($useremail === 'admin@apiit.lk') {
            

            return Inertia::render('Admin/Dashboard');
        }

        return redirect()->back();
    }
}
