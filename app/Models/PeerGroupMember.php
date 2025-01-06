<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeerGroupMember extends Model
{
    use HasFactory;

    // Define the table name
    protected $table = 'peer_group_members';

    // Define the fillable fields (attributes that can be mass-assigned)
    protected $fillable = [
        'peer_group_id',
        'user_id',
    ];

    // Define relationships

    // A PeerGroupMember belongs to a PeerGroup
    public function peerGroup()
    {
        return $this->belongsTo(PeerGroup::class);
    }

    // A PeerGroupMember belongs to a User (a Student)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
