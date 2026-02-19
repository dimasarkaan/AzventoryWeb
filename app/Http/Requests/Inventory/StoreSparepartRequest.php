<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreSparepartRequest extends FormRequest
{
    /**
     * Tentukan apakah user diizinkan untuk membuat request ini.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Sparepart::class);
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk request ini.
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
            'age' => 'required|in:Baru,Pernah Dipakai (Bekas)',
            'condition' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
            'type' => 'required|in:sale,asset',
            'price' => 'required_if:type,sale|nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'status' => 'required|in:aktif,nonaktif',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // Max 5MB untuk foto HP
            'existing_image' => 'nullable|string',
        ];
    }

    /**
     * Dapatkan atribut kustom untuk pesan error validator.
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
     * Dapatkan pesan validasi kustom.
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
