<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

// Controller khusus untuk menangani proses Login & Logout lewat jalur API (biasanya untuk aplikasi Mobile/pihak ketiga).
// Berkomunikasi murni menggunakan teks JSON dan menggunakan sistem Token (Sanctum) sebagai pengganti Session.
class AuthController extends Controller
{
    // Memproses percobaan Login via API
    // Jika email & password benar, sistem akan membuatkan dan memberikan 'Kunci Akses' (Bearer Token)
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Check user active status if status column exists
        if (isset($user->status) && strtolower($user->status) != 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak aktif',
            ], 403);
        }

        $token = $user->createToken('API Token '.$request->email)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->value ?? $user->role,
                ],
            ],
        ], 200);
    }

    // Memproses Logout via API
    // Caranya dengan menghancurkan 'Kunci Akses' (Token) yang sedang dipakai oleh HP/perangkat tersebut
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ], 200);
    }
}
