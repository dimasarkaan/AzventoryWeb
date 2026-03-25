<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

use App\Traits\ActivityLogger;

class PasswordController extends Controller
{
    use ActivityLogger;
    /**
     * Memperbarui password pengguna.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required', 
                Password::min(8)->max(16)->letters()->numbers(), 
                'confirmed',
                function ($attribute, $value, $fail) {
                    if (Hash::check($value, auth()->user()->password)) {
                        $fail('Password baru tidak boleh sama dengan password saat ini.');
                    }
                },
            ],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->logActivity('Ubah Password', 'User memperbarui kata sandi akun mereka.');

        return back()->with('status', 'password-updated');
    }
}
