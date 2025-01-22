<?php

namespace App\Http\Controllers;

use App\Models\PeerGroup;
use App\Models\PeerGroupMember;
use App\Models\User;
use App\Models\Profile;
use App\Models\DegreeProgram;
use App\Models\SchoolOfStudy;
use App\Models\Module;
use App\Models\FeedbackRating;
use App\Models\Tutor;
use App\Models\TutorSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PeerGroupController extends Controller
{
    // Display the list of all available peer groups
    public function index($id)
    {
        // get peer group details
        $userid = auth()->user()->id;
        $peerGroup = PeerGroup::with(['leader', 'module'])  // Eager load 'leader' and 'module'
                                ->where('id', $id)
                                ->first();
        $isLeader = PeerGroup::where('leader', $userid)
                            ->where('id', $id)
                            ->first();
        $isMember = PeerGroupMember::where('user_id', $userid)
                                    ->where('peer_group_id', $id)
                                    ->exists();

        if(is_null($peerGroup)){
            return;
        }

        $profile = Profile::where('user_id', $userid)->first();

        // Check if profile exists, before loading dashboard
        if (is_null($profile)) {
            return redirect()->route('profile.create');
        }

        // Get student's current degree and school of study
        $studentDegree = DegreeProgram::where('id', $profile->degree_id)->first();
        $studentSchool = SchoolOfStudy::where('id', $profile->school_id)->value('id');

        // Get students modules based on profile
        $studentModules = Module::where('semester_id', $profile->semester_id)
                                ->where('degree_program_id', $profile->degree_id)
                                ->where('level_id', $profile->level_id)
                                ->get();

        // Extract module names based on the student's profile
        $studentModuleNames = $studentModules->pluck('module_name'); // Get module names instead of IDs

        // Fetch tutors who are approved and whose modules match the student's modules
        $tutors = Tutor::whereIn('user_id', function ($query) use ($studentSchool) {
            $query->select('user_id')
                ->from('profiles')
                ->where('school_id', $studentSchool);
        })->where('user_id', '!=', $userid) // Exclude the authenticated user
        ->where('status', 'approved') // Only fetch approved tutors
        ->get();

        // Fetch tutor selected modules for these tutors
        $formattedTutors = $tutors->map(function ($tutor) {
            $userid = auth()->user()->id;
            $profile = Profile::where('user_id', $userid)->first();
            $tutorAcc = User::where('id', $tutor->user_id)->first();
            $tutorProfile = Profile::where('user_id', $tutor->user_id)->first();
            $tutorDegree = DegreeProgram::where('id', $tutorProfile->degree_id)->first();
            $studentModuleNames = Module::where('semester_id', $profile->semester_id)
                             ->where('degree_program_id', $profile->degree_id)
                             ->where('level_id', $profile->level_id)
                             ->pluck('module_name'); // Extract module names
            
            // Fetch module names for the current tutor in one query
            $moduleNames = DB::table('tutor_selected_modules')
                            ->where('tutor_id', $tutor->id)
                            ->join('modules', 'modules.id', '=', 'tutor_selected_modules.module_id')
                            ->pluck('module_name');
                            
            // Filter tutors whose selected modules match the student's modules
            $matchingModules = $moduleNames->intersect($studentModuleNames);

            // Calculate the average rating of the tutor's feedback
            $averageRating = FeedbackRating::where('tutor_id', $tutor->id)->exists()
            ? round(FeedbackRating::where('tutor_id', $tutor->id)->avg('rating'), 1)
            : null;

            $averageCancellation = TutorSession::where('tutor_id', $tutor->id)
                                                ->whereIn('status', ['completed', 'cancelled']) // Filter only "completed" and "cancelled" statuses
                                                ->exists()
                                                ? round(
                                                    (TutorSession::where('tutor_id', $tutor->id)->where('status', 'cancelled')->count() /
                                                    TutorSession::where('tutor_id', $tutor->id)->whereIn('status', ['completed', 'cancelled'])->count()) * 100,
                                                    1
                                                )
                                                : null;

            // Only include tutors with matching modules
            if ($matchingModules->isNotEmpty()) {
                return [
                    'id' => $tutor->id,
                    'name' => $tutorAcc->name,
                    'profilePic' => $tutorProfile->profile_pic,
                    'degree' => $tutorDegree->degree_name,
                    'rating' => $averageRating,
                    'cancellation' => $averageCancellation,
                    'modules' => $matchingModules,
                    'matchesUserDegree' => $tutorDegree->id === $profile->degree_id, // Flag if tutor matches the user's degree
                ];
            }

            return null; // No matching modules, so return null (to be filtered out)
        });

        // Filter out null values (tutors with no matching modules)
        $formattedTutors = $formattedTutors->filter()->toArray();

        // Sort tutors so that those who match the user's degree come first
        usort($formattedTutors, function ($a, $b) {
            // First, check if degree matches
            if ($a['matchesUserDegree'] === $b['matchesUserDegree']) {
                // If both match the degree, sort by rating (higher rating first)
                $ratingA = $a['rating'] ?? 0; // Default to 0 if null
                $ratingB = $b['rating'] ?? 0; // Default to 0 if null

                // Compare ratings, higher rating first
                if ($ratingA == $ratingB) {
                    return 0; // If ratings are equal, return 0
                }
                
                return ($ratingB > $ratingA) ? 1 : -1; // Descending order
            }
            
            // Tutors who match the degree come first (return negative value for $a)
            return ($a['matchesUserDegree'] === true) ? -1 : 1;
        });

        // Extract the module details
        $pgModule = $peerGroup->module;
        $school = DegreeProgram::where('id', $pgModule->degree_program_id)->first();

        // Prepare the module details
        $moduleDetails = [
            'module_name' => $pgModule->module_name,
            'school_id' => $school->school_id,
            'semester_id' => $pgModule->semester_id,
            'level_id' => $pgModule->level_id
        ];

        // Fetch all users from the profiles table where school_id, level_id, and semester_id match
        $userProfiles = Profile::where('school_id', $moduleDetails['school_id'])
                                    ->where('level_id', $moduleDetails['level_id'])
                                    ->where('semester_id', $moduleDetails['semester_id'])
                                    ->where('user_id', '!=', $userid)
                                    ->get();

        // Check if there are any profiles
        if (!($userProfiles->isEmpty())) {
            // Fetch all profiles where the user matches the peer group's school, semester, and level
            $peers = $userProfiles->map(function ($profile) use ($pgModule, $id) {
                // Fetch the modules the user is enrolled in based on their profile details
                $modules = Module::where('semester_id', $profile->semester_id)
                                ->where('degree_program_id', $profile->degree_id)
                                ->where('level_id', $profile->level_id)
                                ->get();
                
                // Filter the modules that match the peer group module by name
                $commonModules = $modules->filter(function ($module) use ($pgModule) {
                    return $module->module_name === $pgModule->module_name;
                });
    
                // Only return user if they have common modules with the peer group
                if ($commonModules->isNotEmpty()) {
                    // Check if the user is already part of the peer group
                    $isAlreadyMember = PeerGroupMember::where('peer_group_id', $id)
                                                    ->where('user_id', $profile->user_id)
                                                    ->exists();
                    return [
                        'user_id' => $profile->user_id,
                        'name' => User::find($profile->user_id)->name,  // Get the user name
                        'profilePic' => $profile->profile_pic,
                        'degreeName' => DegreeProgram::find($profile->degree_id)->degree_name,
                        'isMember' => $isAlreadyMember ? 'True' : 'False'
                    ];
                }
            });
        }

        $leader = User::where('id', $peerGroup->leader)->first();
        $leaderProfile = Profile::where('user_id', $leader->id)->first();
        $leaderDegree = DegreeProgram::where('id', $leaderProfile->degree_id)->first();

        // Count members in the peer group and add 1 for the leader
        $memberCount = PeerGroupMember::where('peer_group_id', $id)->count() + 1;

        // Format the data
        $formattedPeerGroup = [
            'id' => $peerGroup->id,
            'name' => $peerGroup->name,
            'degree' => $leaderDegree->degree_name,
            'module' => $peerGroup->module->module_name,  // Assuming 'module_name' is the field in the modules table
            'leader' => $peerGroup->leader,   
            'leaderName' => $leader->name,
            'leaderPfp' => $leaderProfile->profile_pic,
            'isUserLeader' => $isLeader ? "Yes" : "No",    
            'isUserMember' => $isMember ? "Yes" : "No", 
            'currentMembers' => $memberCount,   
            'totalMembers' => $peerGroup->total_members,
        ];

        // Retrieve all the members of the peer group
        $peerGroupMembers = PeerGroupMember::where('peer_group_id', $id)
                                            ->with('user')  // Eager load the related 'user'
                                            ->get();

        // Format the response with necessary fields: id, name, profile_pic, degree
        $formattedMembers = $peerGroupMembers->map(function ($member) {
            $userid = auth()->user()->id;
            $user = $member->user;  // Access the related User model
            $profile = Profile::where('user_id', $user->id)->first();  // Access the related Profile model
            $degree = DegreeProgram::where('id', $profile->degree_id)->first();
            return [
                'id' => $user->id,
                'name' => $user->name,
                'profile_pic' => $profile->profile_pic,  
                'degree' => $degree->degree_name,  
                'isUser' => $user->id === $userid ? "Yes" : "No",  
            ];
        });

        $cancelRequestSessions = TutorSession::where('peer_group_id', $id)
                                            ->where('status', 'cancelRequest') 
                                            ->get();

        $formattedCancelRequests = [];

        if ($cancelRequestSessions->isNotEmpty()) {
            $formattedCancelRequests = $cancelRequestSessions->map(function ($session) use ($id) {
                $tutor = Tutor::where('id', $session->tutor_id)->with('user')->first();
                $profiles = Profile::where('user_id', $tutor->user_id)->first();
                $altSession = TutorSession::find($session->alt_session_id);
                $module = Module::where('id', $session->module_id)->first();
        
                return [
                    'sessionId' => $session->id,
                    'peerGroupId' => $id,
                    'tutor_name' => $tutor ? $tutor->user->name : 'Unknown Tutor',
                    'profile_pic' => $profiles->profile_pic ?? null,
                    'module' => $module->module_name ?? 'Unknown Module',
                    'reason' => $session->notes,
                    'sessionDate' => $session->session_date,
                    'sessionStartTime' => $session->start_time,
                    'sessionEndTime' => $session->end_time,
                    'altSessionId' => $altSession->id ?? null,
                    'altDate' => $altSession->session_date ?? null,
                    'altStartTime' => $altSession->start_time ?? null,
                    'altEndTime' => $altSession->end_time ?? null,
                ];
            })->toArray(); // Convert collection to an array for `usort`
        }
        
        // Sort session details by sessionDate and then by sessionStartTime
        if (!empty($formattedCancelRequests)) {
            usort($formattedCancelRequests, function ($a, $b) {
                // Compare sessionDate first
                $dateComparison = strtotime($a['sessionDate']) - strtotime($b['sessionDate']);
                if ($dateComparison === 0) {
                    // If sessionDate is the same, compare sessionStartTime
                    return strtotime($a['sessionStartTime']) - strtotime($b['sessionStartTime']);
                }
                return $dateComparison;
            });
        }

        // Get all booked sessions for the specified peer group
        $groupSessions = TutorSession::where('peer_group_id', $id)
                                    ->where('status', 'booked')  // Filter for "booked" status
                                    ->get();

        // Initialize session details array
        $groupSessionDetails = [];

        if ($groupSessions->isNotEmpty()) {
            foreach ($groupSessions as $session) {
                $tutor = Tutor::where('id', $session->tutor_id)->with('user')->first();
                $profiles = Profile::where('user_id', $tutor->user_id)->first();
                $module = Module::where('id', $session->module_id)->first();
                $studentName = User::where('id', $userid)->first();  

                // Add session details to the array
                $groupSessionDetails[] = [
                    'id' => $session->id,
                    'student_name' => $studentName->name,
                    'tutor_name' => $tutor ? $tutor->user->name : 'Unknown Tutor',
                    'profile_pic' => $profiles->profile_pic,
                    'meeting_url' => $session->meeting_url,
                    'module_name' => $module ? $module->module_name : 'Unknown Module',
                    'session_date' => $session->session_date,
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                    'notes' => $session->notes,
                ];
            }
        }

        // Get all completed and cancelled sessions for the specified peer group
        $pastGroupSessions = TutorSession::where('peer_group_id', $id)
                        ->whereIn('status', ['completed', 'cancelled'])  // Filter for "completed" and "cancelled" status
                        ->get();

        // Initialize session details array
        $pastGroupSessionDetails = [];

        if ($pastGroupSessions->isNotEmpty()) {
            foreach ($pastGroupSessions as $session) {
                $tutor = Tutor::where('id', $session->tutor_id)->with('user')->first();
                $profiles = Profile::where('user_id', $tutor->user_id)->first();
                $module = Module::where('id', $session->module_id)->first();

                // Add session details to the array
                $pastGroupSessionDetails[] = [
                    'id' => $session->id,
                    'tutor_name' => $tutor ? $tutor->user->name : 'Unknown Tutor',
                    'profile_pic' => $profiles->profile_pic,
                    'module_name' => $module ? $module->module_name : 'Unknown Module',
                    'session_date' => $session->session_date,
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                    'status' => $session->status,  
                    'notes' => $session->notes,
                ];
            }
        }

        // Sort the array by session_date and then start_time
        usort($groupSessionDetails, function ($a, $b) {
            // Compare session_date first
            $dateComparison = strtotime($a['session_date']) - strtotime($b['session_date']);
            if ($dateComparison === 0) {
                // If session_date is the same, compare start_time
                return strtotime($a['start_time']) - strtotime($b['start_time']);
            }
            return $dateComparison;
        });

        // Sort the array by session_date and then start_time
        usort($pastGroupSessionDetails, function ($a, $b) {
            // Compare session_date first
            $dateComparison = strtotime($a['session_date']) - strtotime($b['session_date']);
            if ($dateComparison === 0) {
                // If session_date is the same, compare start_time
                return strtotime($a['start_time']) - strtotime($b['start_time']);
            }
            return $dateComparison;
        });

        return Inertia::render('PeerGroup',[
            'peerGroup'=>$formattedPeerGroup,
            'peerGroupMembers'=>$formattedMembers,
            'groupSessions' => $groupSessionDetails,
            'cancelledSessions' => $formattedCancelRequests,
            'pastGroupSessions' => $pastGroupSessionDetails,
            'peers' => $peers,
            'tutors' => $formattedTutors,
            'sModules' => $studentModules,
        ]);
    }

    // Creating a new peer group
    public function createGroup(Request $request)
    {
        $userid = auth()->user()->id;

        // Validate the incoming data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'module' => 'required',
            'groupSize' => 'required|integer|min:2|max:5', // Group size should be between 2 and 5 (including the leader)
        ]);

        // Check if the user already has a peer group for the selected module
        $existingGroup = PeerGroup::where('leader', $userid)
                        ->where('module_id', $validated['module'])
                        ->where('status', 'opened')
                        ->exists();

        if ($existingGroup) {
        return back()->withErrors(['module' => 'You already have a peer group for this module.']);
        }

        // Create the peer group
        $peerGroup = PeerGroup::create([
            'name' => $validated['name'],
            'module_id' => $validated['module'], // Assuming the module_id is passed correctly
            'total_members' => $validated['groupSize'],
            'leader' => $userid,
        ]);
        return redirect()->route('dashboard');
    }

    // Add member to group
    public function addMember(Request $request)
    {
        $userId = auth()->user()->id;
        $peerGroupId = $request->peer_group_id; // Get the peer group ID from the request
        $peerId = $request->peer_id;

        // Validate the data
        $validated = $request->validate([
            'peer_group_id' => 'required|exists:peer_groups,id', // Check if the group exists
            'peer_id' => 'required|exists:users,id', // Check if the group exists
        ]);

        // Check if the peer group exists and isn't full
        $peerGroup = PeerGroup::find($peerGroupId);

        if (!$peerGroup) {
            return back()->withErrors(['peer_group' => 'Peer group not found.']);
        }

        // Calculate the current member count (including the leader)
        $currentMembers = PeerGroupMember::where('peer_group_id', $peerGroupId)->count() + 1; // +1 to include the leader

        if ($currentMembers >= $peerGroup->total_members) {
            return back()->withErrors(['peer_group' => 'This peer group is already full.']);
        }

        // Check if the user is already a member of the peer group
        $existingMember = PeerGroupMember::where('user_id', $peerId)
                                        ->where('peer_group_id', $peerGroupId)
                                        ->exists();

        if ($existingMember) {
            return back()->withErrors(['peer_group' => 'This peer is already a member of this group.']);
        }

        // Add the user as a member of the peer group
        PeerGroupMember::create([
            'user_id' => $peerId,
            'peer_group_id' => $peerGroupId,
        ]);

        return redirect()->route('peergroup', ['id' => $peerGroupId]);
    }

    // Remove member from group
    public function removeMember(Request $request)
    {
        $userId = auth()->user()->id;
        $peerGroupId = $request->peer_group_id; // Get the peer group ID from the request
        $peerId = $request->peer_id;

        // Validate the data
        $validated = $request->validate([
            'peer_group_id' => 'required|exists:peer_groups,id', // Check if the group exists
            'peer_id' => 'required|exists:users,id', // Check if the group exists
        ]);

        // Check if the peer group exists
        $peerGroup = PeerGroup::find($peerGroupId);

        if (!$peerGroup) {
            return back()->withErrors(['peer_group' => 'Peer group not found.']);
        }

        // Check if the user is a member of the peer group
        $member = PeerGroupMember::where('user_id', $peerId)
                                ->where('peer_group_id', $peerGroupId)
                                ->first();

        if (!$member) {
            return back()->withErrors(['peer_group' => 'This peer is not a member of this group.']);
        }

        // Remove the user from the peer group
        $member->delete();

        return redirect()->route('peergroup', ['id' => $peerGroupId]);
    }

    // Leave peer group
    public function leaveGroup(Request $request)
    {
        $userId = auth()->user()->id;
        $peerGroupId = $request->peer_group_id; // Get the peer group ID from the request

        // Validate the data
        $validated = $request->validate([
            'peer_group_id' => 'required|exists:peer_groups,id', // Check if the group exists
        ]);

        // Check if the peer group exists
        $peerGroup = PeerGroup::find($peerGroupId);

        if (!$peerGroup) {
            return back()->withErrors(['peer_group' => 'Peer group not found.']);
        }

        // Check if the user is a member of the peer group
        $member = PeerGroupMember::where('user_id', $userId)
                                ->where('peer_group_id', $peerGroupId)
                                ->first();

        if (!$member) {
            return back()->withErrors(['peer_group' => 'You are not a member of this group.']);
        }

        // Remove the user from the peer group
        $member->delete();

        return redirect()->route('dashboard');
    }

    // Delete peer group
    public function deleteGroup(Request $request)
    {
        $userId = auth()->user()->id;
        $peerGroupId = $request->peer_group_id; // Get the peer group ID from the request

        // Validate the data
        $validated = $request->validate([
            'peer_group_id' => 'required|exists:peer_groups,id', // Check if the group exists
        ]);

        // Check if the peer group exists
        $peerGroup = PeerGroup::find($peerGroupId);

        if (!$peerGroup) {
            return back()->withErrors(['peer_group' => 'Peer group not found.']);
        }

        // Check if the user is the leader of the peer group
        if ($peerGroup->leader != $userId) {
            return back()->withErrors(['peer_group' => 'Only the leader of the group can delete it.']);
        }

        // Delete the peer group
        $peerGroup->status = 'closed';
        $peerGroup->save();

        return redirect()->route('dashboard');
    }

    public function bookSession (Request $request) {
        // Validate the incoming request
        $validated = $request->validate([
            'peerGroups' => 'required',
            'sessionSlot' => 'required',
            'notes' => 'nullable|string|max:255',
        ]);

        $userId = auth()->user()->id;

        // Find the peer group
        $peerGroup = PeerGroup::find($validated['peerGroups']);
        if (!$peerGroup || $peerGroup->leader !== $userId) {
            return back()->withErrors('error', 'You are not authorized to book a session for this peer group.');
        }

        // Find the session slot
        $session = TutorSession::find($validated['sessionSlot']);
        if (!$session || $session->status === 'booked') {
            return back()->withErrors('error', 'The selected session slot is not available.');
        }

        // Base URL for the meeting
        $baseUrl = "https://sfu.mirotalk.com/join";

        // Generate room name: "Peer Group Session for <Peer Group Name>"
        $roomName = Str::slug("Peer Group Session for {$peerGroup->name}", '-');

        // Generate a random 6-character password
        $roomPassword = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 6);

        // Construct the meeting URL
        $roomUrl = "{$baseUrl}?room={$roomName}&roomPassword={$roomPassword}&audio=0&video=0&screen=0&notify=0&duration=unlimited";

        // Save the session data
        $session->status = 'booked';
        $session->peer_group_id = $peerGroup->id; // Assign the peer group ID
        $session->module_id = $peerGroup->module_id; // Assign the module from the peer group
        $session->meeting_url = $roomUrl;
        $session->notes = $validated['notes'];
        $session->save();

        return redirect()->route('tutor.profile', ['id' => $session->tutor_id]);
    }

    public function acceptCancelRequest (Request $request)
    {
        $userid = auth()->user()->id;
        $session = TutorSession::find($request->sessionId);
        $peerGroup = PeerGroup::find($request->peerGroupId);

        // Base URL
        $baseUrl = "https://sfu.mirotalk.com/join";

        // Generate room name: "Peer Group Session for <Peer Group Name>"
        $roomName = Str::slug("Peer Group Session for {$peerGroup->name}", '-');

        // Function to generate random 6-character password
        function generateRandomPassword($length = 6) {
            return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $length);
        }

        // Generate room password
        $roomPassword = generateRandomPassword();

        // Construct the full URL
        $roomUrl = "{$baseUrl}?room={$roomName}&roomPassword={$roomPassword}&audio=0&video=0&screen=0&notify=0&duration=unlimited";

        $session->status = 'cancelled';
        $session->save();

        $altSession = TutorSession::find($request->altSessionId);
        $altSession->peer_group_id = $peerGroup->id;
        $altSession->module_id = $session->module_id;
        $altSession->status = 'booked';
        $altSession->meeting_url = $roomUrl;
        $altSession->notes = "Rescheduled session of {$session->session_date} | {$session->start_time} - {$session->end_time}";
        $altSession->save();

        return redirect()->route('peergroup', ['id' => $request->peerGroupId]);
    }

    public function denyCancelRequest (Request $request)
    {
        $userid = auth()->user()->id;
        $session = TutorSession::find($request->sessionId);

        $session->status = 'cancelled';
        $session->save();

        $altSession = TutorSession::find($request->altSessionId);
        $altSession->status = 'pending';
        $altSession->save();

        return redirect()->route('peergroup', ['id' => $request->peerGroupId]);
    }

}
