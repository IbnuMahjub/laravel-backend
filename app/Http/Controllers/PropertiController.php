<?php

namespace App\Http\Controllers;

use App\Http\Resources\PropertyCollection;
use App\Http\Resources\PropertyResource;
use App\Models\tm_category;
use App\Models\tr_property;
use App\Models\tr_unit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PropertiController extends Controller
{
    public function data_property()
    {
        try {
            $properti = tr_property::where('is_delete', 0)
                ->with('category')
                ->paginate(10);
            // ->get();

            if ($properti->isEmpty()) {
                return sendResponse('kosong', [], 'tidak tersedia');
            }
            // return sendResponse('success', PropertyResource::collection($properti), 'all data properti');
            return sendResponse('success', PropertyResource::collection($properti), [
                'current_page' => $properti->currentPage(),
                'last_page' => $properti->lastPage(),
                'per_page' => $properti->perPage(),
                'total' => $properti->total()
            ], 'all data properti');
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $th->getMessage()
            ], 500);
        }
    }
    public function show_data_property($slug)
    {
        try {
            $showProperty = tr_property::with(['category', 'unit'])->where('slug', $slug)->first();

            if (!$showProperty) {
                return sendResponse('kosong', [], 'tidak tersedia');
            }

            // $imageUrl = $showProperty->image ? Storage::url($showProperty->image) : "";

            return sendResponse('success', new PropertyResource($showProperty), 'data properti detail ' .  $showProperty->name_property);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $th->getMessage()
            ]);
        }
    }


    public function index()
    {
        try {
            $properti = tr_property::where('is_delete', 0)
                ->where('user_id', auth()->user()->id)
                ->with('category')
                ->paginate(10);
            if ($properti->isEmpty()) {
                return sendResponse('kosong', [], 'tidak tersedia');
            }

            // return new PropertyCollection($properti);

            // return sendResponse('success', PropertyResource::collection($properti), 'all data properti');
            return sendResponse('success', PropertyResource::collection($properti), [
                'current_page' => $properti->currentPage(),
                'last_page' => $properti->lastPage(),
                'per_page' => $properti->perPage(),
                'total' => $properti->total(),
            ], 'all data properti');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        try {
            $properti = tr_property::with('category')->where('id', $id)->first();
            if (!$properti) {
                return sendResponse('kosong', [], 'tidak tersedia');
            }
            // $imageUrl = $properti->image ? Storage::url($properti->image) : null;
            // $dataProperti = [
            //     'id' => $properti->id,
            //     'name_property' => $properti->name_property,
            //     'slug' => $properti->slug,
            //     'category' => [
            //         'id' => $properti->category ? $properti->category->id : "",
            //         'name_category' => $properti->category ? $properti->category->name_category : ""
            //     ],
            //     'alamat' => $properti->alamat,
            //     'image' => $imageUrl,
            //     'imageOri' => $properti->image,
            //     'negara' => $properti->negara,
            //     'kota' => $properti->kota,
            //     'kecamatan' => $properti->kecamatan ? $properti->kecamatan : "",
            //     'latitude' => $properti->latitude ? $properti->latitude : "",
            //     'longitude' => $properti->longitude ? $properti->longitude : "",
            // ];
            // return sendResponse('success', $dataProperti, 'all data properti');
            return sendResponse('success', new PropertyResource($properti), 'data properti detail ' .  $properti->name_property);
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
                'name_property' => 'required|string|max:255',
                'category_id' => 'required|exists:tm_category,id',
                'alamat' => 'required|string',
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'negara' => 'required|string',
                'kota' => 'required|string',
                'kecamatan' => 'required|string',
                'latitude' => 'required|string',
                'longitude' => 'required|string',
            ]);

            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('properti-images');
            }
            $validated['user_id'] = auth()->user()->id;
            $nameCategory = tm_category::find($validated['category_id']);
            $validated['name_category'] = $nameCategory ? $nameCategory->name_category : "kosong";
            $savedProperti = tr_property::create($validated);

            $properti = tr_property::with('category')->find($savedProperti->id);
            return sendResponse('success', new PropertyResource($properti), 'Berhasil menambahkan data properti ' .  $properti->name_property);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred',
                'details' => $e->getMessage(),
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $properti = tr_property::find($id);
            if (!$properti) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Property not found.'
                ], 404);
            }

            $validated = $request->validate([
                'name_property' => 'required|string|max:255',
                'category_id' => 'required|exists:tm_category,id',
                'alamat' => 'required|string',
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'negara' => 'required|string',
                'kota' => 'required|string',
                'kecamatan' => 'required|string',
                'latitude' => 'required|string',
                'longitude' => 'required|string',
            ]);

            if ($request->hasFile('image')) {
                if ($properti->image) {
                    Storage::delete($properti->image);
                }
                $validated['image'] = $request->file('image')->store('properti-images');
            }

            $nameCategory = tm_category::find($validated['category_id']);
            $validated['name_category'] = $nameCategory ? $nameCategory->name_category : "kosong";

            $properti->update($validated);
            $properti->slug = null;
            $properti->save();

            // $imageUrl = $properti->image ? Storage::url($properti->image) : "";

            $properti = tr_property::with('category')->find($id);

            // return response()->json([
            //     'status' => 'success',
            //     'data' => [
            //         'id' => $properti->id,
            //         'name_property' => $properti->name_property,
            //         'slug' => $properti->slug,
            //         'category' => [
            //             'id' => $properti->category ? $properti->category->id : "",
            //             'name_category' => $properti->category ? $properti->category->name_category : ""
            //         ],
            //         'category_id' => $properti->category_id,
            //         'name_category' => $properti->name_category,
            //         'alamat' => $properti->alamat,
            //         'image' => $imageUrl,
            //         'negara' => $properti->negara,
            //         'kota' => $properti->kota,
            //         'kecamatan' => $properti->kecamatan,
            //         'latitude' => $properti->latitude,
            //         'longitude' => $properti->longitude

            //     ],
            // ], 200);

            return sendResponse('success', new PropertyResource($properti), 'berhasil update ' .  $properti->name_property);
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
            $properti = tr_property::find($id);

            if (!$properti) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Property not found.'
                ], 404);
            }

            if ($properti->image) {
                Storage::delete($properti->image);
            }
            $properti->is_delete = 1;

            $properti->save();
            // $properti->delete();

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

    public function getUnits()
    {
        try {
            $units = tr_unit::with('property')->get();

            $dataUnit = $units->map(function ($unit) {
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
                        'id' => $unit->property ? $unit->property->id : "",
                        'name' => $unit->property ? $unit->property->name_property : ""
                    ],
                    'harga_unit' => $unit->harga_unit,
                    'jumlah_kamar' => $unit->jumlah_kamar,
                    'deskripsi' => $unit->deskripsi,
                    'images' => $imageUrls
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
            $unit = tr_unit::with('property')->find($id);
            if (!$unit) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unit not found.'
                ], 404);
            }

            $imageUrls = [];
            if ($unit->images) {
                $imageUrls = array_map(function ($imagePath) {
                    return Storage::url($imagePath);
                }, $unit->images);
            }
            // $oriImage = null;
            // if ($unit->images) {
            //     $oriImage = $unit->images;
            // }

            // Format data yang akan dikirim dalam response
            $dataUnit = [
                'id' => $unit->id,
                'tipe' => $unit->tipe,
                'property' => [
                    'id' => $unit->property ? $unit->property->id : "",
                    'name_property' => $unit->property ? $unit->property->name_property : ""
                ],

                'harga_unit' => $unit->harga_unit,
                'jumlah_kamar' => $unit->jumlah_kamar,
                'deskripsi' => $unit->deskripsi,
                'images' => $imageUrls,
                'oriImage' => $unit->images
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
                'property_id' => 'required|exists:tr_property,id',
                'tipe' => 'required|string',
                'harga_unit' => 'required|numeric',
                'jumlah_kamar' => 'required|numeric',
                'deskripsi' => 'required|string',
                'images' => 'required|array|min:1',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $nameProperty = tr_property::find($validated['property_id']);

            $validated['name_property'] = $nameProperty ? $nameProperty->name_property : "kosong";

            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePaths[] = $image->store('unit-images');
                }
            }

            $validated['images'] = $imagePaths;

            $unit = tr_unit::create($validated);

            $unit->images = array_map(function ($imagePath) {
                return Storage::url($imagePath);
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
            $unit = tr_unit::find($id);
            if (!$unit) {
                return response()->json([
                    'message' => 'Unit not found.'
                ], 404);
            }
            $validated = $request->validate([
                'property_id' => 'required|exists:tr_property,id',
                'tipe' => 'required|string',
                'deskripsi' => 'required|string',
                'harga_unit' => 'required|numeric',
                'jumlah_kamar' => 'required|numeric',
                'images' => 'array|min:1',
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePaths[] = $image->store('unit-images');
                }
                $validated['images'] = $imagePaths;
            }


            $unit->update($validated);

            $unit->images = array_map(function ($imagePath) {
                return Storage::url($imagePath);
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
            $unit = tr_unit::find($id);
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
