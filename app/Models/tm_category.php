<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class tm_category extends Model
{
    use HasFactory, Sluggable;

    protected $table = 'tm_category';
    protected $guarded = ['id'];
    public function property()
    {
        return $this->hasMany(tr_property::class);
    }
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name_category'
            ]
        ];
    }
}
