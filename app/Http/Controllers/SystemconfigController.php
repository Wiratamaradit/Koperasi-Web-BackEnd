<?php

namespace App\Http\Controllers;

use App\Models\systemconfig;
use Illuminate\Http\Request;

class SystemconfigController extends Controller
{
    function systemAdd(Request $request)
    {
        $system = new systemconfig;
        $system->interestLoan = $request->input('interestLoan');
        $system->interestSaving = $request->input('interestSaving');
        $system->memberFee = $request->input('memberFee');
        $system->save();

        return response()->json(["message" => "Data berhasil ditambahkan"], 200);
    }

    function systemList()
    {
        $system = systemconfig::with('systemconfigs')->get();
        if ($system->isEmpty()) {
            return response()->json(["message" => "Data tidak ditemukan"], 404);  
        }
        return response()->json(["message" => "Berhasil menampilkan data", "data" => $system], 200);
    }
}
