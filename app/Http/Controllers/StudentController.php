<?php

namespace App\Http\Controllers;

use App\Models\DegreeProgram;
use App\Models\Level;
use App\Models\Semester;
use App\Models\Module;
use App\Models\Profile;
use App\Models\SchoolOfStudy;
use App\Models\Tutor;
use App\Models\User;
use App\Models\TutorSession;
use App\Models\PeerGroup;
use App\Models\PeerGroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
        $sessions = TutorSession::where('user_id', $userid)
                                ->where('status', 'booked') // Filter for "booked" status
                                ->get();
        $sessionDetails = [];
        foreach ($sessions as $session) {
            $tutor = Tutor::where('id', $session->tutor_id)->with('user')->first();
            $profiles = Profile::where('user_id', $session->user_id)->first();
            $module = Module::where('id', $session->module_id)->first();
            $studentName = User::where('id', $userid)->first();
            $sessionDetails[] = [
                'id' => $session->id,
                'student_name' => $studentName->name,
                'tutor_name' => $tutor ? $tutor->user->name : 'Unknown Tutor',
                'profile_pic' => $profiles->profile_pic,
                'meeting_url' => $session->meeting_url,
                'module_name' => $module ? $module->module_name : 'Unknown Module',
                'session_date' => $session->session_date,
                'start_time' => $session->start_time,
                'end_time' => $session->end_time,
            ];
        }

        // Sort session details by session_date and then by start_time
        usort($sessionDetails, function ($a, $b) {
            // Compare session_date first
            $dateComparison = strtotime($a['session_date']) - strtotime($b['session_date']);
            if ($dateComparison === 0) {
                // If session_date is the same, compare start_time
                return strtotime($a['start_time']) - strtotime($b['start_time']);
            }
            return $dateComparison;
        });

        // get students current modules
        $userid = auth()->user()->id;
        $studentprofile = Profile::where('user_id', $userid)->first();
        $studentModules = Module::where('semester_id', $studentprofile->semester_id)
                                ->where('degree_program_id', $studentprofile->degree_id)
                                ->where('level_id', $studentprofile->level_id)
                                ->get();

        // Fetch module ids based on the student's profile
        $studentModulesId = Module::where('semester_id', $studentprofile->semester_id)
                                ->where('degree_program_id', $studentprofile->degree_id)
                                ->where('level_id', $studentprofile->level_id)
                                ->pluck('id'); // Only get the module IDs

        
        
        // Fetch all peer groups for the student's modules in a single query with eager loading
        $peerGroups = PeerGroup::with(['leader', 'module'])
                                ->whereIn('module_id', $studentModulesId)
                                ->get();
        // Format the peer groups data with the required fields
        $formattedPeerGroups = $peerGroups->map(function ($group) {
            $leader = User::where('id', $group->leader)->first();
            $leaderProfile = Profile::where('user_id', $leader->id)->first();
            $leaderDegree = DegreeProgram::where('id', $leaderProfile->degree_id)->first();
            $memberCount = PeerGroupMember::where('peer_group_id', $group->id)->count() + 1;
            return [
                'id' => $group->id,
                'name' => $group->name,
                'degree' => $leaderDegree->degree_name,
                'module' => $group->module->module_name,  // Assuming the module name is stored as 'module_name'
                'leader' => $leader->name,
                'currentMembers' => $memberCount,          
                'totalMembers' => $group->total_members,
            ];
        });

        return Inertia::render('Dashboard',[
            'semstertutors' => $tutordetails,
            'allDegree' => $allDegree,
            'tutors' => $degreetutordetails,
            'sessions' => $sessionDetails,
            'sModules' => $studentModules,
            'peerGroups'=>$formattedPeerGroups
        ]);
    }

    public function tutorProfile($id)
    {
        $tutor = Tutor::where('id', $id)->first();
        $profile = Profile::where('user_id', $tutor->user_id)->first();
        $tutorSchool = SchoolOfStudy::find($profile->school_id);
        $tutorDegree = DegreeProgram::find($profile->degree_id);
        $tutorLevel = Level::find($profile->level_id);
        $tutorSemester = Semester::find($profile->semester_id);
        $tutorSelectedModules = $tutor->selectedModules()->get();
        $user = $tutor->user;
        $tutorSessions = TutorSession::where('tutor_id', $id)
                             ->where('status', 'pending')
                             ->get();
        // Convert the collection to an array for sorting
        $tutorsessionsArray = $tutorSessions->toArray();

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
        $tutorSessions = collect($tutorsessionsArray);

        // get students current modules
        $userid = auth()->user()->id;
        $studentprofile = Profile::where('user_id', $userid)->first();
        $studentModules = Module::where('semester_id', $studentprofile->semester_id)
                                ->where('degree_program_id', $studentprofile->degree_id)
                                ->where('level_id', $studentprofile->level_id)
                                ->get();

        // Find the common modules between student and tutor based on 'module_name'
        $commonModules = $studentModules->filter(function ($studentModule) use ($tutorSelectedModules) {
            return $tutorSelectedModules->contains('module_name', $studentModule->module_name);
        });

        // Map to include only 'id' and 'module_name' if needed
        $commonModules = $commonModules->map(function ($module) {
            return [
                'id' => $module->id,
                'module_name' => $module->module_name,
            ];
        });

        return Inertia::render('TutorProfile', [
            'tutor' => $tutor,
            'profile' => $profile,
            'school' => $tutorSchool,
            'degree' => $tutorDegree,
            'level' => $tutorLevel,
            'semester' => $tutorSemester,
            'tutormodules' => $tutorSelectedModules,
            'user' => $user,
            'sessions' => $tutorSessions,
            'commonModules' => $commonModules,
            'studentModules' => $studentModules,
        ]);
    }

    public function bookSession(Request $request)
    {
        $userid = auth()->user()->id;
        $session = TutorSession::find($request->sessionSlot);
        $tutor = Tutor::where('id', $session->tutor_id)->first();
        $tutorName = User::where('id', $tutor->user_id)->first();

        // Base URL
        $baseUrl = "https://sfu.mirotalk.com/join";

        // Generate room name: "Tutor Session with <Tutor Name>"
        $roomName = Str::slug("Tutor Session with {$tutorName->name}", '-');

        // Function to generate random 6-character password
        function generateRandomPassword($length = 6) {
            return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $length);
        }

        // Generate room password
        $roomPassword = generateRandomPassword();

        // Construct the full URL
        $roomUrl = "{$baseUrl}?room={$roomName}&roomPassword={$roomPassword}&audio=0&video=0&screen=0&notify=0&duration=unlimited";

        // Save all session data
        $session->status = 'booked';
        $session->user_id = $userid;
        $session->module_id = $request->module;
        $session->meeting_url = $roomUrl;
        $session->notes = $request->notes;
        $session->save();

        return redirect()->route('tutor.profile', ['id' => $session->tutor_id]);
    }
}
