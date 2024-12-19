<?php

namespace App\Http\Controllers;

use App\Models\DegreeProgram;
use App\Models\Level;
use App\Models\Module;
use App\Models\Profile;
use App\Models\SchoolOfStudy;
use App\Models\Tutor;
use App\Models\TutorSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function dashboard()
    {
        $userid = auth()->user()->id;
        $profile = Profile::where('user_id', $userid)->first();

        $semesterModules = Module::where('semester_id', $profile->semester_id)->where('degree_program_id', $profile->degree_id)->where('level_id', $profile->level_id)->get();

        //get the semesterModules id
        $semesterModulesId = [];
        foreach ($semesterModules as $module) {
            $semesterModulesId[] = $module->id;
        }

        //get the tutors who have selected the modules in the semester
        $tutors = Tutor::whereHas('selectedModules', function ($query) use ($semesterModulesId) {
            $query->whereIn('module_id', $semesterModulesId);
        })->where('user_id', '!=', $userid) // Exclude current user
        ->get();

        //get tutors profile
        $tutorIds = [];
        foreach ($tutors as $tutor) {
            $tutorIds[] = $tutor->user_id;
        }

        //send the tutors with the user details and profile details
        $allDegree = DegreeProgram::where('school_id', $profile->school_id)->get();
        $tutordetails = [];
        foreach ($tutors as $tutor) {
            $tutordetails[] = [
                'user' => $tutor->user,
                'profile' => Profile::where('user_id', $tutor->user_id)->first(),
                'modules' => $tutor->selectedModules,
                'tutor' => $tutor->id,
            ];
        }

        //get all modules of the school
        $degrees = DegreeProgram::where('school_id', $profile->school_id)->get();
        $degreeModules = [];
        foreach ($degrees as $degree) {
            $degreeModules[] = Module::where('degree_program_id', $degree->id)->get();
        }
        // get the tutor who have selected the modules
        $degreeModulesId = [];
        foreach ($degreeModules as $module) {
            foreach ($module as $mod) {
                $degreeModulesId[] = $mod->id;
            }
        }

        $degreetutors = Tutor::whereHas('selectedModules', function ($query) use ($degreeModulesId) {
            $query->whereIn('module_id', $degreeModulesId);
        })->whereNotIn('user_id', $tutorIds) // Exclude tutors from the first set
        ->where('user_id', '!=', $userid) // Exclude current user
        ->get();

        $degreetutorIds = [];
        foreach ($degreetutors as $tutor) {
            $degreetutorIds[] = $tutor->user_id;
        }

        $degreetutordetails = [];
        foreach ($degreetutorIds as $tutor) {
            $degreetutordetail = Tutor::where('user_id', $tutor)->first();
            $degreetutordetails[] = [
                'user' => $degreetutordetail->user,
                'profile' => Profile::where('user_id', $degreetutordetail->user_id)->first(),
                'modules' => $degreetutordetail->selectedModules,
                'tutor' => $degreetutordetail,
            ];
        }

        //upcomming sessions and status of the user
        $sessions = TutorSession::where('user_id', $userid)->get();
        $sessionDetails = [];
        foreach ($sessions as $session) {
            $tutor = Tutor::where('id', $session->tutor_id)->with('user')->first();
            $sessionDetails[] = [
                'tutor_name' => $tutor ? $tutor->user->name : 'Unknown Tutor',
                'status' => $session->status,
                'meeting_url' => $session->meeting_url,
            ];
        }

        return Inertia::render('Dashboard',[
            'semstertutors' => $tutordetails,
            'allDegree' => $allDegree,
            'tutors' => $degreetutordetails,
            'sessions' => $sessionDetails,
        ]);
    }

    public function tutorProfile($id)
    {
        $tutor = Tutor::where('id', $id)->first();
        $profile = Profile::where('user_id', $tutor->user_id)->first();
        $tutorSchool = SchoolOfStudy::find($profile->school_id);
        $tutorDegree = DegreeProgram::find($profile->degree_id);
        $tutorLevel = Level::find($profile->level_id);
        $tutorSelectedModules = $tutor->selectedModules()->get();
        $tutorAvailableTime = $tutor->availableTimes()->get();
        $user = $tutor->user;
        $tutoeSessions = TutorSession::where('tutor_id', $id)->first();

        return Inertia::render('TutorProfile', [
            'tutor' => $tutor,
            'profile' => $profile,
            'school' => $tutorSchool,
            'degree' => $tutorDegree,
            'level' => $tutorLevel,
            'modules' => $tutorSelectedModules,
            'availableTime' => $tutorAvailableTime,
            'user' => $user,
            'sessions' => $tutoeSessions,
        ]);
    }

    public function requestSession(Request $request)
    {
        $userid = auth()->user()->id;

        TutorSession::create([
            'user_id' => $userid,
            'tutor_id' => $request->tutorId,
            'date' => $request->date,
            'startTime' => $request->startTime,
            'endTime' => $request->endTime,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return Inertia::render('/dashboard');
    }

    public function cancelSession($id)
    {

        $session = TutorSession::where('id', $id)->first();
        $session->status = 'Rejected';
        $session->save();

        return redirect()->back();
    }

    public function acceptSession($id)
    {
        $session = TutorSession::where('id', $id)->first();
        $session->status = 'accepted';

        // Generate a meeting link
        $meetingId = uniqid('meet_');
        $meetingUrl = "https://meet.jit.si/$meetingId";

        // Save meeting URL to the session
        $session->meeting_url = $meetingUrl;
        $session->save();

        return redirect()->back();
    }

}
