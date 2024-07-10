<?php

namespace App\Http\Controllers;

use App\Models\loantransaction;
use Illuminate\Http\Request;
use App\Models\installment;

class LoantransController extends Controller
{
    function loantransAdd(Request $request)
    {
        $installmentId = $request->input('installmentId');
        $installment = installment::find($installmentId);
        if ($request->hasFile('pdf')) {
            $pdfFile = $request->file('pdf');
            $fileName = 'Bukti_Pembayaran_' . time() . '.' . $pdfFile->getClientOriginalExtension();
            $filePath = $pdfFile->storeAs('public/pdf_files', $fileName);
            $loantrans = new loantransaction;
            $loantrans->installmentId = $installment->id;
            $loantrans->fileName = $fileName;
            $loantrans->receipt = asset('storage/pdf_files/' . $fileName);

            $loantrans->save();
            $installmentQuery = installment::query()->where('id', $installmentId)->first();
            $installmentQuery->paymentStatus = 'PAID';
            $installmentQuery->installmentStatus = 'Lunas';
            $installmentQuery->save();

            return response()->json(['message' => 'File berhasil diunggah.', 'data' => $loantrans], 200);
        } else {

            return response()->json(['message' => 'Tidak ada file yang diunggah.'], 400);
        }
    }

    function loantransList()
    {
        $loantrans = loantransaction::all();
        return response()->json(["message" => "Berhasil menampilkan data", "data" => $loantrans], 200);
    }
}
