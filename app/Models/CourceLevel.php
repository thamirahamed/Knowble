<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourceLevel extends Model
{
    protected $fillable = [
        'cource_id',
        'level',
    ];

    public function cource()
    {
        return $this->belongsTo(Cource::class);
    }
}
