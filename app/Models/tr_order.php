<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class tr_order extends Model
{
    use HasFactory;
    protected $table = 'tr_order';

    protected $fillable = [
        'property_id',
        'unit_id',
        'name_property',
        'harga_unit',
        'jumlah_kamar',
        'tanggal_check_in',
        'tanggal_check_out',
        'jumlah_hari',
        'user_id',
        'username',
        'catatan',
        'status',
        'total_harga',
        'kode_pemesanan',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->total_harga = $order->jumlah_hari * $order->harga_unit;
            $order->kode_pemesanan = Str::upper(Str::random(10));
            $order->status = 'unpaid';
        });
    }

    public function invoices()
    {
        return $this->hasMany(tr_invoice::class, 'kode_pemesanan', 'kode_pemesanan');
    }
}
