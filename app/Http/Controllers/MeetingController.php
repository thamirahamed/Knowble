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

class MeetingController extends Controller
{
    // Show meeting dashboard
    public function index(Request $request)
    {
        $userId = auth()->id();
        $isTutor = Tutor::where('user_id', $userId)->first();
        $booking = TutorSession::where('id', $request->get('id'))->first();

        if(!$booking || is_null($booking->meeting_url)){
            return back();
        }

        //Retrieve Tutor Information
        $tutorId = $booking->tutor_id;
        $tutor = Tutor::where('id', $tutorId)->first();
        $tutorName = User::where('id', $tutor->user_id)->first();

        //Retrieve student information
        $userId = $booking->user_id;
        $student = User::where('id', $userId)->first();

        // Retrieve the module name
        $module = Module::where('id', $booking->module_id)->first();
        $moduleName = $module->module_name;

        // Create the array with only required information
        $bookingDetails = [
            'isUserTutor' => $isTutor ? "Yes" : "No",
            'tutor_id' => $tutorName->id,
            'tutor_name' => $tutorName->name,
            'student_name' => $student->name,
            'module_name' => $moduleName,
            'session_date' => $booking->session_date,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'notes' => $booking->notes,
        ];
        return Inertia::render('Meetings/VideoCall',
        [
            'meetingUrl' => $request->get('meetingUrl'),
            'bookingDetails' => $bookingDetails,
        ]);
    }
}
