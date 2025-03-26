<?php

namespace App\Http\Controllers;

use App\Models\tr_invoice;
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

            // return response()->json([
            //     'status' => 'success',
            //     'message' => 'Invoice retrieved successfully.',
            //     'data' => $invoice
            // ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }
    }

    // public function handleMidtransCallback(Request $request)
    // {
    //     try {
    //         $serverKey = config('midtrans.server_key');
    //         $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

    //         if ($hashed !== $request->signature_key) {
    //             return response()->json(['status' => 'error', 'message' => 'Invalid Signature'], 403);
    //         }

    //         $order = tr_order::where('kode_pemesanan', $request->order_id)->first();
    //         if (!$order) {
    //             return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
    //         }

    //         if ($request->transaction_status == 'settlement' || $request->transaction_status == 'capture') {
    //             $order->update(['status' => 'paid']);
    //         } elseif ($request->transaction_status == 'pending') {
    //             $order->update(['status' => 'pending']);
    //         } elseif ($request->transaction_status == 'expire' || $request->transaction_status == 'cancel') {
    //             $order->update(['status' => 'failed']);
    //         }

    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Payment status updated',
    //             'data' => $order
    //         ]);
    //     } catch (\Throwable $th) {
    //         return response()->json(['status' => 'error', 'message' => $th->getMessage()], 500);
    //     }
    // }

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

}
