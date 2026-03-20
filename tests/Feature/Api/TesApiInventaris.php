<?php

namespace Tests\Feature\Api;

use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test komprehensif untuk seluruh endpoint REST API Inventory.
 *
 * Endpoint yang diuji:
 *   GET    /api/v1/inventory           → index
 *   POST   /api/v1/inventory           → store
 *   GET    /api/v1/inventory/{id}      → show
 *   PUT    /api/v1/inventory/{id}      → update
 *   DELETE /api/v1/inventory/{id}      → destroy
 *   PUT    /api/v1/inventory/{id}/adjust-stock → adjustStock
 */
class TesApiInventaris extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role = 'superadmin'): User
    {
        return User::factory()->create([
            'role' => $role,
            'password_changed_at' => now(),
        ]);
    }

    private function makeSparepart(array $override = []): Sparepart
    {
        return Sparepart::factory()->create(array_merge([
            'stock' => 10,
            'minimum_stock' => 2,
            'status' => 'aktif',
        ], $override));
    }

    private function validPayload(array $override = []): array
    {
        return array_merge([
            'part_number' => 'PN-API-'.uniqid(),
            'name' => 'Test Barang API',
            'brand' => 'Brand Test',
            'location' => 'Rak A1',
            'category' => 'Elektronik',
            'condition' => 'Baru',
            'age' => 'Baru',
            'type' => 'sale',
            'stock' => 5,
            'minimum_stock' => 1,
            'unit' => 'Pcs',
            'status' => 'aktif',
        ], $override);
    }

    // =========================================================================
    // SEKSI 1 — AUTENTIKASI
    // =========================================================================

    #[Test]
    public function semua_endpoint_ditolak_tanpa_token()
    {
        $sparepart = $this->makeSparepart();

        $this->getJson('/api/v1/inventory')->assertStatus(401);
        $this->postJson('/api/v1/inventory', [])->assertStatus(401);
        $this->getJson("/api/v1/inventory/{$sparepart->id}")->assertStatus(401);
        $this->putJson("/api/v1/inventory/{$sparepart->id}", [])->assertStatus(401);
        $this->deleteJson("/api/v1/inventory/{$sparepart->id}")->assertStatus(401);
        $this->putJson("/api/v1/inventory/{$sparepart->id}/adjust-stock", [])->assertStatus(401);
    }

    // =========================================================================
    // SEKSI 2 — GET /api/v1/inventory (index)
    // =========================================================================

    #[Test]
    public function index_mengembalikan_daftar_inventory_dengan_paginasi()
    {
        Sanctum::actingAs($this->makeUser());
        Sparepart::factory()->count(5)->create();

        $this->getJson('/api/v1/inventory')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'part_number', 'name', 'brand',
                        'category', 'type', 'condition', 'status',
                        'stock' => ['current', 'minimum', 'unit', 'is_low'],
                        'location',
                    ],
                ],
                'meta' => ['api_version', 'service'],
            ]);
    }

    #[Test]
    public function index_dengan_filter_search_mengembalikan_data_yang_sesuai()
    {
        Sanctum::actingAs($this->makeUser());
        $this->makeSparepart(['name' => 'Laptop ASUS X123']);
        $this->makeSparepart(['name' => 'Printer Canon']);

        $response = $this->getJson('/api/v1/inventory?search=Laptop');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertStringContainsString('Laptop', $data[0]['name']);
    }

    #[Test]
    public function index_dengan_filter_category_mengembalikan_data_sesuai()
    {
        Sanctum::actingAs($this->makeUser());
        $this->makeSparepart(['category' => 'Printer']);
        $this->makeSparepart(['category' => 'Laptop']);

        $response = $this->getJson('/api/v1/inventory?category=Printer');

        $response->assertStatus(200);
        foreach ($response->json('data') as $item) {
            $this->assertEquals('Printer', $item['category']);
        }
    }

    #[Test]
    public function index_dengan_filter_per_page_mengembalikan_jumlah_yang_diminta()
    {
        Sanctum::actingAs($this->makeUser());
        Sparepart::factory()->count(10)->create();

        $response = $this->getJson('/api/v1/inventory?per_page=3');

        $response->assertStatus(200);
        $this->assertLessThanOrEqual(3, count($response->json('data')));
    }

    #[Test]
    public function index_mengembalikan_data_kosong_jika_tidak_ada_inventory()
    {
        Sanctum::actingAs($this->makeUser());

        $response = $this->getJson('/api/v1/inventory');
        $response->assertStatus(200);
        $this->assertEmpty($response->json('data'));
    }

    // =========================================================================
    // SEKSI 3 — GET /api/v1/inventory/{id} (show)
    // =========================================================================

    #[Test]
    public function show_mengembalikan_detail_barang_yang_benar()
    {
        Sanctum::actingAs($this->makeUser());
        $sparepart = $this->makeSparepart(['name' => 'RAM DDR4 8GB', 'stock' => 20]);

        $this->getJson("/api/v1/inventory/{$sparepart->id}")
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $sparepart->id,
                    'name' => 'RAM DDR4 8GB',
                    'stock' => ['current' => 20],
                ],
            ]);
    }

    #[Test]
    public function show_mengembalikan_404_jika_barang_tidak_ditemukan()
    {
        Sanctum::actingAs($this->makeUser());

        $this->getJson('/api/v1/inventory/99999')
            ->assertStatus(404)
            ->assertJson(['status' => 'error']);
    }

    #[Test]
    public function show_mengembalikan_struktur_json_yang_lengkap()
    {
        Sanctum::actingAs($this->makeUser());
        $sparepart = $this->makeSparepart(['price' => 150000]);

        $this->getJson("/api/v1/inventory/{$sparepart->id}")
            ->assertStatus(200)
            ->assertJsonStructure([
                'status', 'message',
                'data' => [
                    'id', 'part_number', 'name', 'brand', 'category',
                    'type', 'condition', 'status', 'location',
                    'stock' => ['current', 'minimum', 'unit', 'is_low'],
                    'price', 'image_url', 'created_at', 'updated_at',
                ],
            ]);
    }

    // =========================================================================
    // SEKSI 4 — POST /api/v1/inventory (store)
    // =========================================================================

    #[Test]
    public function store_berhasil_membuat_barang_baru()
    {
        Sanctum::actingAs($this->makeUser());
        $payload = $this->validPayload(['name' => 'SSD Samsung 512GB']);

        $this->postJson('/api/v1/inventory', $payload)
            ->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'data' => ['name' => 'SSD Samsung 512GB'],
            ]);

        $this->assertDatabaseHas('spareparts', ['name' => 'SSD Samsung 512GB']);
    }

    #[Test]
    public function store_berhasil_dengan_price_opsional()
    {
        Sanctum::actingAs($this->makeUser());
        $payload = $this->validPayload();
        unset($payload['price']);

        $this->postJson('/api/v1/inventory', $payload)->assertStatus(201);
    }

    #[Test]
    public function store_gagal_jika_part_number_sudah_ada()
    {
        Sanctum::actingAs($this->makeUser());
        $this->makeSparepart(['part_number' => 'PN-DUPLICATE']);

        $this->postJson('/api/v1/inventory', $this->validPayload(['part_number' => 'PN-DUPLICATE']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['part_number']);
    }

    #[Test]
    public function store_gagal_jika_field_wajib_tidak_diisi()
    {
        Sanctum::actingAs($this->makeUser());

        $this->postJson('/api/v1/inventory', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['part_number', 'name', 'brand', 'location', 'type', 'stock', 'category', 'condition', 'status']);
    }

    #[Test]
    public function store_gagal_jika_type_tidak_valid()
    {
        Sanctum::actingAs($this->makeUser());

        $this->postJson('/api/v1/inventory', $this->validPayload(['type' => 'invalid']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    #[Test]
    public function store_gagal_jika_stock_negatif()
    {
        Sanctum::actingAs($this->makeUser());

        $this->postJson('/api/v1/inventory', $this->validPayload(['stock' => -5]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['stock']);
    }

    #[Test]
    public function store_gagal_jika_status_tidak_valid()
    {
        Sanctum::actingAs($this->makeUser());

        $this->postJson('/api/v1/inventory', $this->validPayload(['status' => 'unknown']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    // =========================================================================
    // SEKSI 5 — PUT /api/v1/inventory/{id} (update)
    // =========================================================================

    #[Test]
    public function update_berhasil_mengubah_nama_barang()
    {
        Sanctum::actingAs($this->makeUser());
        $sparepart = $this->makeSparepart(['name' => 'Nama Lama']);

        $this->putJson("/api/v1/inventory/{$sparepart->id}", ['name' => 'Nama Baru'])
            ->assertStatus(200)
            ->assertJson(['status' => 'success', 'data' => ['name' => 'Nama Baru']]);

        $this->assertDatabaseHas('spareparts', ['id' => $sparepart->id, 'name' => 'Nama Baru']);
    }

    #[Test]
    public function update_berhasil_partial_update_hanya_satu_field()
    {
        Sanctum::actingAs($this->makeUser());
        $sparepart = $this->makeSparepart(['location' => 'Rak Lama']);

        $this->putJson("/api/v1/inventory/{$sparepart->id}", ['location' => 'Rak Baru'])
            ->assertStatus(200);

        $this->assertDatabaseHas('spareparts', ['id' => $sparepart->id, 'location' => 'Rak Baru']);
    }

    #[Test]
    public function update_mengembalikan_404_jika_barang_tidak_ditemukan()
    {
        Sanctum::actingAs($this->makeUser());

        $this->putJson('/api/v1/inventory/99999', ['name' => 'Test'])
            ->assertStatus(404)
            ->assertJson(['status' => 'error']);
    }

    #[Test]
    public function update_gagal_jika_part_number_sudah_dipakai_barang_lain()
    {
        Sanctum::actingAs($this->makeUser());
        $sparepartA = $this->makeSparepart(['part_number' => 'PN-AAA']);
        $sparepartB = $this->makeSparepart(['part_number' => 'PN-BBB']);

        $this->putJson("/api/v1/inventory/{$sparepartB->id}", ['part_number' => 'PN-AAA'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['part_number']);
    }

    #[Test]
    public function update_berhasil_dengan_part_number_milik_sendiri()
    {
        Sanctum::actingAs($this->makeUser());
        $sparepart = $this->makeSparepart(['part_number' => 'PN-SELF']);

        $this->putJson("/api/v1/inventory/{$sparepart->id}", [
            'part_number' => 'PN-SELF',
            'name' => 'Nama Diperbarui',
        ])->assertStatus(200);
    }

    // =========================================================================
    // SEKSI 6 — DELETE /api/v1/inventory/{id} (destroy)
    // =========================================================================

    #[Test]
    public function destroy_berhasil_soft_delete_barang()
    {
        Sanctum::actingAs($this->makeUser());
        $sparepart = $this->makeSparepart();

        $this->deleteJson("/api/v1/inventory/{$sparepart->id}")
            ->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertSoftDeleted('spareparts', ['id' => $sparepart->id]);
    }

    #[Test]
    public function destroy_mengembalikan_404_jika_barang_tidak_ditemukan()
    {
        Sanctum::actingAs($this->makeUser());

        $this->deleteJson('/api/v1/inventory/99999')
            ->assertStatus(404)
            ->assertJson(['status' => 'error']);
    }

    #[Test]
    public function barang_yang_sudah_dihapus_tidak_muncul_di_index()
    {
        Sanctum::actingAs($this->makeUser());
        $sparepart = $this->makeSparepart(['name' => 'Barang Akan Dihapus']);

        $this->deleteJson("/api/v1/inventory/{$sparepart->id}")->assertStatus(200);

        $response = $this->getJson('/api/v1/inventory?search=Barang Akan Dihapus');
        $this->assertEmpty($response->json('data'));
    }

    // =========================================================================
    // SEKSI 7 — PUT /api/v1/inventory/{id}/adjust-stock
    // =========================================================================

    #[Test]
    public function adjust_stock_decrement_berhasil_dan_log_tercatat()
    {
        $user = $this->makeUser();
        Sanctum::actingAs($user);
        $sparepart = $this->makeSparepart(['stock' => 10]);

        $this->putJson("/api/v1/inventory/{$sparepart->id}/adjust-stock", [
            'type' => 'decrement',
            'quantity' => 3,
            'description' => 'Teknisi ambil part',
        ])->assertStatus(200)
            ->assertJson(['status' => 'success', 'data' => ['current_stock' => 7]]);

        $this->assertDatabaseHas('spareparts', ['id' => $sparepart->id, 'stock' => 7]);
        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $sparepart->id,
            'user_id' => $user->id,
            'type' => 'keluar',
            'quantity' => 3,
            'status' => 'approved',
        ]);
    }

    #[Test]
    public function adjust_stock_increment_berhasil_dan_log_tercatat()
    {
        $user = $this->makeUser();
        Sanctum::actingAs($user);
        $sparepart = $this->makeSparepart(['stock' => 10]);

        $this->putJson("/api/v1/inventory/{$sparepart->id}/adjust-stock", [
            'type' => 'increment',
            'quantity' => 5,
            'description' => 'Restok dari supplier',
        ])->assertStatus(200)
            ->assertJson(['status' => 'success', 'data' => ['current_stock' => 15]]);

        $this->assertDatabaseHas('spareparts', ['id' => $sparepart->id, 'stock' => 15]);
        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $sparepart->id,
            'user_id' => $user->id,
            'type' => 'masuk',
            'quantity' => 5,
            'status' => 'approved',
        ]);
    }

    #[Test]
    public function adjust_stock_gagal_jika_stok_tidak_mencukupi()
    {
        Sanctum::actingAs($this->makeUser());
        $sparepart = $this->makeSparepart(['stock' => 5]);

        $this->putJson("/api/v1/inventory/{$sparepart->id}/adjust-stock", [
            'type' => 'decrement',
            'quantity' => 999,
        ])->assertStatus(400)->assertJson(['status' => 'error']);

        $this->assertDatabaseHas('spareparts', ['id' => $sparepart->id, 'stock' => 5]);
    }

    #[Test]
    public function adjust_stock_gagal_jika_item_tidak_ditemukan()
    {
        Sanctum::actingAs($this->makeUser());

        $this->putJson('/api/v1/inventory/99999/adjust-stock', [
            'type' => 'decrement',
            'quantity' => 1,
        ])->assertStatus(404)->assertJson(['status' => 'error']);
    }

    #[Test]
    public function adjust_stock_gagal_jika_type_tidak_valid()
    {
        Sanctum::actingAs($this->makeUser());
        $sparepart = $this->makeSparepart();

        $this->putJson("/api/v1/inventory/{$sparepart->id}/adjust-stock", [
            'type' => 'invalid_type',
            'quantity' => 1,
        ])->assertStatus(422)->assertJsonValidationErrors(['type']);
    }

    #[Test]
    public function adjust_stock_gagal_jika_quantity_nol_atau_negatif()
    {
        Sanctum::actingAs($this->makeUser());
        $sparepart = $this->makeSparepart();

        $this->putJson("/api/v1/inventory/{$sparepart->id}/adjust-stock", [
            'type' => 'decrement', 'quantity' => 0,
        ])->assertStatus(422)->assertJsonValidationErrors(['quantity']);

        $this->putJson("/api/v1/inventory/{$sparepart->id}/adjust-stock", [
            'type' => 'decrement', 'quantity' => -5,
        ])->assertStatus(422)->assertJsonValidationErrors(['quantity']);
    }

    #[Test]
    public function adjust_stock_tanpa_token_ditolak_401()
    {
        $sparepart = $this->makeSparepart();

        $this->putJson("/api/v1/inventory/{$sparepart->id}/adjust-stock", [
            'type' => 'decrement', 'quantity' => 1,
        ])->assertStatus(401);
    }

    #[Test]
    public function adjust_stock_tanpa_description_tetap_berhasil()
    {
        Sanctum::actingAs($this->makeUser());
        $sparepart = $this->makeSparepart(['stock' => 10]);

        $this->putJson("/api/v1/inventory/{$sparepart->id}/adjust-stock", [
            'type' => 'increment', 'quantity' => 2,
        ])->assertStatus(200);
    }

    #[Test]
    public function adjust_stock_mengembalikan_is_low_stock_true_jika_stok_kritis()
    {
        Sanctum::actingAs($this->makeUser());
        $sparepart = $this->makeSparepart(['stock' => 10, 'minimum_stock' => 5]);

        $this->putJson("/api/v1/inventory/{$sparepart->id}/adjust-stock", [
            'type' => 'decrement', 'quantity' => 8,
        ])->assertStatus(200)
            ->assertJson(['data' => ['current_stock' => 2, 'is_low_stock' => true]]);
    }

    #[Test]
    public function adjust_stock_mengembalikan_is_low_stock_false_jika_stok_aman()
    {
        Sanctum::actingAs($this->makeUser());
        $sparepart = $this->makeSparepart(['stock' => 10, 'minimum_stock' => 2]);

        $this->putJson("/api/v1/inventory/{$sparepart->id}/adjust-stock", [
            'type' => 'increment', 'quantity' => 5,
        ])->assertStatus(200)
            ->assertJson(['data' => ['current_stock' => 15, 'is_low_stock' => false]]);
    }

    // =========================================================================
    // SEKSI 8 — ROLE ACCESS CONTROL
    // =========================================================================

    #[Test]
    public function admin_dapat_mengakses_semua_endpoint_api()
    {
        Sanctum::actingAs($this->makeUser('admin'));
        $sparepart = $this->makeSparepart();

        $this->getJson('/api/v1/inventory')->assertStatus(200);
        $this->getJson("/api/v1/inventory/{$sparepart->id}")->assertStatus(200);
    }

    #[Test]
    public function operator_dapat_mengakses_endpoint_read()
    {
        Sanctum::actingAs($this->makeUser('operator'));
        $sparepart = $this->makeSparepart();

        $this->getJson('/api/v1/inventory')->assertStatus(200);
        $this->getJson("/api/v1/inventory/{$sparepart->id}")->assertStatus(200);
    }
}

