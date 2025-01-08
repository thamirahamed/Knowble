<?php

namespace App\Http\Controllers;

use App\Events\SessionStatusUpdated;
use App\Models\DegreeProgram;
use App\Models\Level;
use App\Models\ResourceShare;
use App\Models\Semester;
use App\Models\Module;
use App\Models\Profile;
use App\Models\SchoolOfStudy;
use App\Models\Tutor;
use App\Models\User;
use App\Models\TutorSession;
use App\Models\PeerGroup;
use App\Models\PeerGroupMember;
use App\Models\FeedbackRating;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function dashboard()
    {
        $userid = auth()->user()->id;
        $profile = Profile::where('user_id', $userid)->first();

        // Check if profile is null
        if (is_null($profile)) {
            return redirect()->route('profile.create');
        }

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

//        $allsessions = TutorSession::all();
//
//        foreach ($allsessions as $session){
//            $sesssionendtime = $session->end_time;
//            $currenttime = Carbon::now();
//
//
//            if($currenttime > $sesssionendtime){
//                $session->status = 'completed';
//                $session->save();
//
//                broadcast(new SessionStatusUpdated($session));
//
//            }
//        }

        //upcomming sessions and status of the user
        $sessions = TutorSession::where('user_id', $userid)
                                ->where('status', 'booked') // Filter for "booked" status
                                ->get();
        $sessionDetails = [];
        if($sessions->isNotEmpty()){
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

        // Extract module names based on the student's profile
        $studentModuleNames = $studentModules->pluck('module_name'); // Get module names instead of IDs

        // Fetch all peer groups for the student's modules by matching module names
        $peerGroups = PeerGroup::with(['leader', 'module'])
                                ->whereHas('module', function ($query) use ($studentModuleNames) {
                                    $query->whereIn('module_name', $studentModuleNames); // Match by module names
                                })
                                ->get();
        // Format the peer groups data with the required fields
        $formattedPeerGroups = $peerGroups->map(function ($group) {
            $userid = auth()->user()->id;
            $leader = User::where('id', $group->leader)->first();
            $studentProfile = Profile::where('user_id', $userid)->first();
            $leaderProfile = Profile::where('user_id', $leader->id)->first();
            $leaderDegree = DegreeProgram::where('id', $leaderProfile->degree_id)->first();
            $memberCount = PeerGroupMember::where('peer_group_id', $group->id)->count() + 1;
            // Check if the current user is the leader
            $isLeader = $group->leader == $userid;

            // Check if the current user is a member
            $isMember = PeerGroupMember::where('user_id', $userid)
                                        ->where('peer_group_id', $group->id)
                                        ->exists();
            return [
                'id' => $group->id,
                'name' => $group->name,
                'degree' => $leaderDegree->degree_name,
                'module' => $group->module->module_name,  // Assuming the module name is stored as 'module_name'
                'leader' => $leader->name,
                'currentMembers' => $memberCount,
                'totalMembers' => $group->total_members,
                'isUserLeader' => $isLeader,
                'isUserMember' => $isMember,
                'isUserDegree' => $leaderProfile->degree_id == $studentProfile->degree_id,
            ];
        });

        // Sort the peer groups to prioritize the user's degree program
        $sortedPeerGroups = $formattedPeerGroups->sortByDesc('isUserDegree')->values();


        return Inertia::render('Dashboard',[
            'semstertutors' => $tutordetails,
            'allDegree' => $allDegree,
            'tutors' => $degreetutordetails,
            'sessions' => $sessionDetails,
            'sModules' => $studentModules,
            'peerGroups'=>$sortedPeerGroups
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

        // Check if profile is null
        if (is_null($studentprofile)) {
            return redirect()->route('profile.create');
        }

        $studentModules = Module::where('semester_id', $studentprofile->semester_id)
                                ->where('degree_program_id', $studentprofile->degree_id)
                                ->where('level_id', $studentprofile->level_id)
                                ->get();

        // Find the common modules between student and tutor based on 'module_name'
        $commonModules = $studentModules->filter(function ($studentModule) use ($tutorSelectedModules) {
            return $tutorSelectedModules->contains('module_name', $studentModule->module_name);
        });

        $resourcesShared = ResourceShare::where('tutor_id', $id)->get();

        // Map to include only 'id' and 'module_name' if needed
        $commonModules = $commonModules->map(function ($module) {
            return [
                'id' => $module->id,
                'module_name' => $module->module_name,
            ];
        });

        // Check if the user is a leader of any peer group
        $isLeader = PeerGroup::where('leader', $userid)->exists();

        $hasCompletedSessions = TutorSession::where('tutor_id', $tutor->id)
                                            ->where('user_id', $userid)
                                            ->where('status', 'completed')
                                            ->first();

        // Check if the user is a leader of any peer group
        $leaderPeerGroupIds = PeerGroup::where('leader', $userid)->pluck('id');

        // Check if the user is a member of any peer group
        $memberPeerGroupIds = PeerGroupMember::where('user_id', $userid)->pluck('peer_group_id');

        // Combine peer group IDs where the user is either a leader or member
        $allPeerGroupIds = $leaderPeerGroupIds->merge($memberPeerGroupIds)->unique();

        // Check if the user has completed sessions in any of the associated peer groups with the tutor
        $hasGroupCompletedSessions = TutorSession::whereIn('peer_group_id', $allPeerGroupIds)
                                                ->where('tutor_id', $tutor->id)
                                                ->where('status', 'completed')
                                                ->exists();

        // Fetch feedback entries for the specified tutor
        $feedbacks = FeedbackRating::where('tutor_id', $tutor->id)
                                    ->where('user_id', '!=', $userid)
                                    ->with('user') // Load user relationship to get user details
                                    ->get();

        $feedbackDetails = [];

        if($feedbacks->isNotEmpty()){
            // Map the feedback data to include required details
            $feedbackDetails = $feedbacks->map(function ($feedback) {
                $userid = auth()->user()->id;
                $profiles = Profile::where('user_id', $feedback->user_id)->first();
                return [
                    'id' => $feedback->id,
                    'user_name' => $feedback->user->name, // Assuming FeedbackRating belongs to User
                    'pfp' => $profiles->profile_pic, // Assuming FeedbackRating belongs to User
                    'rating' => $feedback->rating,
                    'feedback' => $feedback->feedback,
                ];
            });
        }

        $userFeedback = FeedbackRating::where('tutor_id', $tutor->id)
                                        ->where('user_id', $userid )
                                        ->with('user')
                                        ->first();

        $userFeedbackData = null;
                
        if(!is_null($userFeedback)){
            $userProfile = Profile::where('user_id', $userFeedback->user_id)->first();
            $userFeedbackData = [
                'id' => $userFeedback->id,
                'user_name' => $userFeedback->user->name,
                'rating' => $userFeedback->rating,
                'feedback' => $userFeedback->feedback,
                'pfp' => $userProfile->profile_pic, // Assuming the user has a profile picture
            ];
        }

        
        // Calculate the average rating of the tutor's feedback
        $averageRating = FeedbackRating::where('tutor_id', $tutor->id)->exists()
        ? FeedbackRating::where('tutor_id', $tutor->id)->avg('rating')
        : null;

        // Get the authenticated user
        $user2 = auth()->user();
        // Fetch all peer groups where the user is the leader, along with module names
        $peerGroups = PeerGroup::where('leader', $user2->id)
            ->with('module:id,module_name') // Assuming 'module' relationship exists in PeerGroup model
            ->get(['id', 'name', 'module_id']);
        // Transform the data to include module names
        $peerGroups = $peerGroups->map(function ($peerGroup) {
            return [
                'id' => $peerGroup->id,
                'name' => $peerGroup->name,
                'moduleName' => $peerGroup->module ? $peerGroup->module->module_name : null,
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
            'isLeader' => $isLeader,
            'peerGroups' => $peerGroups,
            'resourcesShared' => $resourcesShared,
            'hasCompletedSession' => $hasCompletedSessions,
            'hasCompletedGroupSession' => $hasGroupCompletedSessions,
            'userFeedback' => $userFeedbackData ,
            'feedbacks' => $feedbackDetails ,
            'avgRating' => $averageRating,
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

    public function createFeedback(Request $request){

    }
}
