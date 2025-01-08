<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackRating extends Model
{
    protected $fillable = [
        'rating',
        'feedback',
        'tutor_session_id',
    ];

    public function tutorSession(): BelongsTo
    {
        return $this->belongsTo(TutorSession::class);
    }
}
