<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeerGroup extends Model
{
    use HasFactory;

    // Define the table name
    protected $table = 'peer_groups';

    // Define the fillable fields (attributes that can be mass-assigned)
    protected $fillable = [
        'name',
        'leader',
        'module_id',
        'total_members',
    ];

    // Define relationships

    // A PeerGroup belongs to a Leader (a User)
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader');
    }

    // A PeerGroup has many PeerGroupMembers
    public function members()
    {
        return $this->hasMany(PeerGroupMember::class);
    }

    // A PeerGroup belongs to a Module (a Module)
    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function sessions()
    {
        return $this->hasMany(Session::class, 'peer_group_id');
    }
}
