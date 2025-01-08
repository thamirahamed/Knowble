<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackRating extends Model
{
    protected $fillable = [
        'user_id',
        'tutor_id',
        'rating',
        'feedback',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tutor()
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }
}
