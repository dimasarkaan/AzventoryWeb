<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Traits\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

// Controller krusial yang mengurus proses keluar masuk (Login & Logout) pengguna di website.
// Menangani pencocokan email & password, pembuatan sesi (Session), hingga melempar (Redirect) pengguna ke halaman Dashboard yang sesuai dengan pangkat/jabatannya.
class AuthenticatedSessionController extends Controller
{
    use ActivityLogger;

    // Menampilkan halaman antarmuka form Login kepada pengguna
    public function create(): View
    {
        return view('auth.login');
    }

    // Memproses data email & password yang dikirim dari form Login
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $this->logActivity('Login', 'Pengguna masuk ke sistem.');

        $user = $request->user();
        $redirectPath = match ($user->role) {
            \App\Enums\UserRole::SUPERADMIN => route('dashboard.superadmin'),
            \App\Enums\UserRole::ADMIN => route('dashboard.admin'),
            \App\Enums\UserRole::OPERATOR => route('dashboard.operator'),
            default => route('dashboard', absolute: false),
        };

        return redirect()->intended($redirectPath);
    }

    // Memproses aksi Logout saat pengguna mengklik tombol "Keluar"
    public function destroy(Request $request): RedirectResponse
    {
        $this->logActivity('Logout', 'Pengguna keluar dari sistem.');

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
