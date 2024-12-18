<?php

namespace App\Http\Controllers;

use App\Models\AvailableTime;
use App\Models\Tutor;
use App\Models\TutorSession;
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
        $tutorsSelectedModules = $tutor->selectedModules()->get();
        $availableTimes = AvailableTime::where('tutor_id', $tutor->id)->get();

        $tutorsessions = TutorSession::where('tutor_id', $tutor->id)->get();
        $sessionuserdetails = [];
        foreach ($tutorsessions as $session) {
            $sessionuserdetails[] = $session->user()->first();
        }

        $approval = [
            'userdetails' => $sessionuserdetails,
            'tutorsessions' => $tutorsessions,
        ];

        return Inertia::render('Tutor/Dashboard',
        [
            'approvedModules' => $approvedModules,
            'rejectedModules' => $rejectedModules,
            'rejectedReason' => $rejectedReason,
            'tutorsSelectedModules' => $tutorsSelectedModules,
            'availableTimes' => $availableTimes,
            'approvals' => $approval,
        ]);
    }

    public function selectModule($id)
    {
        $tutor = Tutor::where('user_id', auth()->id())->first();
        $tutor->selectedModules()->attach($id);

        return redirect()->route('tutor.dashboard');
    }

    public function removeModule($id)
    {
        $tutor = Tutor::where('user_id', auth()->id())->first();
        $tutor->selectedModules()->detach($id);

        return redirect()->route('tutor.dashboard');
    }

    public function storeAvailableTimes(Request $request)
    {

        // Retrieve the tutor associated with the authenticated user
        $tutor = Tutor::where('user_id', auth()->id())->first();

        $tutoravailabletimes = $tutor->availableTimes()->get();
        $tutoravailabletimes->each->delete();

        // Validate the request
        $request->validate([
            'sessions' => 'required|array',
            'sessions.*.day' => 'required|string',
            'sessions.*.start_time' => 'required|date_format:H:i',
            'sessions.*.end_time' => 'required|date_format:H:i',
        ]);

        // Loop through sessions and store each time slot
        foreach ($request->sessions as $session) {
            AvailableTime::create([
                'tutor_id' => $tutor->id,
                'day' => $session['day'],
                'start_time' => $session['start_time'],
                'end_time' => $session['end_time']
            ]);
        }

        return redirect()->route('tutor.dashboard');
    }

    public function destroyAvailableTimes(Request $request)
    {
        dd($request->all());
        $tutor = Tutor::where('user_id', auth()->id())->first();

        // Validate that 'days' is provided
        $request->validate([
            'days' => 'required|array',
        ]);

        // Delete available times for the provided days
        AvailableTime::where('tutor_id', $tutor->id)
            ->whereIn('day', $request->days)
            ->delete();

        return response()->json(['message' => 'Available times deleted successfully']);
    }

}
