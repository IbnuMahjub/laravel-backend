<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tr_chat_room_members extends Model
{
    use HasFactory;

    protected $table = 'tr_chat_room_members';

    protected $fillable = [
        'room_id',
        'user_id',
    ];

    public function room()
    {
        return $this->belongsTo(tr_chat_room::class, 'room_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
