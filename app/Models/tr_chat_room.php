<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tr_chat_room extends Model
{
    use HasFactory;

    protected $table = 'tr_chat_rooms';
    protected $fillable = [
        'name',
    ];

    public function members()
    {
        return $this->belongsToMany(User::class, 'chat_room_members');
    }

    public function chats()
    {
        return $this->hasMany(tr_chat::class);
    }
}
