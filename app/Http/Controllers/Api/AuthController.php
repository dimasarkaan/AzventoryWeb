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
    use \App\Traits\ActivityLogger;

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
            $userAttempt = User::where('email', $request->email)->first();
            if ($userAttempt) {
                \App\Models\ActivityLog::create([
                    'user_id' => $userAttempt->id,
                    'action' => 'Login Gagal (API)',
                    'description' => "Upaya masuk via API gagal. Kata sandi yang dimasukkan salah.",
                    'properties' => [
                        'ip' => request()->ip(),
                        'user_agent' => request()->header('User-Agent'),
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Check user active status if status column exists
        if (isset($user->status) && strtolower($user->status) != 'aktif') {
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'Login Ditolak (API)',
                'description' => "Upaya login via API ditolak karena akun sedang nonaktif.",
                'properties' => [
                    'ip' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                ],
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak aktif',
            ], 403);
        }

        $token = $user->createToken('API Token '.$request->email)->plainTextToken;

        // Log successful login (Must use ActivityLog::create to set correct user_id since API requests are stateless before token is returned, though Auth::attempt might log them in statefully for this request)
        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Login (API)',
            'description' => "Pengguna berhasil masuk ke sistem melalui API (Mobile).",
            'properties' => [
                'ip' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
            ],
        ]);

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
        $user = $request->user();
        if ($user) {
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'Logout (API)',
                'description' => "Pengguna keluar dari sistem API (Kunci Akses dicabut).",
                'properties' => [
                    'ip' => request()->ip(),
                    'user_agent' => request()->header('User-Agent'),
                ],
            ]);
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ], 200);
    }
}
