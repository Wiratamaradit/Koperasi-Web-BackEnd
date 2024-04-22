<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\saving;

class SavingController extends Controller
{
    function saveAdd(Request $request)
    {
        $existingsave = DB::table('savings')->where('userId', $request->input('userId'))->first();

        if ($existingsave) {
            return response(["message" => "Anda sudah melakukan pengajuan simpanan"], 400);
        }

        $save = new saving;
        $user = User::find($request->input('userId'));
        $save->userId = $request->input('userId');
        $save->code = 'SMP-' . $user->nik . '-00' . saving::count() + 1;
        $save->nominalPerMonth = $request->input('nominalPerMonth');
        $save->interest = $request->input('interest');
        $save->date = $request->input('date');
        $save->paymentMethod = $request->input('paymentMethod');
        $save->timePeriod = $request->input('timePeriod');

        $save->validationSavingStatus = $request->input('validationSavingStatus') ?? null;
        $save->status = $request->input('status') ?? null;

        if ($save->validationSavingStatus === null) {
            $save->validationSavingStatus = "On-Process";
        }
        if ($save->status === null) {
            $save->status = "INACTIVE";
        }

        $save->save();
        return response()->json(["message" => "Berhasil melakukan pengajuan simpanan", "data" => $save], 200);
    }

    function saveList(request $request)
    {
        $saveQuery = saving::query()->with('user');

        $saveFilters = [
            'code' => 'code',
            'name' => 'user.name',
            'nik' => 'user.nik',
            'nominalPerMonth' => 'nominalPerMonth',
            'interest' => 'interest',
            'date' => 'date',
            'paymentMethod' => 'paymentMethod',
            'timePeriod' => 'timePeriod',
            'validationSavingStatus' => 'validationSavingStatus',
            'status' => 'status',
        ];

        if ($request->has('userId')) {
            $saveFilters['userId'] = 'userId';
        }

        foreach ($saveFilters as $param => $column) {
            $value = $request->query($param);
            if ($value) {
                $saveQuery->where($column, $value);
            }
        }
        $saveData = $saveQuery->get();
        $saveArray = [];
        foreach ($saveData as $save) {
            $saveArray[] = [
                'id' => $save->id,
                'user' => $save->user,
                'code' => $save->code,
                'nominalPerMonth' => $save->nominalPerMonth,
                'interest' => $save->interest,
                'date' => $save->date,
                'paymentMethod' => $save->paymentMethod,
                'timePeriod' => $save->timePeriod,
                'validationSavingStatus' => $save->validationSavingStatus,
                'status' => $save->status,
                'savingpayments' => $save->savingpayments
            ];
        }
        return response()->json(["message" => "Berhasil memuat data", "data" => $saveArray], 200);
    }

    function saveValidationRegion(Request $request, $id)
    {
        $save = saving::find($id);
        if (!$save) {
            return response(["message" => "Simpanan tidak ditemukan"], 404);
        }
        $validationSavingStatus = $request->input('validationSavingStatus');
        if ($validationSavingStatus === "Approved") {
            $save->validationSavingStatus = "Approved";
            $save->status = "ACTIVE";
            $save->save();
            return response(['message' => 'Data sudah sesuai', 'data' => $save], 200);
        } elseif ($validationSavingStatus === "Rejected") {
            $save->validationSavingStatus = "Rejected";
            $save->save();
            return response(['message' => 'Data tidak sesuai', 'data' => $save], 200);
        }

        return response(['message' => 'Data tidak sesuai', 'data' => $save], 400);
    }

}
