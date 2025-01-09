<?php

namespace App\Http\Controllers;

use App\Models\PeerGroup;
use App\Models\PeerGroupMember;
use App\Models\User;
use App\Models\Profile;
use App\Models\DegreeProgram;
use App\Models\Module;
use App\Models\Tutor;
use App\Models\TutorSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;;
use Illuminate\Support\Str;
use Inertia\Inertia;

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
            'pastGroupSessions' => $pastGroupSessionDetails,
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

    // Join a peer group
    public function joinGroup(Request $request)
    {
        $userId = auth()->user()->id;
        $peerGroupId = $request->peer_group_id; // Get the peer group ID from the request

        // Validate the data
        $validated = $request->validate([
            'peer_group_id' => 'required|exists:peer_groups,id', // Check if the group exists
        ]);

        // Check if the peer group exists and isn't full
        $peerGroup = PeerGroup::find($peerGroupId);

        if (!$peerGroup) {
            return back()->withErrors(['peer_group' => 'Peer group not found.']);
        }

        // Check if the user is the leader of the peer group
        if ($peerGroup->leader == $userId) {
            return back()->withErrors(['peer_group' => 'You are the leader of this peer group and cannot join as a regular member.']);
        }

        // Calculate the current member count (including the leader)
        $currentMembers = PeerGroupMember::where('peer_group_id', $peerGroupId)->count() + 1; // +1 to include the leader

        if ($currentMembers >= $peerGroup->total_members) {
            return back()->withErrors(['peer_group' => 'This peer group is already full.']);
        }

        // Check if the user is already a member of the peer group
        $existingMember = PeerGroupMember::where('user_id', $userId)
                                        ->where('peer_group_id', $peerGroupId)
                                        ->exists();

        if ($existingMember) {
            return back()->withErrors(['peer_group' => 'You are already a member of this group.']);
        }

        // Add the user as a member of the peer group
        PeerGroupMember::create([
            'user_id' => $userId,
            'peer_group_id' => $peerGroupId,
        ]);

        return redirect()->route('peergroup', ['id' => $peerGroupId])->with('success', 'Successfully joined the peer group.');
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

        // Delete all members of the peer group
        PeerGroupMember::where('peer_group_id', $peerGroupId)->delete();

        // Delete the peer group
        $peerGroup->delete();

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

}
