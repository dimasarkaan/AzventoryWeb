<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\UserRole;

class DashboardRedirectController extends Controller
{
    /**
     * Menangani request yang masuk.
     * 
     * Mengarahkan user ke dashboard yang sesuai berdasarkan role.
     */
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
