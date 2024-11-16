<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id', 
        'school_of_study', 
        'year_sem', 
        'cb_number', 
        'profile_pic', 
        'available_times', 
        'role',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

}
