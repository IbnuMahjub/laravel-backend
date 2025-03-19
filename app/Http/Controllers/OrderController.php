<?php

namespace App\Http\Controllers;

use App\Models\tr_order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function booking(Request $request)
    {
        try {
            $validated = $request->validate([
                'property_id' => 'required|exists:tr_property,id',
                'unit_id' => 'required|exists:tr_unit,id',
                'name_property' => 'required|string',
                'harga_unit' => 'required|numeric',
                'jumlah_kamar' => 'required|numeric',
                'catatan' => 'nullable|string',
                'tanggal_check_in' => 'required|date',
                'tanggal_check_out' => 'required|date',
                'jumlah_hari' => 'required|numeric',
                'user_id' => 'nullable|exists:users,id',
                'username' => 'nullable|string',
            ]);

            $order = tr_order::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Order created successfully.',
                'data' => $order
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function get_order($kode_pemesanan)
    {
        try {

            $order = tr_order::where('kode_pemesanan', $kode_pemesanan)->first();

            if (!$order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order not found.'
                ]);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Order retrieved successfully.',
                'data' => $order
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }
}
