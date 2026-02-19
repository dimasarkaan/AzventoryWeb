<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    // Tentukan izin akses (selalu true karena dilindungi route/middleware).
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi input.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:superadmin,admin,operator',
            'jabatan' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ];
    }
}
