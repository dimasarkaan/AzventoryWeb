<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

// Controller untuk memproses klik pada tautan (link) verifikasi di kotak masuk email pengguna.
// Jika tautan valid, akun tersebut resmi disahkan (Verified).
class VerifyEmailController extends Controller
{
    // Menangkap klik dari email, memvalidasi keasliannya, dan mengubah status akun di database
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
