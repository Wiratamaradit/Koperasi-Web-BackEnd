<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    function userLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response(["message" => "Maaf, Email tidak terdaftar"], 400);
        }
        if ($user->registrationStatus === 'Rejected' || $user->validationStatus === 'Invalid') {
            return response(["message" => "Maaf, Pendaftaran Anda ditolak"], 400);
        }
        if ($user->status === 'INACTIVE') {
            return response(["message" => "Maaf, Pendaftaran sedang dalam proses validasi"], 400);
        }
        if (Auth::attempt($credentials)) {
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }
        return response()->json(['error' => 'User tidak terdaftar'], 400);
    }

    function userList(Request $request)
    {
        $query = User::query();

        $filters = [
            'codeUser' => 'codeUser',
            'name' => 'name',
            'email' => 'email',
            'role' => 'role',
            'nik' => 'nik',
            'employeeStatus' => 'employeeStatus',
            'branchName' => 'branchName',
            'position' => 'position',
            'managerName' => 'managerName',
            'joinDate' => 'joinDate',
            'address' => 'address',
            'phoneNumber' => 'phoneNumber',
            'bankName' => 'bankName',
            'accountNumber' => 'accountNumber',
            'validationStatus' => 'validationStatus',
            'registrationStatus' => 'registrationStatus',
            'status' => 'status',
        ];

        foreach ($filters as $param => $column) {
            $value = $request->query($param);
            if ($value !== null) {
                $query->where($column, $value);
            }
        }

        $users = $query->get();
        return response()->json(["message" => "Berhasil memuat data", "data" => $users], 200);
    }

    function userAdd(Request $request)
    {
        $existingUser = DB::table('users')->where('nik', $request->input('nik'))->first();
        if ($existingUser) {
            return response(["message" => "User sudah terdaftar"], 400);
        }
        $user = new User;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = $request->input('password');
        $user->role = $request->input('role');
        $user->nik = $request->input('nik');
        $user->codeUser = 'AGT-' . $user->nik . '-00' . User::count() + 1;
        $user->employeeStatus = $request->input('employeeStatus');
        $user->branchName = $request->input('branchName');
        $user->position = $request->input('position');
        $user->managerName = $request->input('managerName');
        $user->joinDate = $request->input('joinDate');
        $user->address = $request->input('address');
        $user->phoneNumber = $request->input('phoneNumber');
        $user->bankName = $request->input('bankName');
        $user->accountNumber = $request->input('accountNumber');
        $user->validationStatus = $request->input('validationStatus') ?? null;
        $user->registrationStatus = $request->input('registrationStatus') ?? null;
        $user->status = $request->input('status') ?? null;
        if ($user->validationStatus === null) {
            $user->validationStatus = "On-Process";
        }
        if ($user->registrationStatus === null) {
            $user->registrationStatus = "On-Process";
        }
        if ($user->status === null) {
            $user->status = "INACTIVE";
        }
        $user->save();
        return response(["message" => "User berhasil ditambahkan", "data" => $user], 200);
    }
    function userDelete(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response(["message" => "User tidak ditemukan"], 404);
        }
        $user->delete();
        return response(["message" => "User berhasil dihapus"], 200);
    }

    function userValidationRegion(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response(["message" => "User tidak ditemukan"], 404);
        }
        $validationStatus = $request->input('validationStatus');
        $reason = $request->input('reason');
        if ($validationStatus === "Valid") {
            $user->validationStatus = "Valid";
            $user->save();
            return response(['message' => 'Data sudah sesuai', 'data' => $user], 200);
        } elseif ($validationStatus === "Invalid") {
            $user->validationStatus = "Invalid";
            $user->reason = $reason;
            $user->save();
            return response(['message' => 'Data tidak sesuai', 'data' => $user], 200);
        }
        return response(['message' => 'Data tidak sesuai', 'data' => $user], 400);
    }

    function userValidationGeneral(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response(["message" => "User tidak ditemukan"], 400);
        }
        $registrationStatus = $request->input('registrationStatus');
        if ($registrationStatus === "Approved") {
            $user->registrationStatus = "Approved";
            $user->status = "ACTIVE";
            $user->save();
            return response([
                "message" => 'Anda disetujui bergabung menjadi anggota koperasi',
                'data' => $user
            ], 200);
        } elseif ($registrationStatus === "Rejected") {
            $user->registrationStatus = "Rejected";
            $user->status = "INACTIVE";
            $user->save();
            return response([
                "message" => 'Anda ditolak bergabung menjadi anggota koperasi',
                'data' => $user
            ], 400);
        }
        $user->save();
        return response([
            "message" => "Anda ditolak bergabung menjadi anggota koperasi",
            "data" => $user
        ], 400);
    }
    function userUpdate(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(["message" => "User tidak ditemukan"], 404);
        }
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->nik = $request->input('nik');
        $user->employeeStatus = $request->input('employeeStatus');
        $user->branchName = $request->input('branchName');
        $user->position = $request->input('position');
        $user->managerName = $request->input('managerName');
        $user->joinDate = $request->input('joinDate');
        $user->address = $request->input('address');
        $user->phoneNumber = $request->input('phoneNumber');
        $user->bankName = $request->input('bankName');
        $user->accountNumber = $request->input('accountNumber');
        $user->validationStatus = "On-Process";
        $user->save();

        return response()->json(["message" => "User berhasil diperbarui", "data" => $user], 200);
    }

    function userEdit(Request $request, $id)
    {
        $user = User::find($id);
        return response(["message" => "Data :", "data" => $user], 200);
    }
}
