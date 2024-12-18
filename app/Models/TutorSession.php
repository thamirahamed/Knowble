<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorSession extends Model
{
    protected $fillable = [
        'user_id',
        'tutor_id',
        'status',
        'date',
        'startTime',
        'endTime',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }
}
