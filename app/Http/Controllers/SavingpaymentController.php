<?php

namespace App\Http\Controllers;

use App\Models\savingpayment;
use App\Models\saving;
use App\Models\User;
use Illuminate\Http\Request;

class SavingpaymentController extends Controller
{
    function savepayAdd(Request $request)
    {
        $saveId = $request->input('saveId');
        $save = saving::find($saveId);
        $userId = $request->input('userId');
        $user = User::find($userId);

        if (!$save) {
            return response()->json(['error' => 'Data saving tidak ditemukan.'], 404);
        }
        $savepay = new savingpayment;
        $savepay->saveId = $saveId;
        $savepay->userId = $userId;
        $savepay->nominal = $request->input('nominal');
        $savepay->payment = ($savepay->nominal * $save->interest) + ($savepay->nominal);
        $savepay->paymentMethod = 'Cash';
        $savepay->date = $request->input('date');
        $savepay->status = 'Paid';
        $savepay->save();

        return response()->json(["message" => "Pembayaran Berhasil"], 200);
    }

    function savepayList(request $request)
    {
        $query = savingpayment::query()->with('users', 'savings');

        $filters = [
            'name' => 'users.name',
            'code' => 'savings.code',
            'nominal' => 'nominal',
            'payment' => 'payment',
            'paymentMethod' => 'paymentMethod',
            'date' => 'date',
            'status' => 'status',
        ];

        if ($request->has('userId')) {
            $filters['userId'] = 'userId';
        }

        foreach ($filters as $param => $column) {
            $value = $request->query($param);
            if ($value) {
                $query->where($column, $value);
            }
        }
        $save = $query->get();
        return response()->json(["message" => "Berhasil memuat data", "data" => $save], 200);
    }
}
