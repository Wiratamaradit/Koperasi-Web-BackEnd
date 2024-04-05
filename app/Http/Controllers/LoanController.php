<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\loan;
use App\Models\installment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{

    public function loanAdd(Request $request)
    {
        $existingloan = DB::table('loans')->
            where('userId', $request->input('userId'))->
            first();

        if ($existingloan) {
            return response(["message" => "Anda sudah melakukan pinjaman"], 400);
        }

        $loan = new loan;
        $user = User::find($request->input('userId'));
        $loan->userId = $request->input('userId');
        $loan->code = 'PJM-' . $user->nik . '-00' . loan::count() + 1;
        $loan->nominal = $request->input('nominal');
        $loan->interest = $request->input('interest');
        $loan->tenor = $request->input('tenor');
        $loan->date = $request->input('date');
        $loan->description = $request->input('description');

        $loan->validationLoanStatus = $request->input('validationLoanStatus') ?? null;
        $loan->loanStatus = $request->input('loanStatus') ?? null;
        $loan->status = $request->input('status') ?? null;

        if ($loan->validationLoanStatus === null) {
            $loan->validationLoanStatus = "On-Process";
        }
        if ($loan->loanStatus === null) {
            $loan->loanStatus = "On-Process";
        }
        if ($loan->status === null) {
            $loan->status = "INACTIVE";
        }
        $loan->save();

        if ($loan->id) {
            for ($i = 1; $i <= $loan->tenor; $i++) {
                $installment = new installment;
                $installment->loanId = $loan->id;
                $installment->userId = $loan->userId;
                $installment->nominalPayment = ($loan->nominal / $loan->tenor) + ($loan->nominal * 0.002);
                $installment->paymentMethod = 'Payroll';
                $installment->paymentStatus = $request->input('paymentStatus') ?? null;
                $installment->installmentStatus = $request->input('installmentStatus') ?? null;
                $installment->date = Carbon::parse($loan->date)->addMonths($i + 1);

                if ($installment->paymentStatus === null) {
                    $installment->paymentStatus = "Unpaid";
                }
                if ($installment->installmentStatus === null) {
                    $installment->installmentStatus = "On-Process";
                }
                $installment->save();
            }

            return response()->json([
                "message" => "Berhasil melakukan pengajuan pinjaman",
                "data" => [
                    "loan" => $loan,
                    "installment" => [$installment]
                ]
            ], 200);
        } else {
            return response()->json(["message" => "Gagal menyimpan pinjaman"], 500);
        }
    }

    function loanList(request $request)
    {
        // dd($request->userId);
        $query = loan::query()->with('users');

        $filters = [
            'code' => 'code',
            'name' => 'users.name',
            'nik' => 'users.nik',
            'nominal' => 'nominal',
            'interest' => 'interest',
            'tenor' => 'tenor',
            'date' => 'date',
            'description' => 'description',
            'loanStatus' => 'loanStatus',
            'validationLoanStatus' => 'validationLoanStatus',
            'status' => 'status',
        ];

        if ($request->has("userId")) {
            $filters["userId"] = "userId";
        }

        foreach ($filters as $param => $column) {
            $value = $request->query($param);
            if ($value) {
                $query->where($column, $value);
            }
        }
        $loan = $query->get();
        return response()->json(["message" => "Berhasil memuat data", "data" => $loan], 200);
    }

    function loanValidationRegion(Request $request, $id)
    {
        $loan = loan::find($id);
        if (!$loan) {
            return response(["message" => "Pinjaman tidak ditemukan"], 404);
        }
        $validationLoanStatus = $request->input('validationLoanStatus');
        if ($validationLoanStatus === "Valid") {
            $loan->validationLoanStatus = "Valid";
            $loan->save();
            return response(['message' => 'Data sudah sesuai', 'data' => $loan], 200);
        } elseif ($validationLoanStatus === "Invalid") {
            $loan->validationLoanStatus = "Invalid";
            $loan->save();
            return response(['message' => 'Data tidak sesuai', 'data' => $loan], 200);
        }

        return response(['message' => 'Data tidak sesuai', 'data' => $loan], 400);
    }

    function loanValidationGeneral(Request $request, $id)
    {
        $loan = loan::find($id);
        if (!$loan) {
            return response(["message" => "Pinjaman tidak ditemukan"], 400);
        }
        $loanStatus = $request->input('loanStatus');
        if ($loanStatus === "Approved") {
            $loan->loanStatus = "Approved";
            $loan->status = "ACTIVE";
            $loan->save();

            if ($loan->status === 'ACTIVE') {
                DB::installment(function () use ($loan) {
                    $this->installAdd($loan);
                });
            }
            return response(["message" => 'Pinjaman anda sudah disetujui', 'data' => $loan], 200);
        } elseif ($loanStatus === "Rejected") {
            $loan->loanStatus = "Rejected";
            $loan->status = "INACTIVE";
            $loan->save();
            return response(["message" => 'Pinjaman anda tidak disetujui', 'data' => $loan], 400);
        }
        $loan->save();
        return response(["message" => "Pinjaman anda ditolak", "data" => $loan], 400);
    }


}
