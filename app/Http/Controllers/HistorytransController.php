<?php

namespace App\Http\Controllers;

use App\Models\historytransaction;
use App\Models\savingpayment;
use Illuminate\Http\Request;

class HistorytransController extends Controller
{
    function historyAdd(Request $request)
    {
        $history = new historytransaction;
        $history->savepayId = $request->input('savetpayId');
        $history->loantransId = $request->input('loantransId');
        $history->nominal = $request->input('nominal');
        $history->paymentMethod = $request->input('paymentMethod');
        $history->description = $request->input('description');
        $history->status = $request->input('status');
        $history->save();

        return response()->json(["message" => "Data berhasil ditambahkan"], 200);
    }

    function historyList()
    {
        $history = historytransaction::with('historytransactions')->get();
        if ($history->isEmpty()) {
            return response()->json(["message" => "Data tidak ditemukan"], 404);  
        }
        return response()->json(["message" => "Berhasil menampilkan data", "data" => $history], 200);
    }
}
