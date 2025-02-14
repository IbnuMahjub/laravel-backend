<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertiController extends Controller
{
    public function index()
    {
        try {
            $properti = Property::with('category')->get();
            $dataProperti = $properti->map(function ($property) {
                $imageUrl = $property->image ? Storage::url($property->image) : null;
                return [
                    'id' => $property->id,
                    'name' => $property->name,
                    'slug' => $property->slug,
                    'category' => [

                        'id' => $property->category ? $property->category->id : null,
                        'name' => $property->category ? $property->category->name : null
                    ],
                    'alamat' => $property->alamat,
                    'image' => $imageUrl
                ];
            });
            if ($properti->isEmpty()) {
                return response()->json([
                    'status' => 'kosong',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'data' => $dataProperti,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $properti = Property::with('category')->find($id);
            if (!$properti) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Property not found.'
                ], 404);
            }
            $imageUrl = $properti->image ? Storage::url($properti->image) : null;
            $dataProperti = [
                'id' => $properti->id,
                'name' => $properti->name,
                'slug' => $properti->slug,
                'category' => [
                    'id' => $properti->category ? $properti->category->id : null,
                    'name' => $properti->category ? $properti->category->name : null
                ],
                // 'category' => $properti->category ? $properti->category->name : null,
                'harga' => $properti->harga,
                'alamat' => $properti->alamat,
                'image' => $imageUrl
            ];
            return response()->json([
                'status' => 'success',
                'data' => $dataProperti,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'alamat' => 'required|string',
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('properti-images');
            }

            $savedProperti = Property::create($validated);

            $properti = Property::with('category')->find($savedProperti->id);

            $imageUrl = $properti->image ? Storage::url($properti->image) : null;
            $response = [
                'id' => $properti->id,
                'name' => $properti->name,
                'slug' => $properti->slug,
                'category' => [
                    'id' => $properti->category ? $properti->category->id : null,
                    'name' => $properti->category ? $properti->category->name : null,
                ],
                'category_id' => $properti->category_id,
                'category_name' => $properti->category->name,
                'alamat' => $properti->alamat,
                'image' => $imageUrl,
            ];

            return response()->json([
                'status' => 'success',
                'data' => $response,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $properti = Property::find($id);
            if (!$properti) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Property not found.'
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'alamat' => 'required|string',
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($request->hasFile('image')) {
                if ($properti->image) {
                    Storage::delete($properti->image);
                }
                $validated['image'] = $request->file('image')->store('properti-images');
            }

            $properti->update($validated);
            $properti->slug = null;
            $properti->save();

            $imageUrl = $properti->image ? Storage::url($properti->image) : null;

            $properti = Property::with('category')->find($id);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $properti->id,
                    'name' => $properti->name,
                    'slug' => $properti->slug,
                    'category' => [
                        'id' => $properti->category ? $properti->category->id : null,
                        'name' => $properti->category ? $properti->category->name : null
                    ],
                    'category_id' => $properti->category_id,
                    'category_name' => $properti->category->name,
                    'alamat' => $properti->alamat,
                    'image' => $imageUrl,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $properti = Property::find($id);

            if (!$properti) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Property not found.'
                ], 404);
            }

            if ($properti->image) {
                Storage::delete($properti->image);
            }

            $properti->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Property deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }



    // public function getUnits()
    // {
    //     $units = Unit::with('property')->get();
    //     return response()->json($units);
    // }

    public function getUnits()
    {
        try {
            $units = Unit::with('property')->get();

            $dataUnit = $units->map(function ($unit) {
                // Menangani multiple gambar
                $imageUrls = [];
                if ($unit->images) {
                    $imageUrls = array_map(function ($imagePath) {
                        return Storage::url($imagePath);
                    }, $unit->images);
                }

                return [
                    'id' => $unit->id,
                    'tipe' => $unit->tipe,
                    'property' => [
                        // 'id' => $unit->property->id,
                        // 'name' => $unit->property->name
                        'id' => $unit->property ? $unit->property->id : null,
                        'name' => $unit->property ? $unit->property->name : null
                    ],
                    'harga_unit' => $unit->harga_unit,
                    'jumlah_kamar' => $unit->jumlah_kamar,
                    'deskripsi' => $unit->deskripsi,
                    'images' => $imageUrls  // Mengembalikan array gambar
                ];
            });

            if ($units->isEmpty()) {
                return response()->json([
                    'status' => 'kosong',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'data' => $dataUnit
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }




    public function unitShow($id)
    {
        try {
            $unit = Unit::with('property')->find($id);  // Mengambil unit berdasarkan ID
            if (!$unit) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unit not found.'
                ], 404);
            }

            // Menangani gambar
            $imageUrls = [];
            if ($unit->images) {
                $imageUrls = array_map(function ($imagePath) {
                    return Storage::url($imagePath);  // Mengambil URL gambar menggunakan Storage
                }, $unit->images);
            }

            // Format data yang akan dikirim dalam response
            $dataUnit = [
                'id' => $unit->id,
                'tipe' => $unit->tipe,
                'property' => [
                    'id' => $unit->property ? $unit->property->id : null,
                    'name' => $unit->property ? $unit->property->name : null
                ],
                'harga_unit' => $unit->harga_unit,
                'jumlah_kamar' => $unit->jumlah_kamar,
                'deskripsi' => $unit->deskripsi,
                'images' => $imageUrls
            ];

            return response()->json([
                'status' => 'success',
                'data' => $dataUnit
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeUnit(Request $request)
    {
        try {
            $validated = $request->validate([
                'property_id' => 'required|exists:properties,id',
                'tipe' => 'required|string',
                'harga_unit' => 'required|numeric',
                'jumlah_kamar' => 'required|numeric',
                'deskripsi' => 'required|string',
                'images' => 'required|array|min:1',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePaths[] = $image->store('unit-images');
                }
            }

            $validated['images'] = $imagePaths;

            $unit = Unit::create($validated);

            $unit->images = array_map(function ($imagePath) {
                return Storage::url($imagePath);  // Mendapatkan URL gambar
            }, $unit->images);

            return response()->json([
                'status' => 'success',
                'data' => $unit,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }


    public function updateUnit(Request $request, $id)
    {
        try {
            $unit = Unit::find($id);
            if (!$unit) {
                return response()->json([
                    'message' => 'Unit not found.'
                ], 404);
            }
            $validated = $request->validate([
                'property_id' => 'required|exists:properties,id',
                'tipe' => 'required|string',
                'deskripsi' => 'required|string',
                'harga_unit' => 'required|numeric',
                'jumlah_kamar' => 'required|numeric',
                'images' => 'required|array|min:1',
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePaths[] = $image->store('unit-images');
                }
            }

            $validated['images'] = $imagePaths;

            $unit->update($validated);

            $unit->images = array_map(function ($imagePath) {
                return Storage::url($imagePath);  // Mendapatkan URL gambar
            }, $unit->images);

            return response()->json([
                'status' => 'success',
                'data' => $unit,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyUnit($id)
    {
        try {
            $unit = Unit::find($id);
            if (!$unit) {
                return response()->json([
                    'message' => 'Unit not found.'
                ], 404);
            }
            if ($unit->images) {
                foreach ($unit->images as $image) {
                    Storage::delete($image);
                }
            }
            $unit->delete();
            return response()->json([
                'message' => 'Unit deleted successfully.'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $th->getMessage()
            ]);
        }
    }
}
