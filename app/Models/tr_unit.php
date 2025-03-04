<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tr_unit extends Model
{
    use HasFactory;
    protected $table = 'tr_unit';
    protected $fillable = [
        'property_id',
        'tipe',
        'harga_unit',
        'jumlah_kamar',
        'deskripsi',
        'images', // Tambahkan kolom 'images'
    ];

    protected $casts = [
        'images' => 'array', // Pastikan Laravel menganggap 'images' sebagai array
    ];

    public function property()
    {
        return $this->belongsTo(tr_property::class);
    }
}
