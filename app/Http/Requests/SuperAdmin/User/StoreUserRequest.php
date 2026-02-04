<?php

namespace App\Http\Requests\SuperAdmin\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
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
