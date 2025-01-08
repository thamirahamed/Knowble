<?php

namespace App\Http\Controllers;

use App\Models\FeedbackRating;
use Illuminate\Http\Request;

class FeedbackRatingController extends Controller
{
    public function index()
    {
        return FeedbackRating::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rating' => ['required', 'numeric'],
            'feedback' => ['nullable'],
            'tutor_session_id' => ['required', 'exists:tutor_sessions'],
        ]);

        return FeedbackRating::create($data);
    }

    public function show(FeedbackRating $feedbackRating)
    {
        return $feedbackRating;
    }

    public function update(Request $request, FeedbackRating $feedbackRating)
    {
        $data = $request->validate([
            'rating' => ['required', 'numeric'],
            'feedback' => ['nullable'],
            'tutor_session_id' => ['required', 'exists:tutor_sessions'],
        ]);

        $feedbackRating->update($data);

        return $feedbackRating;
    }

    public function destroy(FeedbackRating $feedbackRating)
    {
        $feedbackRating->delete();

        return response()->json();
    }
}
