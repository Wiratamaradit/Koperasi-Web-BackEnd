<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SimulasiController extends Controller
{
    public function loanSimulation(Request $request)
    {
        // $installment = $request->input('installment', []);
        $installment = [];

        $loanTotal = $request->input('loanTotal');
        $interest = $request->input('interest');
        $tenor = $request->input('tenor');

        if ($loanTotal <= 0 || $interest <= 0 || $tenor <= 0) {
            return response()->json(['message' => 'Input tidak valid. Pastikan total pinjaman, bunga, dan tenor memiliki nilai yang valid.'], 400);
        }
        $loanRequest = $loanTotal;
        $interestPerMonth = $loanTotal * $interest;
        $paymentPerMonth = ($loanTotal / $tenor) + ($loanTotal * $interest);
        $paymentTotal = $paymentPerMonth * $tenor;


        for ($i = 1; $i <= $tenor; $i++) {
            $installment[] = [
                'installment' => number_format($loanTotal / $tenor, 2, '.', ''),
                'interest' => $loanTotal * $interest,
                'total' => number_format(($loanTotal / $tenor) + ($loanTotal * $interest), 2, '.', '')
            ];
        }

        return response()->json([
            'data' => [
                'loanTotal' => $loanTotal,
                'interest' => $interest,
                'tenor' => $tenor,
                'loanRequest' => $loanRequest,
                'interestPerMonth' => $interestPerMonth,
                'paymentPerMonth' => $paymentPerMonth,
                'paymentTotal' => $paymentTotal,
            ],
            'installment' => $installment

        ], 200);
    }

    public function savingSimulation(Request $request)
    {

        $saveTotal = $request->input('saveTotal');
        $interest = $request->input('interest');

        if ($saveTotal <= 0 || $interest <= 0) {
            return response()->json(['message' => 'Input tidak valid. Pastikan total pinjaman, bunga, dan tenor memiliki nilai yang valid.'], 400);
        }
        $saveRequest = $saveTotal;
        $timePeriode1 = (($saveTotal * $interest) + $saveTotal) * 12;
        $timePeriode2 = (($saveTotal * $interest) + $saveTotal) * 18;
        $timePeriode3 = (($saveTotal * $interest) + $saveTotal) * 24;

        return response()->json([
            'data' => [
                'saveTotal' => $saveTotal,
                'interest' => $interest,
                'saveRequest' => $saveRequest,
                'timePeriode1' => $timePeriode1,
                'timePeriode2' => $timePeriode2,
                'timePeriode3' => $timePeriode3
            ]
        ], 200);
    }

}
