<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class tr_invoice extends Model
{
    use HasFactory;

    protected $table = 'tr_invoice';

    protected $fillable = [
        'kode_pemesanan',
        'total_harga',
        'status',
        'user_id',
        'no_invoice'
    ];


    public function order()
    {
        return $this->belongsTo(tr_order::class, 'kode_pemesanan', 'kode_pemesanan');
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            $invoice->no_invoice = Str::upper(Str::random(10));
        });
    }
}
