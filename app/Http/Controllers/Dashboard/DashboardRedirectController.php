<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Controller kecil pengatur lalu lintas (Router).
// Tugasnya hanya satu: Mengecek pangkat/jabatan pengguna dan melempar (Redirect) mereka ke halaman Dashboard masing-masing.
class DashboardRedirectController extends Controller
{
    // Mengeksekusi pengalihan halaman secara otomatis saat rute '/dashboard' diakses
    public function __invoke(Request $request)
    {
        $user = auth()->user();

        return match ($user->role) {
            UserRole::SUPERADMIN => redirect()->route('dashboard.superadmin'),
            UserRole::ADMIN => redirect()->route('dashboard.admin'),
            UserRole::OPERATOR => redirect()->route('dashboard.operator'),
            default => abort(403, 'Unauthorized action.'),
        };
    }
}
