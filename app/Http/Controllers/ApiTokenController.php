<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Http\Request;

// Controller khusus untuk urusan tingkat lanjut: Pembuatan Kunci Akses (API Token).
// Token ini berguna kalau nanti aplikasi ini mau dihubungkan ke aplikasi Mobile atau sistem luar.
class ApiTokenController extends Controller
{
    // Memproses pembuatan token/kunci rahasia baru (Hanya Superadmin yang berhak)
    public function store(Request $request)
    {
        // Validasi Role
        if (auth()->user()->role !== UserRole::SUPERADMIN) {
            abort(403, 'Akses Ditolak: Hanya Superadmin yang diizinkan untuk membuat API Token.');
        }

        // Validasi input
        $request->validate([
            'token_name' => 'required|string|max:255',
        ]);

        // Proses generate sanctum token
        // Plain text token hanya akan dikembalikan satu kali
        $token = $request->user()->createToken($request->token_name);

        return back()
            ->with('new_api_token', $token->plainTextToken)
            ->with('success', 'API Token berhasil dibuat. Harap salin token tersebut.');
    }

    // Mencabut dan menghanguskan token yang sudah tidak dipakai agar tidak bisa disalahgunakan
    public function destroy(Request $request, $tokenId)
    {
        // Validasi Role
        if (auth()->user()->role !== UserRole::SUPERADMIN) {
            abort(403, 'Akses Ditolak: Hanya Superadmin yang diizinkan untuk mencabut API Token.');
        }

        // Cari dan hapus token yang cocok milik user bersangkutan (mencegah hapus token user lain)
        $request->user()->tokens()->where('id', $tokenId)->delete();

        return back()->with('success', 'Akses API Token berhasil dicabut secara permanen.');
    }
}
