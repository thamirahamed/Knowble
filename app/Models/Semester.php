<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Semester extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'semester_name',
    ];

    public function modules()
    {
        return $this->hasMany(Module::class);
    }
}
