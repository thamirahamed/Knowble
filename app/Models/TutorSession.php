<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorSession extends Model
{
    protected $fillable = [
        'tutor_id',
        'session_date',
        'start_time',
        'end_time',
        'status',
        'date',
        'user_id',
        'module_id',
        'meeting_url',
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

    public function modules()
    {
        return $this->belongsTo(Module::class);
    }
}
