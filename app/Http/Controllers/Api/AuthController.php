<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

/**
 * @group Authentication
 *
 * Endpoint untuk autentikasi API.
 */
class AuthController extends Controller
{
    /**
     * Login via API
     * 
     * Endpoint ini digunakan untuk mendapatkan Bearer Token JWT/Sanctum melalui Email dan Password.
     * 
     * @unauthenticated
     * 
     * @bodyParam email string required Email milik pengguna (harus terdaftar dan aktif). Example: superadmin@example.com
     * @bodyParam password string required Password akun pengguna. Example: password
     * 
     * @response 200 {
     *   "success": true,
     *   "message": "Login berhasil",
     *   "data": {
     *     "token": "1|abcdef1234567890",
     *     "user": {
     *       "id": 1,
     *       "name": "Super Admin",
     *       "email": "superadmin@example.com",
     *       "role": "superadmin"
     *     }
     *   }
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Email atau password salah"
     * }
     */
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
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Check user active status if status column exists
        if (isset($user->status) && strtolower($user->status) != 'aktif') {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak aktif'
            ], 403);
        }

        $token = $user->createToken('API Token ' . $request->email)->plainTextToken;

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
                ]
            ]
        ], 200);
    }

    /**
     * Logout via API
     * 
     * Menghapus Bearer token (akses untuk perangkat saat ini).
     * 
     * @response 200 {
     *   "success": true,
     *   "message": "Logout berhasil"
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ], 200);
    }
}
