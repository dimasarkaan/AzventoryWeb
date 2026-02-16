<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\UserRole;

class DashboardRedirectController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = auth()->user();

        return match ($user->role) {
            UserRole::SUPERADMIN => redirect()->route('dashboard.superadmin'),
            UserRole::ADMIN => redirect()->route('admin.dashboard'),
            UserRole::OPERATOR => redirect()->route('operator.dashboard'),
            default => abort(403, 'Unauthorized action.'),
        };
    }
}
