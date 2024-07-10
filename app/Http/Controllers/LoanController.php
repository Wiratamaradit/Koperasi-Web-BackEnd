<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\loan;
use App\Models\installment;
use Carbon\Carbon;

class LoanController extends Controller
{
    public function loanAdd(Request $request)
    {
        $user = User::find($request->input('userId'));
        if (!$user) {
            return response(["message" => "Pengguna tidak ditemukan."], 404);
        }

        $existingLoan = $user->loans()->where('status', 'ACTIVE')->first();
        if ($existingLoan) {
            return response([
                "message" => "Anda sudah memiliki pinjaman aktif, 
                tidak dapat melakukan pengajuan pinjaman lagi."
            ], 400);
        }

        $loan = new Loan;
        $loan->userId = $user->id;
        $loan->code = 'PJM-' . $user->nik . '-' . sprintf('%03d', Loan::count() + 1);
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
        $loan->reason = $request->input('reason');
        $loan->save();
        return response(["message" => "Berhasil melakukan pengajuan pinjaman", "data" => $loan], 200);
    }

    function loanList(request $request)
    {
        $loanQuery = loan::query()->with('users', 'installments');

        $loanFilters = [
            'code' => 'code',
            'userId' => 'userId',
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
            'reason' => 'reason',
        ];

        if ($request->has("userId")) {
            $loanFilters["userId"] = "userId";
        }

        foreach ($loanFilters as $param => $column) {
            $value = $request->query($param);
            if ($value) {
                $loanQuery->where($column, $value);
            }
        }

        $loanData = $loanQuery->get();
        $loanArray = [];
        foreach ($loanData as $loan) {
            $installments = [];
            foreach ($loan->installments as $installment) {
                $installmentData = $installment->toArray();
                $installmentData['installmentPayment'] = $installment->getInstallmentPayment();
                $installments[] = $installmentData;
            }
            $loanArray[] = [
                "id" => $loan->id,
                "user" => $loan->users,
                "code" => $loan->code,
                "userId" => $loan->userId,
                "nominal" => $loan->nominal,
                "interest" => $loan->interest,
                'tenor' => $loan->tenor,
                'date' => $loan->date,
                'description' => $loan->description,
                'loanStatus' => $loan->loanStatus,
                'validationLoanStatus' => $loan->validationLoanStatus,
                'status' => $loan->status,
                'reason' => $loan->reason,
                'installments' => $installments,
            ];
        }

        return response()->json([
            "message" => "Berhasil memuat data",
            "data" => $loanArray
        ], 200);
    }

    function loanValidationRegion(Request $request, $id)
    {
        $loan = loan::find($id);
        if (!$loan) {
            return response(["message" => "Pinjaman tidak ditemukan"], 404);
        }
        $validationLoanStatus = $request->input('validationLoanStatus');
        $reason = $request->input('reason');
        if ($validationLoanStatus === "Valid") {
            $loan->validationLoanStatus = "Valid";
            $loan->save();
            return response(['message' => 'Data sudah sesuai', 'data' => $loan], 200);
        } elseif ($validationLoanStatus === "Invalid") {
            $loan->validationLoanStatus = "Invalid";
            $loan->reason = $reason;
            $loan->save();
            return response(['message' => 'Data tidak sesuai', 'data' => $loan], 200);
        } elseif ($validationLoanStatus === "Revisions") {
            $loan->validationLoanStatus = "Revisions";
            $loan->reason = $reason;
            $loan->save();
            return response(["message" => 'Pinjaman anda sedang di revisi', 'data' => $loan], 200);
        }
        return response(['message' => 'Data tidak sesuai', 'data' => $loan], 400);
    }

    function loanValidationGeneral(Request $request, $id)
    {
        $loan = Loan::find($id);
        if (!$loan) {
            return response(["message" => "Pinjaman tidak ditemukan"], 400);
        }
        $loanStatus = $request->input('loanStatus');
        $reason = $request->input('reason');
        if ($loanStatus === "Approved") {
            $loan->loanStatus = "Approved";
            $loan->status = "ACTIVE";
            $loan->save();
            // Membuat angsuran
            $installments = [];
            for ($i = 1; $i <= $loan->tenor; $i++) {
                $installment = new Installment;
                $installment->loanId = $loan->id;
                $installment->userId = $loan->userId;
                $installment->nominalPayment = ($loan->nominal / $loan->tenor) + ($loan->nominal * 0.02);
                $installment->paymentMethod = 'Payroll';
                $installment->paymentStatus = $request->input('paymentStatus') ?? 'UnPaid';
                $installment->installmentStatus = $request->input('installmentStatus') ?? 'On-Process';
                $installment->date = Carbon::parse($loan->date)->addMonths($i);
                $installment->save();
                $installments[] = $installment;
            }
            return response()->json([
                "message" => "Berhasil melakukan pengajuan pinjaman",
                "data" => [
                    "loan" => $loan,
                    "installment" => $installments
                ]
            ], 200);
        } elseif ($loanStatus === "Rejected") {
            $loan->loanStatus = "Rejected";
            $loan->reason = $reason;
            $loan->status = "INACTIVE";
            $loan->save();
            return response(["message" => 'Pinjaman anda tidak disetujui', 'data' => $loan], 400);
        }

        return response(["message" => "Pinjaman anda ditolak", "data" => $loan], 400);
    }

    function loanUpdate(Request $request, $id)
    {
        $loan = loan::find($id);

        if (!$loan) {
            return response()->json(["message" => "Pinjaman tidak ditemukan"], 404);
        }

        $userId = $request->input('userId');
        $user = User::find($userId);

        $loan->nominal = $request->input('nominal');
        $loan->interest = $request->input('interest');
        $loan->tenor = $request->input('tenor');
        $loan->date = $request->input('date');
        $loan->description = $request->input('description');
        $loan->validationLoanStatus = "On-Process";
        $loan->loanStatus = $request->input('loanStatus');
        $loan->status = $request->input('status');
        $loan->reason = $request->input('reason');
        $loan->save();

        return response()->json(["message" => "Pinjaman berhasil diperbarui", "data" => $loan], 200);
    }

    function loanEdit(Request $request, $id)
    {
        $loan = loan::find($id);
        return response(["message" => "Data :", "data" => $loan], 200);
    }
}
