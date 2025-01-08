<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    protected $fillable = ['user_id', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedModules()
    {
        return $this->belongsToMany(Module::class, 'tutor_modules_approved', 'tutor_id', 'module_id');
    }

    public function  rejectedModules()
    {
        return $this->belongsToMany(Module::class, 'tutor_modules_rejected', 'tutor_id', 'module_id');
    }

    public function rejectMessage()
    {
        return $this->hasOne(RejectMessage::class);
    }

    public function selectedModules()
    {
        return $this->belongsToMany(Module::class, 'tutor_selected_modules', 'tutor_id', 'module_id');
    }

    public function sessions()
    {
        return $this->hasMany(TutorSession::class);
    }

    public function resourceShares()
    {
        return $this->hasMany(ResourceShare::class);
    }
}
