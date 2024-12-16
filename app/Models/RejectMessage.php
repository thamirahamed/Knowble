<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RejectMessage extends Model
{
    protected $fillable = ['message'];

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }
}
