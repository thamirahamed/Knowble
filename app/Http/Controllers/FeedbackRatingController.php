<?php

namespace App\Http\Controllers;

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
use Illuminate\Http\Request;
use Inertia\Inertia;


class FeedbackRatingController extends Controller
{
    public function createFeedback(Request $request)
    {
        $userid = auth()->user()->id;

        // Validate the request data
        $validatedData = $request->validate([
            'tutor' => 'required|exists:tutors,id',
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:1000',
        ]);

        // Check if the user has completed individual sessions with the tutor
        $hasCompletedIndividualSession = TutorSession::where('tutor_id', $validatedData['tutor'])
            ->where('user_id', $userid)
            ->where('status', 'completed')
            ->exists();

        // Get peer group IDs where the user is either a leader or a member
        $peerGroupIds = PeerGroup::where('leader', $userid)
            ->orWhereIn('id', PeerGroupMember::where('user_id', $userid)->pluck('peer_group_id'))
            ->pluck('id');

        // Check if the user has completed group sessions with the tutor
        $hasCompletedGroupSession = TutorSession::whereIn('peer_group_id', $peerGroupIds)
            ->where('tutor_id', $validatedData['tutor'])
            ->where('status', 'completed')
            ->exists();

        // Combine the checks
        if (!$hasCompletedIndividualSession && !$hasCompletedGroupSession) {
            return redirect()->back()->withErrors([
                'session' => 'You cannot leave feedback without a completed session with this tutor, either individually or as part of a peer group.',
            ]);
        }

        // Check if feedback already exists for the tutor by the user
        $existingFeedback = FeedbackRating::where('user_id', $userid)
                                            ->where('tutor_id', $validatedData['tutor'])
                                            ->first();

        if ($existingFeedback) {
            return redirect()->back()->withErrors([
                'feedback' => 'Feedback already submitted for this tutor.',
            ]);
        }

        // Create a new feedback entry
        FeedbackRating::create([
            'user_id' => $userid,
            'tutor_id' => $validatedData['tutor'],
            'rating' => $validatedData['rating'],
            'feedback' => $validatedData['feedback'],
        ]);

        return redirect()->route('tutor.profile', ['id' => $request->tutor]);
    }

    public function editFeedback(Request $request)
    {
        $userid = auth()->user()->id;

        // Validate the request data
        $validatedData = $request->validate([
            'tutor' => 'required|exists:tutors,id',
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:1000',
        ]);

        // Find the feedback entry for this tutor by the authenticated user
        $feedback = FeedbackRating::where('tutor_id', $validatedData['tutor'])
                                ->where('user_id', $userid)
                                ->first();

        // Check if feedback exists
        if (!$feedback) {
            return redirect()->back()->withErrors([
                'error' => 'Feedback not found. Please ensure you are editing an existing review.',
            ]);
        }

        $feedback->rating = $validatedData['rating'];
        $feedback->feedback = $validatedData['feedback'];
        $feedback->save();
        
        return redirect()->route('tutor.profile', ['id' => $request->tutor]);
    }

    public function deleteFeedback(Request $request)
    {
        $userid = auth()->user()->id;

        // Validate the request data
        $validatedData = $request->validate([
            'tutor' => 'required|exists:tutors,id',
            'feedback' => 'required',
        ]);

        // Find the feedback entry for this tutor by the authenticated user
        $feedback = FeedbackRating::where('id', $validatedData['feedback'])
                                ->first();

        // Check if feedback exists
        if (!$feedback) {
            return redirect()->back()->withErrors([
                'error' => 'Feedback not found. Please ensure you are deleting an existing review.',
            ]);
        }

        // Delete the feedback
        $feedback->delete();

        return redirect()->route('tutor.profile', ['id' => $validatedData['tutor']]);
    }
}
