<?php

namespace App\Http\Controllers;

use App\Models\savingpayment;
use Illuminate\Http\Request;

class SavingpaymentController extends Controller
{
    function savepayAdd(Request $request)
    {
        $savepay = new savingpayment;
        $savepay->saveId = $request->input('saveId');
        $savepay->nominal = $request->input('nominal');
        $savepay->paymentMethod = $request->input('paymentMethod');
        $savepay->description = $request->input('description');
        $savepay->status = $request->input('status');
        $savepay->save();

        return response()->json(["message" => "Pembayaran Berhasil"], 200);
    }

    function savepayList()
    {
        $savepay = savingpayment::with('savepayments')->get();
        if ($savepay->isEmpty()) {
            return response()->json(["message" => "Data tidak ditemukan"], 404);  
        }
        return response()->json(["message" => "Berhasil menampilkan data", "data" => $savepay], 200);
    }
}
