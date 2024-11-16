<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLevel extends Model
{
    protected $fillable = [
        'course_id',
        'level',
    ];

    public function cource()
    {
        return $this->belongsTo(Course::class);
    }
}
