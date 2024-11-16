<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'CourseName',
    ];

    public function levels()
    {
        return $this->hasMany(CourseLevel::class);
    }
}
