<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'level_id',
        'degree_id',
        'school_id',
        'semester_id',
        'cb_number',
        'profile_pic',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function degree()
    {
        return $this->belongsTo(DegreeProgram::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function school()
    {
        return $this->belongsTo(SchoolOfStudy::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

}
