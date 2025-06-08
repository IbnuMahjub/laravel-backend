<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tr_chat extends Model
{
    use HasFactory;

    protected $table = 'tr_chats';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'message_type',
        'room_id',
        'is_read',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
