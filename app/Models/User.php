<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'id');
    }

    public function tutor()
    {
        return $this->hasOne(Tutor::class, 'id');
    }

    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'chat_user');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function sessions()
    {
        return $this->hasMany(TutorSession::class);
    }

    // A User can be a leader of many PeerGroups
    public function peerGroupsAsLeader()
    {
        return $this->hasMany(PeerGroup::class, 'leader');
    }

    // A User can be a member of many PeerGroups through PeerGroupMember
    public function peerGroups()
    {
        return $this->belongsToMany(PeerGroup::class, 'peer_group_members', 'user_id', 'peer_group_id');
    }

    public function ledPeerGroupSessions()
    {
        return $this->hasManyThrough(Session::class, PeerGroup::class, 'leader_id', 'peer_group_id', 'id', 'id');
    }

    public function givenFeedbacks()
    {
        return $this->hasMany(FeedbackRating::class, 'user_id');
    }
}
