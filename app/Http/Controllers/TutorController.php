<?php

namespace App\Http\Controllers;

use App\Models\Tutor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TutorController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $tutor = Tutor::where('user_id', $userId)->first();

        if ($tutor->status === 'pending') {
            return Inertia::render('Dashboard');
        }

        $approvedModules = $tutor->approvedModules()->get();
        $rejectedModules = $tutor->rejectedModules()->get();
        $rejectedReason = $tutor->rejectMessage()->first();
        return Inertia::render('Tutor/Dashboard',
        [
            'approvedModules' => $approvedModules,
            'rejectedModules' => $rejectedModules,
            'rejectedReason' => $rejectedReason
        ]);
    }
}
