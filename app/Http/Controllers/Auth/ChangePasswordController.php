<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePasswordController extends Controller
{
    public function create()
    {
        return view('auth.change-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->max(16)->letters()->numbers(),
            ],
        ]);

        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
        ]);

        // Redirect based on role after successful password change
        $redirectPath = match ($user->role) {
            'superadmin' => '/superadmin/dashboard',
            'admin' => '/admin/dashboard',
            'operator' => '/operator/dashboard',
            default => '/dashboard',
        };

        return redirect($redirectPath)->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
