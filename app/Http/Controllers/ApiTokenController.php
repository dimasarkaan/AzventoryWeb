<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use Illuminate\Http\Request;

// Controller khusus untuk urusan tingkat lanjut: Pembuatan Kunci Akses (API Token).
// Token ini berguna kalau nanti aplikasi ini mau dihubungkan ke aplikasi Mobile atau sistem luar.
class ApiTokenController extends Controller
{
    use \App\Traits\ActivityLogger;

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
            'abilities' => 'required|array|min:1',
        ], [
            'abilities.required' => 'Minimal pilih satu hak akses (modul) untuk token ini.',
            'abilities.min' => 'Minimal pilih satu hak akses (modul) untuk token ini.',
        ]);

        // Ambil abilities dari input form, jika kosong atau tidak diset, defaultnya kosong (bukan '*')
        // Namun di UI kita mewajibkan setidaknya memilih, atau jika ingin semua, akan dicentang semua
        $abilities = $request->input('abilities', []);

        // Proses generate sanctum token dengan granular abilities
        $token = $request->user()->createToken($request->token_name, $abilities);

        $this->logActivity('Generate API Token', "Superadmin membuat Kunci API baru dengan label '{$request->token_name}' dan ".count($abilities).' hak akses.');

        return back()
            ->with('new_api_token', $token->plainTextToken)
            ->with('success', 'API Token berhasil dibuat. Harap salin token tersebut.');
    }

    // Memperbarui hak akses (abilities) dari token yang sudah ada
    public function update(Request $request, $tokenId)
    {
        // Validasi Role
        if (auth()->user()->role !== UserRole::SUPERADMIN) {
            abort(403, 'Akses Ditolak: Hanya Superadmin yang diizinkan untuk mengubah API Token.');
        }

        $request->validate([
            'token_name' => 'required|string|max:255',
            'abilities' => 'required|array|min:1',
        ], [
            'abilities.required' => 'Minimal pilih satu hak akses (modul) untuk token ini.',
            'abilities.min' => 'Minimal pilih satu hak akses (modul) untuk token ini.',
        ]);

        // Cari token berdasarkan ID (menggunakan model PersonalAccessToken bawaan Laravel Sanctum)
        $token = \Laravel\Sanctum\PersonalAccessToken::find($tokenId);

        if ($token) {
            $abilities = $request->input('abilities', []);
            $token->forceFill([
                'name' => $request->token_name,
                'abilities' => $abilities,
            ])->save();

            $this->logActivity('Edit API Token', "Superadmin mengubah Kunci API '{$token->name}'.");
        }

        return back()->with('success', 'Hak akses API Token berhasil diperbarui.');
    }

    // Mencabut dan menghanguskan token yang sudah tidak dipakai agar tidak bisa disalahgunakan
    public function destroy(Request $request, $tokenId)
    {
        // Validasi Role
        if (auth()->user()->role !== UserRole::SUPERADMIN) {
            abort(403, 'Akses Ditolak: Hanya Superadmin yang diizinkan untuk mencabut API Token.');
        }

        // Cari token berdasarkan ID (menggunakan model PersonalAccessToken bawaan Laravel Sanctum)
        $token = \Laravel\Sanctum\PersonalAccessToken::find($tokenId);

        if ($token) {
            $tokenName = $token->name;
            $token->delete();
            $this->logActivity('Revoke API Token', "Superadmin mencabut (menghanguskan) Kunci API '{$tokenName}'.");
        }

        return back()->with('api_token_deleted', true)->with('success', 'Akses API Token berhasil dicabut secara permanen.');
    }
}
