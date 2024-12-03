<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolOfStudy extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_name',
    ];

    public function degreePrograms()
    {
        return $this->hasMany(DegreeProgram::class);
    }
}
