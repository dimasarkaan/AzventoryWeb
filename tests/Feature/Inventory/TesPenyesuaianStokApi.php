<?php

namespace Tests\Feature\Inventory;

use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesPenyesuaianStokApi extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Sparepart $sparepart;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'superadmin',
            'password_changed_at' => now(),
        ]);

        $this->sparepart = Sparepart::factory()->create([
            'stock' => 10,
            'minimum_stock' => 2,
            'status' => 'aktif',
        ]);
    }

    #[Test]
    public function api_adjust_stock_decrement_berhasil_dan_user_id_tidak_null()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->putJson("/api/v1/inventory/{$this->sparepart->id}/adjust-stock", [
            'type' => 'decrement',
            'quantity' => 3,
            'description' => 'Teknisi Mengganti Part',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => ['current_stock' => 7],
            ]);

        $this->assertDatabaseHas('spareparts', [
            'id' => $this->sparepart->id,
            'stock' => 7,
        ]);

        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $this->sparepart->id,
            'user_id' => $this->admin->id,
            'type' => 'keluar',
            'quantity' => 3,
            'status' => 'approved',
        ]);
    }

    #[Test]
    public function api_adjust_stock_increment_berhasil()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->putJson("/api/v1/inventory/{$this->sparepart->id}/adjust-stock", [
            'type' => 'increment',
            'quantity' => 5,
            'description' => 'Restok dari supplier',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => ['current_stock' => 15],
            ]);

        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $this->sparepart->id,
            'user_id' => $this->admin->id,
            'type' => 'masuk',
            'quantity' => 5,
            'status' => 'approved',
        ]);
    }

    #[Test]
    public function api_adjust_stock_gagal_jika_stok_tidak_cukup()
    {
        Sanctum::actingAs($this->admin);

        $this->putJson("/api/v1/inventory/{$this->sparepart->id}/adjust-stock", [
            'type' => 'decrement',
            'quantity' => 999,
        ])->assertStatus(400)->assertJson(['status' => 'error']);

        $this->assertDatabaseHas('spareparts', [
            'id' => $this->sparepart->id,
            'stock' => 10,
        ]);
    }

    #[Test]
    public function api_adjust_stock_ditolak_jika_tidak_ada_token()
    {
        $this->putJson("/api/v1/inventory/{$this->sparepart->id}/adjust-stock", [
            'type' => 'decrement',
            'quantity' => 1,
        ])->assertStatus(401);
    }

    #[Test]
    public function api_adjust_stock_gagal_jika_item_tidak_ditemukan()
    {
        Sanctum::actingAs($this->admin);

        $this->putJson('/api/v1/inventory/99999/adjust-stock', [
            'type' => 'decrement',
            'quantity' => 1,
        ])->assertStatus(404)->assertJson(['status' => 'error']);
    }

    #[Test]
    public function api_adjust_stock_validasi_gagal_jika_type_salah()
    {
        Sanctum::actingAs($this->admin);

        $this->putJson("/api/v1/inventory/{$this->sparepart->id}/adjust-stock", [
            'type' => 'invalid_type',
            'quantity' => 1,
        ])->assertStatus(422);
    }
}

