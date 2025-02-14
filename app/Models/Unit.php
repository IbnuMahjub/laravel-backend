<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
    protected $fillable = [
        'property_id',
        'tipe',
        'harga_unit',
        'jumlah_kamar',
        'deskripsi',
        'images', // Tambahkan kolom 'images'
    ];

    // Jika menggunakan tipe data json pada 'images', tambahkan cast
    protected $casts = [
        'images' => 'array', // Pastikan Laravel menganggap 'images' sebagai array
    ];
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
