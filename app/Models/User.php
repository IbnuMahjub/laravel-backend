<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'avatar',
        'google_id',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function sendPasswordResetNotification($token)
    {

        $url = 'http://127.0.0.1:8081/reset-password?token=' . $token;

        $this->notify(new ResetPasswordNotification($url));
    }

    public function role_user()
    {
        return $this->belongsTo(tm_roleuser::class, 'id_role_user');
    }

    public function sentChats()
    {
        return $this->hasMany(tr_chat::class, 'sender_id');
    }

    // Chat yang diterima user ini
    public function receivedChats()
    {
        return $this->hasMany(tr_chat::class, 'receiver_id');
    }

    // Kalau ada relasi dengan chat_room_members
    public function chatRooms()
    {
        return $this->belongsToMany(tr_chat_room::class, 'chat_room_members');
    }
}
