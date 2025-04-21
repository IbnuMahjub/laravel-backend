<?php

namespace App\Http\Controllers;

use App\Models\tr_invoice;
use App\Models\tr_order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Events\OrderPaidEvent;
use App\Events\SendMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

            $order = tr_order::with('invoices')->where('kode_pemesanan', $kode_pemesanan)->first();

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

    public function invoiceCreate(Request $request)
    {
        try {
            $validated = $request->validate([
                'kode_pemesanan' => 'required|exists:tr_order,kode_pemesanan',
                'total_harga' => 'required|numeric',
                'status' => 'required|string',
                'user_id' => 'nullable|exists:users,id',
            ]);

            $invoice = tr_invoice::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Invoice created successfully.',
                'data' => $invoice
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function get_invoice($no_invoice)
    {
        try {
            $invoice = tr_invoice::with('order')->where('no_invoice', $no_invoice)->first();

            if (!$invoice) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invoice not found.',
                    'data' => []
                ]);
            }

            $dataInvoice = [
                'no_invoice' => $invoice->no_invoice,
                'total_harga' => $invoice->total_harga,
                'status' => $invoice->status,
                'data_order' => [
                    'kode_pemesanan' => $invoice->order->kode_pemesanan,
                    'property_id' => $invoice->order->property_id,
                    'name_property' => $invoice->order->name_property,
                    'harga_unit' => $invoice->order->harga_unit,
                    'jumlah_kamar' => $invoice->order->jumlah_kamar,
                    'catatan' => $invoice->order->catatan,
                    'tanggal_check_in' => $invoice->order->tanggal_check_in,
                    'tanggal_check_out' => $invoice->order->tanggal_check_out,
                    'jumlah_hari' => $invoice->order->jumlah_hari,
                    'user_id' => $invoice->order->user_id  ? $invoice->order->user_id : "",
                    'username' => $invoice->order->username ? $invoice->order->username : "",
                ]
            ];
            return sendResponse('success', $dataInvoice, 'Invoice retrieved successfully.');
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    public function handleMidtransCallback(Request $request)
    {
        try {
            $serverKey = config('midtrans.server_key');
            $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

            if ($hashed !== $request->signature_key) {
                return response()->json(['status' => 'error', 'message' => 'Invalid Signature'], 403);
            }

            $order = tr_order::where('kode_pemesanan', $request->order_id)->first();
            if (!$order) {
                return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
            }

            if ($request->transaction_status == 'settlement' || $request->transaction_status == 'capture') {
                $order->update(['status' => 'paid']);

                $existingInvoice = tr_invoice::where('kode_pemesanan', $order->kode_pemesanan)->first();
                if (!$existingInvoice) {
                    $invoice = tr_invoice::create([
                        'kode_pemesanan' => $order->kode_pemesanan,
                        'total_harga' => $request->gross_amount, // Total harga sesuai pembayaran
                        'status' => 'paid',
                        'user_id' => $order->user_id,
                    ]);
                }


                try {

                    $property = \App\Models\tr_property::find($order->property_id);
                    $owner = \App\Models\User::find($property->user_id);

                    if ($owner) {

                        $token = $owner->api_token;
                        if (!$token) {
                            $token = $owner->createToken('PropertyOwnerToken')->plainTextToken;

                            $owner->api_token = $token;
                            $owner->save();
                        }

                        Http::withToken($token)
                            ->timeout(5)
                            ->get(env('APP_URL') . '/api/send');
                    }
                } catch (\Throwable $e) {
                    Log::error("Failed to notify /api/send: " . $e->getMessage());
                }
            } elseif ($request->transaction_status == 'pending') {
                $order->update(['status' => 'pending']);
            } elseif ($request->transaction_status == 'expire' || $request->transaction_status == 'cancel') {
                $order->update(['status' => 'failed']);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Payment status and invoice updated',
                'data' => $order
            ]);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
        }
    }

    public function countOrder()
    {
        try {

            $dataPaidorder = DB::table('tr_order as to2')
                ->join('tr_property as tp', 'to2.property_id', '=', 'tp.id')
                ->join('users as u', 'tp.user_id', '=', 'u.id')
                ->where('to2.status', 'paid')
                ->where('u.id', auth()->user()->id)
                ->select(
                    'to2.kode_pemesanan',
                    'to2.user_id',
                    'to2.username',
                    'to2.status',
                    'to2.created_at as waktu_pemesanan',
                    'to2.property_id',
                    'to2.unit_id',
                    'to2.name_property',
                    'to2.harga_unit',
                    'to2.jumlah_kamar',
                    'to2.catatan',
                    'to2.tanggal_check_in',
                    'to2.tanggal_check_out',
                    'to2.jumlah_hari',
                    'tp.name_property as nama_properti_asli',
                    'u.name as owner_property'
                )
                ->get();

            $totalPaidOrder = $dataPaidorder->count();


            $dataFix = $dataPaidorder->map(function ($item) {
                foreach ($item as $key => $value) {
                    if (is_null($value)) {
                        $item->$key = "";
                    }
                }
                return $item;
            });


            return response()->json([
                'count' => $totalPaidOrder,
                'dataorder' => $dataFix
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    // test
    // public function send($userId = null)
    // {
    //     if (!$userId && auth()->check()) {
    //         $userId = auth()->user()->id;
    //     }

    //     if (!$userId) {
    //         return response()->json(['message' => 'User ID is required'], 400);
    //     }

    //     $dataPaidorder = DB::table('tr_order as to2')
    //         ->join('tr_property as tp', 'to2.property_id', '=', 'tp.id')
    //         ->join('users as u', 'tp.user_id', '=', 'u.id')
    //         ->where('to2.status', 'paid')
    //         ->where('u.id', $userId)
    //         ->select(
    //             'to2.kode_pemesanan',
    //             'to2.user_id',
    //             'to2.username',
    //             'to2.status',
    //             'to2.created_at as waktu_pemesanan',
    //             'to2.property_id',
    //             'to2.unit_id',
    //             'to2.name_property',
    //             'to2.harga_unit',
    //             'to2.jumlah_kamar',
    //             'to2.catatan',
    //             'to2.tanggal_check_in',
    //             'to2.tanggal_check_out',
    //             'to2.jumlah_hari',
    //             'tp.name_property as nama_properti_asli',
    //             'u.name as owner_property'
    //         )
    //         ->get();

    //     $totalPaidOrder = $dataPaidorder->count();

    //     $dataFix = $dataPaidorder->map(function ($item) {
    //         foreach ($item as $key => $value) {
    //             if (is_null($value)) {
    //                 $item->$key = "";
    //             }
    //         }
    //         return $item;
    //     });

    //     broadcast(new SendMessage($dataFix->toArray(), $totalPaidOrder));

    //     return response()->json([
    //         'message' => 'Order new event sent!',
    //     ]);
    // }


    public function send()
    {
        Log::info('📡 send() method called by user ID: ' . auth()->user()->id);

        $dataPaidorder = DB::table('tr_order as to2')
            ->join('tr_property as tp', 'to2.property_id', '=', 'tp.id')
            ->join('users as u', 'tp.user_id', '=', 'u.id')
            ->where('to2.status', 'paid')
            ->where('u.id', auth()->user()->id)
            ->select(
                'to2.kode_pemesanan',
                'to2.user_id',
                'to2.username',
                'to2.status',
                'to2.created_at as waktu_pemesanan',
                'to2.property_id',
                'to2.unit_id',
                'to2.name_property',
                'to2.harga_unit',
                'to2.jumlah_kamar',
                'to2.catatan',
                'to2.tanggal_check_in',
                'to2.tanggal_check_out',
                'to2.jumlah_hari',
                'tp.name_property as nama_properti_asli',
                'u.name as owner_property'
            )
            ->get();

        $totalPaidOrder = $dataPaidorder->count();

        Log::info('📦 Total paid order found: ' . $totalPaidOrder);

        $dataFix = $dataPaidorder->map(function ($item) {
            foreach ($item as $key => $value) {
                if (is_null($value)) {
                    $item->$key = "";
                }
            }
            return $item;
        });

        Log::info('📤 Broadcasting SendMessage event...');

        broadcast(new SendMessage($dataFix->toArray(), $totalPaidOrder));

        Log::info('✅ SendMessage event broadcasted.');

        return response()->json([
            'message' => 'Order new event sent!',
        ]);
    }
    // public function send()
    // {
    //     $dataPaidorder = DB::table('tr_order as to2')
    //         ->join('tr_property as tp', 'to2.property_id', '=', 'tp.id')
    //         ->join('users as u', 'tp.user_id', '=', 'u.id')
    //         ->where('to2.status', 'paid')
    //         ->where('u.id', auth()->user()->id)
    //         ->select(
    //             'to2.kode_pemesanan',
    //             'to2.user_id',
    //             'to2.username',
    //             'to2.status',
    //             'to2.created_at as waktu_pemesanan',
    //             'to2.property_id',
    //             'to2.unit_id',
    //             'to2.name_property',
    //             'to2.harga_unit',
    //             'to2.jumlah_kamar',
    //             'to2.catatan',
    //             'to2.tanggal_check_in',
    //             'to2.tanggal_check_out',
    //             'to2.jumlah_hari',
    //             'tp.name_property as nama_properti_asli',
    //             'u.name as owner_property'
    //         )
    //         ->get();

    //     $totalPaidOrder = $dataPaidorder->count();

    //     $dataFix = $dataPaidorder->map(function ($item) {
    //         foreach ($item as $key => $value) {
    //             if (is_null($value)) {
    //                 $item->$key = "";
    //             }
    //         }
    //         return $item;
    //     });

    //     broadcast(new SendMessage($dataFix->toArray(), $totalPaidOrder));

    //     return response()->json([
    //         'message' => 'Order new event sent!',
    //         // 'count' => $totalPaidOrder,
    //         // 'dataorder' => $dataFix
    //     ]);
    // }

    // public function send()
    // {
    //     $this->broadcastPaidOrdersForUser(auth()->user()->id);

    //     return response()->json(['message' => 'Order paid event sent!']);
    // }
}
