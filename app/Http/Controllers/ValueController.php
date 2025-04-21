<?php

namespace App\Http\Controllers;

use App\Models\tm_category;
use App\Models\tr_property;
use Illuminate\Http\Request;

class ValueController extends Controller
{
    public function value_category()
    {
        try {
            $valueId = tm_category::all();
            $response = $valueId->map(function ($valueId) {
                return [
                    'id' => $valueId->id,
                    'name_category' => $valueId->name_category,
                ];
            });
            if ($valueId->isEmpty()) {
                return sendResponse('kosong', [], 'tidak tersedia');
            }
            return sendResponse('success', $response, 'value id dan name category');
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $th->getMessage()
            ], 500);
        }
    }

    public function value_property()
    {
        try {
            $valueId = tr_property::all();
            $response = $valueId->map(function ($valueId) {
                return [
                    'id' => $valueId->id,
                    'name_property' => $valueId->name_property,
                ];
            });
            if ($valueId->isEmpty()) {
                return sendResponse('kosong', [], 'tidak tersedia');
            }
            return sendResponse('success', $response, 'value id dan name property');
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $th->getMessage()
            ], 500);
        }
    }
}
