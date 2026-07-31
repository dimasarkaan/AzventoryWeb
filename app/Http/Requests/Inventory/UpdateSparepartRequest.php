<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSparepartRequest extends FormRequest
{
    // Mengecek apakah jabatan pengguna diizinkan untuk mengubah detail barang ini
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('inventory'));
    }

    // Tahap Persiapan: Memproses Kategori, Merk, atau Lokasi baru yang diketik manual oleh pengguna sebelum masuk ke tahap validasi
    protected function prepareForValidation(): void
    {
        $categoryName = trim((string) $this->input('category_name'));
        if ($categoryName !== '') {
            $validator = \Illuminate\Support\Facades\Validator::make(
                ['category_name' => $categoryName],
                ['category_name' => ['required', 'string', 'min:2', 'max:100', 'regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9][a-zA-Z0-9\s\.\,\&\-\(\)\/\'"]*$/']],
                [
                    'category_name.regex' => 'Format Nama Kategori tidak valid. Harus diawali huruf/angka dan mengandung huruf.',
                    'category_name.min' => 'Nama kategori minimal 2 karakter.',
                    'category_name.max' => 'Nama kategori maksimal 100 karakter.',
                ]
            );
            if ($validator->fails()) throw new \Illuminate\Validation\ValidationException($validator);

            $category = \App\Models\Category::whereRaw('LOWER(name) = ?', [strtolower($categoryName)])->first();
            if (!$category) {
                $category = \App\Models\Category::create([
                    'name' => $categoryName,
                    'is_active' => true
                ]);
            }
            $this->merge(['category_id' => $category->id]);
        }

        $brandName = trim((string) $this->input('brand_name'));
        if ($brandName !== '') {
            $validator = \Illuminate\Support\Facades\Validator::make(
                ['brand_name' => $brandName],
                ['brand_name' => ['required', 'string', 'min:2', 'max:100', 'regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9][a-zA-Z0-9\s\.\,\&\-\(\)\/\'"]*$/']],
                [
                    'brand_name.regex' => 'Format Nama Merk tidak valid. Harus diawali huruf/angka dan mengandung huruf.',
                    'brand_name.min' => 'Nama merk minimal 2 karakter.',
                    'brand_name.max' => 'Nama merk maksimal 100 karakter.',
                ]
            );
            if ($validator->fails()) throw new \Illuminate\Validation\ValidationException($validator);

            $brand = \App\Models\Brand::whereRaw('LOWER(name) = ?', [strtolower($brandName)])->first();
            if (!$brand) {
                $brand = \App\Models\Brand::create([
                    'name' => $brandName,
                    'is_active' => true
                ]);
            }
            $this->merge(['brand_id' => $brand->id]);
        }

        $locationName = trim((string) $this->input('location_name'));
        if ($locationName !== '') {
            $validator = \Illuminate\Support\Facades\Validator::make(
                ['location_name' => $locationName],
                ['location_name' => ['required', 'string', 'min:2', 'max:100', 'regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9][a-zA-Z0-9\s\.\,\&\-\(\)\/\'"]*$/']],
                [
                    'location_name.regex' => 'Format Nama Lokasi tidak valid. Harus diawali huruf/angka dan mengandung huruf.',
                    'location_name.min' => 'Nama lokasi minimal 2 karakter.',
                    'location_name.max' => 'Nama lokasi maksimal 100 karakter.',
                ]
            );
            if ($validator->fails()) throw new \Illuminate\Validation\ValidationException($validator);

            $location = \App\Models\Location::whereRaw('LOWER(name) = ?', [strtolower($locationName)])->first();
            if (!$location) {
                if ($this->user()->role === \App\Enums\UserRole::SUPERADMIN) {
                    $location = \App\Models\Location::create([
                        'name' => $locationName,
                        'is_active' => true
                    ]);
                    $this->merge(['location_id' => $location->id]);
                }
            } else {
                $this->merge(['location_id' => $location->id]);
            }
        }
    }

    // Aturan ketat untuk setiap kolom di form edit barang
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'min:3', 'max:255', 'regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9][a-zA-Z0-9\s\.\,\&\-\(\)\/\'"]*$/'],
            'part_number' => ['required', 'string', 'min:3', 'max:255', 'regex:/^[a-zA-Z0-9][a-zA-Z0-9\-\_\/]*$/'],
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'condition' => ['required', 'string', 'min:3', 'max:255', 'regex:/^(?=.*[a-zA-Z])[a-zA-Z0-9][a-zA-Z0-9\s\.\,\&\-\(\)\/\'"]*$/'],
            'color' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z][a-zA-Z\s\-]*$/'],
            'type' => 'required|in:sale,asset',
            'price' => 'required_if:type,sale|nullable|numeric|min:0|max:9999999999999',
            'stock' => 'required|integer|min:0|max:2147483647',
            'minimum_stock' => 'nullable|integer|min:0|max:2147483647',
            'unit' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z0-9\s]*$/'],
            'status' => 'required|in:aktif,nonaktif',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:17408', // Max 17MB untuk foto HP
            'existing_image' => 'nullable|string',
            'age' => 'required|in:Baru,Pernah Dipakai (Bekas)',
        ];

        // Admin cannot update price
        if ($this->user()->role === \App\Enums\UserRole::ADMIN) {
            unset($rules['price']);
        }

        return $rules;
    }

    // Mengganti nama variabel bahasa Inggris menjadi bahasa Indonesia yang sopan untuk ditampilkan di layar (Alias)
    public function attributes(): array
    {
        return [
            'age' => 'Status Pemakaian',
            'price' => 'Harga Satuan',
            'part_number' => 'Part Number',
            'name' => 'Nama Barang',
            'brand_id' => 'Merk',
            'category_id' => 'Kategori',
            'location_id' => 'Lokasi Penyimpanan',
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

    // Kumpulan pesan error berbahasa Indonesia jika aturan di atas dilanggar
    public function messages(): array
    {
        return [
            'name.required' => 'Nama Barang wajib diisi.',
            'name.min' => 'Nama Barang minimal 3 karakter.',
            'part_number.required' => 'Part Number wajib diisi.',
            'part_number.min' => 'Part Number minimal 3 karakter.',
            'brand_id.required' => 'Merk wajib dipilih.',
            'brand_id.exists' => 'Merk yang dipilih tidak valid atau tidak ditemukan.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid atau tidak ditemukan.',
            'location_id.required' => 'Lokasi Penyimpanan wajib dipilih.',
            'location_id.exists' => 'Lokasi Penyimpanan yang dipilih tidak valid atau tidak ditemukan.',
            'age.required' => 'Status Pemakaian wajib dipilih.',
            'age.in' => 'Status Pemakaian harus berisi "Baru" atau "Pernah Dipakai (Bekas)".',
            'condition.required' => 'Kondisi Barang wajib diisi.',
            'condition.min' => 'Kondisi Barang minimal 3 karakter.',
            'type.required' => 'Tipe Barang wajib dipilih.',
            'price.required_if' => 'Harga Satuan wajib diisi untuk barang bertipe Sale.',
            'stock.required' => 'Stok Saat Ini wajib diisi.',
            'stock.min' => 'Stok tidak boleh kurang dari 0.',
            'status.required' => 'Status wajib dipilih.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format file Gambar harus jpeg, png, jpg, atau webp.',
            'image.max' => 'Ukuran file Gambar maksimal 17MB.',
            
            // Regex error messages
            'name.regex' => 'Nama barang harus mengandung huruf, diawali huruf/angka, serta hanya berisi huruf/angka/spasi/simbol (.,&-()/\'").',
            'part_number.regex' => 'Part Number hanya boleh berisi huruf, angka, strip (-), garis miring (/), dan underscore (_).',
            'condition.regex' => 'Kondisi Barang harus mengandung huruf, diawali huruf/angka, serta hanya berisi huruf/angka/spasi/simbol (.,&-()/\'").',
            'color.regex' => 'Warna hanya boleh berisi huruf, spasi, dan strip (-).',
            'unit.regex' => 'Satuan hanya boleh berisi huruf, angka, dan spasi.',
        ];
    }
}
