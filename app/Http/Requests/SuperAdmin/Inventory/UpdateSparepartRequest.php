<?php

namespace App\Http\Requests\SuperAdmin\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSparepartRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'part_number' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'condition' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
            'type' => 'required|in:sale,asset',
            'price' => 'required_if:type,sale|nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'status' => 'required|in:aktif,nonaktif',
            'image' => 'nullable|image|max:10240',
            'age' => 'required|in:Baru,Pernah Dipakai (Bekas)',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'age' => 'Status Pemakaian',
            'price' => 'Harga Satuan',
            'part_number' => 'Part Number',
            'name' => 'Nama Barang',
            'brand' => 'Merk',
            'category' => 'Kategori',
            'location' => 'Lokasi Penyimpanan',
            'condition' => 'Kondisi Barang',
            'color' => 'Warna',
            'type' => 'Tipe Barang',
            'stock' => 'Stok Saat Ini',
            'minimum_stock' => 'Minimum Stok',
            'unit' => 'Satuan',
            'status' => 'Status',
            'image' => 'Gambar',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'age.required' => 'Kolom Status Pemakaian wajib diisi.',
            'age.in' => 'Status Pemakaian harus berisi "Baru" atau "Pernah Dipakai (Bekas)".',
            'price.required_if' => 'Kolom Harga Satuan wajib diisi.',
        ];
    }
}
