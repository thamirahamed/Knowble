<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Tutor;
use App\Models\TutorSession;
use App\Models\User;
use App\Models\Module;
use App\Models\DegreeProgram;
use App\Models\PeerGroup;
use App\Models\PeerGroupMember;
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

        //Retrieve peer group information
        $groupId = $booking->peer_group_id;
        $peerGroup = PeerGroup::where('id', $groupId)->first();
        $memberCount = null;
        if(!is_null($peerGroup)){
            $memberCount = PeerGroupMember::where('peer_group_id', $peerGroup->id)->count() + 1;
        }

        // Retrieve the module name
        $module = Module::where('id', $booking->module_id)->first();
        $moduleName = $module->module_name;

        // Create the array with only required information
        if(!is_null($student)){
            $bookingDetails = [
                'id' => $booking->id,
                'isUserTutor' => $isTutor ? "Yes" : "No",
                'type' => 'individual',
                'tutor_id' => $tutorId,
                'tutor_name' => $tutorName->name,
                'student_name' => $student->name,
                'module_name' => $moduleName,
                'session_date' => $booking->session_date,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'notes' => $booking->notes,
            ];
        }else{
            $bookingDetails = [
                'id' => $booking->id,
                'isUserTutor' => $isTutor ? "Yes" : "No",
                'type' => 'group',
                'tutor_id' => $tutorId,
                'tutor_name' => $tutorName->name,
                'group_name' => $peerGroup->name,
                'module_name' => $moduleName,
                'session_date' => $booking->session_date,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'notes' => $booking->notes,
                'currentMembers' => $memberCount,
                'total_members' => $peerGroup->total_members,
            ];
        }
        return Inertia::render('Meetings/VideoCall',
        [
            'meetingUrl' => $request->get('meetingUrl'),
            'bookingDetails' => $bookingDetails,
        ]);
    }
}
