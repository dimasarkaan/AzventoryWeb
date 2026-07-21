<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

// Controller khusus untuk menangani fitur "Kirim Ulang Email Verifikasi".
// Dipakai kalau pengguna merasa tidak pernah menerima email aktivasi di kotak masuknya.
class EmailVerificationNotificationController extends Controller
{
    // Mengeksekusi pengiriman ulang email berisi link aktivasi
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
