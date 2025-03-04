<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tr_property extends Model
{
    use HasFactory, Sluggable;

    protected $table = 'tr_property';
    protected $guarded = ['id'];
    public function category()
    {
        return $this->belongsTo(tm_category::class);
    }

    public function unit()
    {
        return $this->hasMany(tr_unit::class);
    }
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name_property'
            ]
        ];
    }
}
