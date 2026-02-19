<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Menampilkan halaman permintaan link reset password.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Menangani permintaan link reset password yang masuk.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Kita akan mengirimkan link reset password ke pengguna ini. Setelah kita
        // mencoba mengirimkan link, kita akan memeriksa respon kemudian melihat pesan
        // yang perlu kita tampilkan ke pengguna. Akhirnya, kita akan mengirimkan respon yang tepat.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
