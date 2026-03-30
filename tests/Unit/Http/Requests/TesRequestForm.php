<?php

namespace Tests\Unit\Http\Requests;

use App\Enums\UserRole;
use App\Http\Requests\Inventory\StoreSparepartRequest;
use App\Http\Requests\Inventory\UpdateSparepartRequest;
use App\Http\Requests\Users\StoreUserRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * White-box unit test untuk semua Form Request classes.
 * Memverifikasi rules validasi, pesan kustom, dan logika kondisional.
 */
class TesRequestForm extends TestCase
{
    use RefreshDatabase;

    // ════════════════════════════════════════════════════════════════
    //  StoreSparepartRequest
    // ════════════════════════════════════════════════════════════════

    #[Test]
    public function store_sparepart_request_lolos_dengan_data_valid_minimal()
    {
        $rules = (new StoreSparepartRequest)->rules();

        $data = [
            'name' => 'Baut M5',
            'part_number' => 'BM5-001',
            'brand' => 'Toyota',
            'category' => 'Baut',
            'location' => 'Rak A',
            'age' => 'Baru',
            'condition' => 'Baik',
            'type' => 'asset',
            'stock' => 10,
            'status' => 'aktif',
        ];

        $validator = Validator::make($data, $rules);
        $this->assertFalse($validator->fails(), implode(', ', $validator->errors()->all()));
    }

    #[Test]
    public function store_sparepart_harga_wajib_diisi_jika_tipe_sale()
    {
        $rules = (new StoreSparepartRequest)->rules();

        $data = [
            'name' => 'Oli Mesin',
            'part_number' => 'OM-001',
            'brand' => 'Shell',
            'category' => 'Oli',
            'location' => 'Rak B',
            'age' => 'Baru',
            'condition' => 'Baik',
            'type' => 'sale',
            'price' => null, // wajib jika sale!
            'stock' => 5,
            'status' => 'aktif',
        ];

        $validator = Validator::make($data, $rules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('price', $validator->errors()->toArray());
    }

    #[Test]
    public function store_sparepart_harga_tidak_wajib_jika_tipe_asset()
    {
        $rules = (new StoreSparepartRequest)->rules();

        $data = [
            'name' => 'Laptop',
            'part_number' => 'LAP-001',
            'brand' => 'Dell',
            'category' => 'Elektronik',
            'location' => 'Rak C',
            'age' => 'Baru',
            'condition' => 'Baik',
            'type' => 'asset',
            'price' => null, // opsional jika asset
            'stock' => 1,
            'status' => 'aktif',
        ];

        $validator = Validator::make($data, $rules);
        $this->assertFalse($validator->fails(), 'Price seharusnya opsional untuk tipe asset');
    }

    #[Test]
    public function store_sparepart_age_harus_salah_satu_dari_nilai_yang_diizinkan()
    {
        $rules = (new StoreSparepartRequest)->rules();

        $data = [
            'name' => 'Test',
            'part_number' => 'TEST-001',
            'brand' => 'Brand',
            'category' => 'Cat',
            'location' => 'Loc',
            'age' => 'Invalid Age Value',
            'condition' => 'Baik',
            'type' => 'asset',
            'stock' => 5,
            'status' => 'aktif',
        ];

        $validator = Validator::make($data, $rules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('age', $validator->errors()->toArray());
    }

    #[Test]
    public function store_sparepart_stok_tidak_boleh_negatif()
    {
        $rules = (new StoreSparepartRequest)->rules();

        $data = [
            'name' => 'Test',
            'part_number' => 'TEST-001',
            'brand' => 'Brand',
            'category' => 'Cat',
            'location' => 'Loc',
            'age' => 'Baru',
            'condition' => 'Baik',
            'type' => 'asset',
            'stock' => -1,
            'status' => 'aktif',
        ];

        $validator = Validator::make($data, $rules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('stock', $validator->errors()->toArray());
    }

    // ════════════════════════════════════════════════════════════════
    //  UpdateSparepartRequest — logika kondisional harga untuk Admin
    // ════════════════════════════════════════════════════════════════

    #[Test]
    public function update_sparepart_request_admin_tidak_memiliki_rule_price()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $request = new UpdateSparepartRequest;
        $request->setUserResolver(fn () => $admin);

        $rules = $request->rules();
        $this->assertArrayNotHasKey('price', $rules);
    }

    #[Test]
    public function update_sparepart_request_superadmin_memiliki_rule_price()
    {
        $superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $request = new UpdateSparepartRequest;
        $request->setUserResolver(fn () => $superadmin);

        $rules = $request->rules();
        $this->assertArrayHasKey('price', $rules);
    }

    // ════════════════════════════════════════════════════════════════
    //  StoreUserRequest
    // ════════════════════════════════════════════════════════════════

    #[Test]
    public function store_user_request_gagal_jika_email_sudah_dipakai()
    {
        User::factory()->create(['email' => 'existing@test.com']);
        $rules = (new StoreUserRequest)->rules();

        $data = [
            'name' => 'Existing User',
            'email' => 'existing@test.com',
            'role' => 'admin',
            'jabatan' => 'Manager',
            'status' => 'aktif',
        ];

        $validator = Validator::make($data, $rules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    #[Test]
    public function store_user_request_gagal_jika_role_tidak_valid()
    {
        $rules = (new StoreUserRequest)->rules();

        $data = [
            'name' => 'New User',
            'email' => 'new@test.com',
            'role' => 'manager', // role tidak valid
            'jabatan' => 'Manager',
            'status' => 'aktif',
        ];

        $validator = Validator::make($data, $rules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('role', $validator->errors()->toArray());
    }

    #[Test]
    public function store_user_request_lolos_dengan_data_valid()
    {
        $rules = (new StoreUserRequest)->rules();

        $data = [
            'name' => 'New User',
            'email' => 'newuser@test.com',
            'role' => 'operator',
            'jabatan' => 'Teknisi',
            'status' => 'aktif',
        ];

        $validator = Validator::make($data, $rules);
        $this->assertFalse($validator->fails(), implode(', ', $validator->errors()->all()));
    }
}
