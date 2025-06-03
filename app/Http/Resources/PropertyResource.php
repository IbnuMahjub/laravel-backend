<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        $imageUrl = $this->image ? Storage::url($this->image) : '';

        $data = [
            'id' => $this->id,
            'name_property' => $this->name_property,
            'name_category' => $this->category ? $this->category->name_category : '',
            'slug' => $this->slug,
            'data_category' => [
                'id' => $this->category ? $this->category->id : '',
                'name_category' => $this->category ? $this->category->name_category : ''
            ],
            'negara' => $this->negara,
            'kota' => $this->kota,
            'alamat' => $this->alamat,
            'kecamatan' => $this->kecamatan,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'image' => $imageUrl,
        ];

        if ($this->relationLoaded('unit')) {
            $data['units'] = $this->unit->map(function ($unit) {
                return [
                    'id' => $unit->id,
                    'name_property' => $unit->name_property,
                    'tipe' => $unit->tipe,
                    'harga_unit' => $unit->harga_unit,
                    'jumlah_kamar' => $unit->jumlah_kamar,
                    'deskripsi' => $unit->deskripsi,
                    'images' => $unit->images ? array_map(fn($img) => Storage::url($img), $unit->images) : [],
                ];
            });
        }

        return $data;
    }
}
