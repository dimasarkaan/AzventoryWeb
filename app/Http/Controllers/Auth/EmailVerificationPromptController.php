<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Controller penjaga gerbang khusus (Bouncer) untuk mengecek status verifikasi.
// Mengurung pengguna yang belum verifikasi agar tidak bisa kelayapan masuk ke dashboard.
class EmailVerificationPromptController extends Controller
{
    // Menentukan nasib pengguna: Lanjut ke Dashboard atau dilempar ke halaman "Tolong Cek Email"
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : view('auth.verify-email');
    }
}
