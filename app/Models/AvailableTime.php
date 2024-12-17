<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailableTime extends Model
{
    protected $fillable = ['tutor_id', 'day', 'start_time', 'end_time'];

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }
}
