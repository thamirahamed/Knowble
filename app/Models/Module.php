<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'module_name',
        'level_id',
        'degree_program_id',
        'semester_id',
    ];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function degreeProgram(): BelongsTo
    {
        return $this->belongsTo(DegreeProgram::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function tutors()
    {
        return $this->belongsToMany(Tutor::class, 'tutor_modules_approved', 'module_id', 'tutor_id');
    }

    public function rejectedTutors()
    {
        return $this->belongsToMany(Tutor::class, 'tutor_modules_rejected', 'module_id', 'tutor_id');
    }


}
