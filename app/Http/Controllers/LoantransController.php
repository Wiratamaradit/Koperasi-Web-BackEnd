<?php

namespace App\Http\Controllers;

use App\Models\loantransaction;
use Illuminate\Http\Request;

class LoantransController extends Controller
{
    function loantransAdd(Request $request)
    {
        $loantrans = new loantransaction;
        $loantrans->installmentId = $request->input('installmentId');
        $loantrans->nominal = $request->input('nominal');
        $loantrans->paymentMethod = $request->input('paymentMethod');
        $loantrans->description = $request->input('description');
        $loantrans->status = $request->input('status');
        $loantrans->save();

        return response()->json(["message" => "Pembayaran Berhasil"], 200);
    }

    function loantransList()
    {
        $loantrans = loantransaction::with('loantransactions')->get();
        if ($loantrans->isEmpty()) {
            return response()->json(["message" => "Data tidak ditemukan"], 404);  
        }
        return response()->json(["message" => "Berhasil menampilkan data", "data" => $loantrans], 200);
    }
}
