<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cource extends Model
{
    protected $fillable = [
        'CourseName',
    ];

    public function levels()
    {
        return $this->hasMany(CourceLevel::class);
    }
}
