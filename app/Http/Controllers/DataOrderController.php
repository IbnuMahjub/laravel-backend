<?php

namespace App\Http\Controllers;

use App\Models\tr_order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataOrderController extends Controller
{
    public function get_data_order()
    {
        $user_id_admin = auth()->user()->id_role_user == 1 ? auth()->user()->id_role_user : null;
        $user_id = auth()->user()->id;
        $data = DB::table('users as u')
            ->join('tr_property as tp', 'u.id', '=', 'tp.user_id')
            ->join('tr_order as to2', 'tp.id', '=', 'to2.property_id')
            ->join('tr_invoice as ti', 'to2.kode_pemesanan', '=', 'ti.kode_pemesanan')
            ->select('u.name as owner', 'tp.name_property', 'to2.id as id_order', 'to2.kode_pemesanan', 'to2.username as nama_pembeli', 'ti.no_invoice', 'ti.status')
            ->where('u.id', $user_id)
            ->get();

        if ($user_id_admin) {
            $data = DB::table('users as u')
                ->join('tr_property as tp', 'u.id', '=', 'tp.user_id')
                ->join('tr_order as to2', 'tp.id', '=', 'to2.property_id')
                ->join('tr_invoice as ti', 'to2.kode_pemesanan', '=', 'ti.kode_pemesanan')
                ->select('u.name as owner', 'tp.name_property', 'to2.id as id_order', 'to2.kode_pemesanan', 'to2.username as nama_pembeli', 'ti.no_invoice', 'ti.status')
                ->get();
        }

        $data = $data->map(function ($item) {
            $item->nama_pembeli = $item->nama_pembeli ?? "";
            return $item;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully.',
            'data' => $data
        ]);
    }
}
