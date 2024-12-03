<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DegreeProgram extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'degree_name',
        'school_id',
    ];

    public function schoolOfStudy(): BelongsTo
    {
        return $this->belongsTo(SchoolOfStudy::class);
    }

    public function modules()
    {
        return $this->hasMany(Modules::class);
    }
}
