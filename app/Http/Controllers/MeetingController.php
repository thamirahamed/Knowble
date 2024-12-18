<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MeetingController extends Controller
{
    // Show meeting dashboard
    public function index()
    {
        return Inertia::render('Meetings/Index');
    }

    // Create a new meeting
    public function create(Request $request)
    {
        try {
            // Validate the input
            $request->validate([
                'host_name' => 'required|string|max:255',
            ]);

            // Generate a unique meeting ID
            $meetingId = uniqid('meet_');

            // Create the meeting in the database
            $meeting = Meeting::create([
                'meeting_id' => $meetingId,
                'host_name' => $request->host_name,
            ]);

            // Return the meeting URL
            return response()->json([
                'meeting_url' => "https://meet.jit.si/$meetingId",
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error creating meeting: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
