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
                    'status' => 'error',
                    'message' => 'No properties found.'
                ], 404);
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
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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

            $imageUrl = $properti->image ? Storage::url($properti->image) : null;

            $properti = Property::with('category')->find($id);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $properti->id,
                    'name' => $properti->name,
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



    public function getUnits()
    {
        $units = Unit::with('property')->get();
        return response()->json($units);
    }

    public function getUnitsByPropertiId($propertiId)
    {
        $units = Unit::with('property')->where('property_id', $propertiId)->get();
        return response()->json($units);
    }

    public function storeUnit(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'tipe' => 'required|string',
            'harga_unit' => 'required|numeric',
            'jumlah_kamer' => 'required|numeric',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('unit-images');
        }
        $unit = Unit::create($validated);
        return response()->json($unit);
    }

    public function updateUnit(Request $request, $id)
    {
        $unit = Unit::find($id);
        if (!$unit) {
            return response()->json([
                'message' => 'Unit not found.'
            ], 404);
        }
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'tipe' => 'required|string',
            'harga_unit' => 'required|numeric',
            'jumlah_kamer' => 'required|numeric',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('unit-images');
        }
        $unit->update($validated);
        return response()->json($unit);
    }

    public function destroyUnit($id)
    {
        $unit = Unit::find($id);
        if (!$unit) {
            return response()->json([
                'message' => 'Unit not found.'
            ], 404);
        }
        $unit->delete();
        return response()->json([
            'message' => 'Unit deleted successfully.'
        ], 200);
    }
}
