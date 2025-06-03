<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tr_pesan extends Model
{
    use HasFactory;

    protected $table = 'tr_pesans';

    protected $fillable = [
        'incoming_msg_id',
        'outgoing_msg_id',
        'msg',
    ];
}
