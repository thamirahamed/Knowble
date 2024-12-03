<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Level extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'level_name',
    ];

    public function modules()
    {
        return $this->hasMany(Modules::class);
    }
}
