<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Tutor;
use App\Models\TutorSession;
use App\Models\User;
use App\Models\Module;
use App\Models\DegreeProgram;
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

        // Pending sessions created by the tutor
        $tutorsessions = TutorSession::where('tutor_id', $tutor->id)
                             ->where('status', 'pending')
                             ->get();
        // Convert the collection to an array for sorting
        $tutorsessionsArray = $tutorsessions->toArray();

        // Sort the array by session_date and then start_time
        usort($tutorsessionsArray, function ($a, $b) {
            // Compare session_date first
            $dateComparison = strtotime($a['session_date']) - strtotime($b['session_date']);
            if ($dateComparison === 0) {
                // If session_date is the same, compare start_time
                return strtotime($a['start_time']) - strtotime($b['start_time']);
            }
            return $dateComparison;
        });

        // You can convert the sorted array back to a collection if needed
        $tutorsessions = collect($tutorsessionsArray);

        //User details of the students who have booked a session with the tutor
        $bookings = TutorSession::where('tutor_id', $tutor->id)
                                ->where('status', 'booked')
                                ->get();
        $tutorName = User::where('id', $tutor->user_id)->first();
        $bookingdetails = [];
        foreach ($bookings as $booking) {
            $user = User::where('id', $booking->user_id)->first();
            $module = Module::where('id', $booking->module_id)->first();
            $profiles = Profile::where('user_id', $booking->user_id)->first();
            $degree = DegreeProgram::where('id', $profiles->degree_id)->first();

            $bookingdetails[] = [
                'id' => $booking->id,
                'user' => $user->name,
                'tutor' => $tutorName->name,
                'profile_pic' => $profiles->profile_pic,
                'degree' => $degree->degree_name,
                'meeting_url' => $booking->meeting_url,
                'notes' => $booking->notes,
                'module_name' => $module ? $module->module_name : 'Unknown Module',
                'session_date' => $booking->session_date,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
            ];
        }

        // Sort session details by session_date and then by start_time
        usort($bookingdetails, function ($a, $b) {
            // Compare session_date first
            $dateComparison = strtotime($a['session_date']) - strtotime($b['session_date']);
            if ($dateComparison === 0) {
                // If session_date is the same, compare start_time
                return strtotime($a['start_time']) - strtotime($b['start_time']);
            }
            return $dateComparison;
        });

        return Inertia::render('Tutor/Dashboard',
        [
            'approvedModules' => $approvedModules,
            'rejectedModules' => $rejectedModules,
            'rejectedReason' => $rejectedReason,
            'tutorsSelectedModules' => $tutorsSelectedModules,
            'sessionSlots' => $tutorsessions,
            'bookings' => $bookingdetails,
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

    public function createSession (Request $request)
    {
        // Retrieve the tutor associated with the authenticated user
        $tutor = Tutor::where('user_id', auth()->id())->first();

        $tutorsessionslots = $tutor->sessions()->get();
        // $tutorsessionslots->each->delete();

        if (!$tutor) {
            return redirect()->route('tutor.dashboard')
                ->with('error', 'No associated tutor account found. Please contact support.');
        }

        // Validate the request
        $request->validate([
            'sessions' => 'required|array',
            'sessions.*.session_date' => 'required|string',
            'sessions.*.start_time' => 'required|date_format:H:i',
            'sessions.*.end_time' => 'required|date_format:H:i',
        ]);

        // Extract the single session from the array
        $session = $request->sessions[0];

        // Store the session
        TutorSession::create([
            'tutor_id' => $tutor->id,
            'session_date' => $session['session_date'],
            'start_time' => $session['start_time'],
            'end_time' => $session['end_time'],
        ]);

        return redirect()->route('tutor.dashboard');
    }

    public function deleteSession ($id)
    {
        $session = TutorSession::findOrFail($id);

        // Ensure the session belongs to the authenticated tutor
        $tutor = Tutor::where('user_id', auth()->id())->first();
        if ($session->tutor_id !== $tutor->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $session->delete();

        return redirect()->route('tutor.dashboard');
    }

}
