<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

// Controller untuk benteng keamanan ganda (Double-Check Security).
// Bertugas memaksa pengguna mengetik ulang password mereka sebelum diizinkan mengakses menu/tindakan yang sangat sensitif.
class ConfirmablePasswordController extends Controller
{
    // Menampilkan halaman pop-up atau form "Harap Konfirmasi Password Anda"
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    // Mencocokkan password yang diketik dengan yang ada di database
    // Jika cocok, pengguna diberi waktu bebas akses (tanpa konfirmasi lagi) selama beberapa saat
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
