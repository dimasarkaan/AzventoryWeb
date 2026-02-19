<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Traits\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    use ActivityLogger;

    /**
     * Menampilkan halaman login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Menangani permintaan autentikasi (Login).
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $this->logActivity('Login', 'Pengguna masuk ke sistem.');

        $user = $request->user();
        $redirectPath = match ($user->role) {
            \App\Enums\UserRole::SUPERADMIN => route('dashboard.superadmin'),
            \App\Enums\UserRole::ADMIN => route('admin.dashboard'),
            \App\Enums\UserRole::OPERATOR => route('operator.dashboard'),
            default => route('dashboard', absolute: false),
        };

        return redirect()->intended($redirectPath);
    }

    /**
     * Menghapus sesi autentikasi (Logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->logActivity('Logout', 'Pengguna keluar dari sistem.');

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
