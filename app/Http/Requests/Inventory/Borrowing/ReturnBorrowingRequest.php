<?php

namespace App\Http\Requests\Inventory\Borrowing;

use Illuminate\Foundation\Http\FormRequest;

class ReturnBorrowingRequest extends FormRequest
{
    /**
     * Tentukan apakah user diizinkan untuk membuat request ini.
     */
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk request ini.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $borrowing = $this->route('borrowing');
        $remaining = $borrowing ? $borrowing->remaining_quantity : 999999;

        return [
            'return_quantity' => 'required|integer|min:1|max:' . $remaining,
            'return_condition' => 'required|in:good,bad,lost',
            'return_notes' => 'nullable|string',
            'return_photos' => [
                'required_if:return_condition,good,bad',
                'array',
                'min:1',
                'max:5'
            ],
            'return_photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB
        ];
    }

    public function messages()
    {
        return [
            'return_quantity.required' => 'Jumlah wajib diisi.',
            'return_quantity.integer' => 'Jumlah harus berupa angka.',
            'return_quantity.min' => 'Jumlah minimal 1.',
            'return_quantity.max' => 'Jumlah tidak boleh melebihi sisa pinjaman (:max).',
            'return_condition.required' => 'Kondisi barang wajib dipilih.',
            'return_condition.in' => 'Kondisi tidak valid.',
            'return_photos.array' => 'Format foto tidak valid.',
            'return_photos.min' => 'Minimal unggah :min foto.',
            'return_photos.max' => 'Maksimal unggah :max foto.',
            'return_photos.*.image' => 'File harus berupa gambar.',
            'return_photos.*.mimes' => 'Format file harus jpeg, png, jpg, gif, atau webp.',
            'return_photos.*.max' => 'Ukuran file maksimal 5MB.',
        ];
    }
}
