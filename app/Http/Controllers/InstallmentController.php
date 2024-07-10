<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\installment;

class InstallmentController extends Controller
{
    public function installList(request $request)
    {
        $query = installment::query()->with('users', 'loan');

        $filters = [
            'name' => 'users.name',
            'code' => 'loan.code',
            'nominalPayment' => 'nominalPayment',
            'date' => 'date',
            'paymentMethod' => 'paymentMethod',
            'paymentStatus' => 'paymentStatus',
            'installmentStatus' => 'installmentStatus',
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
