<?php

namespace App\Http\Controllers\Inventory\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @group Profile & Account
 *
 * API endpoints terkait data akun user yang sedang aktif dan riwayat transaksinya.
 */
class ProfileController extends Controller
{
    /**
     * Mendapatkan profil pengguna yang sedang login.
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'avatar_url' => $user->avatar ? asset('storage/'.$user->avatar) : null,
                'stats' => [
                    'total_borrowed' => $user->borrowings()->count(),
                    'active_borrows' => $user->borrowings()->where('status', 'borrowed')->count(),
                ],
                'created_at' => $user->created_at,
            ],
        ]);
    }

    /**
     * Update profil via API.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
            // Password update sebaiknya di endpoint terpisah atau via web demi keamanan tinggi
        ]);

        $user->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui via API',
            'data' => $user,
        ]);
    }
}
