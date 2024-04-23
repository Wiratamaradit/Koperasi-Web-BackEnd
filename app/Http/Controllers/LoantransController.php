<?php

namespace App\Http\Controllers;

use App\Models\loantransaction;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\installment;

class LoantransController extends Controller
{
    function loantransAdd(Request $request)
{
    $installmentId = $request->input('installmentId');
    $installment = installment::find($installmentId);

    if (!$installment) {
        return response()->json(['error' => 'Installment not found'], 404);
    }
    $base64Data = $request->input('base64_data');
    if ($base64Data) {
        $fileData = base64_decode($base64Data);
        $fileName = 'BuktiPembayaran_' . time() . '.pdf';
        $filePath = storage_path('app/pdf_files/' . $fileName);
        file_put_contents($filePath, $fileData);
        $loantrans = new loantransaction;
        $loantrans->installmentId = $installment->id;
        $loantrans->fileName = $fileName;
        $loantrans->receipt = $filePath;
        $loantrans->save();

        return response()->json(['message' => 'File berhasil diunggah.', 'data' => $loantrans], 200);
    } else {
        return response()->json(['error' => 'No file data provided'], 400);
    }
}

    function loantransList()
    {
        $loantrans = loantransaction::all();
        return response()->json(["message" => "Berhasil menampilkan data", "data" => $loantrans], 200);
    }
}
