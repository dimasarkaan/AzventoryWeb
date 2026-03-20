<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Regression + feature tests untuk API adjust-stock endpoint.
 *
 * BUG #001 — KRITIS (sudah diperbaiki):
 *   auth()->id() mengembalikan null saat menggunakan Sanctum bearer token
 *   karena auth() me-resolve guard 'web' bukan 'sanctum'.
 *   FIX: ganti dengan $request->user()->id.
 *
 * Test ini memastikan bug tersebut tidak muncul kembali.
 */
class TesPenyesuaianStokApi extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected Sparepart $sparepart;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create(['role' => UserRole::SUPERADMIN]);
        $this->sparepart = Sparepart::factory()->create(['stock' => 20, 'minimum_stock' => 5]);
    }

    // ── Helper ─────────────────────────────────────────────────────

    private function tokenHeader(): array
    {
        $token = $this->superadmin->createToken('test-token')->plainTextToken;

        return ['Authorization' => "Bearer {$token}"];
    }

    // ── REGRESSION: BUG #001 — user_id null di stock_logs ─────────

    #[Test]
    public function regression_adjust_stock_tidak_boleh_error_500_karena_user_id_null()
    {
        // Ini adalah reproduksi PERSIS dari bug yang dilaporkan:
        // memanggil adjust-stock via Sanctum bearer token → user_id null → 500
        $response = $this->putJson(
            route('api.inventory.adjust-stock', $this->sparepart->id),
            [
                'type' => 'decrement',
                'quantity' => 1,
                'description' => 'Teknisi Mengganti Item',
            ],
            $this->tokenHeader()
        );

        // Harus BUKAN 500. Bug #001 menyebabkan 500 karena user_id null.
        $response->assertStatus(200);

        // user_id di stock_logs TIDAK boleh null
        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $this->sparepart->id,
            'user_id' => $this->superadmin->id,
            'type' => 'keluar',
            'quantity' => 1,
            'status' => 'approved',
        ]);
    }

    // ── Decrement (keluar) ─────────────────────────────────────────

    #[Test]
    public function adjust_stock_decrement_mengurangi_stok_dan_membuat_log()
    {
        $initialStock = $this->sparepart->stock;

        $response = $this->putJson(
            route('api.inventory.adjust-stock', $this->sparepart->id),
            ['type' => 'decrement', 'quantity' => 3, 'description' => 'Digunakan untuk maintenance'],
            $this->tokenHeader()
        );

        $response->assertOk()
            ->assertJsonPath('data.current_stock', $initialStock - 3);

        $this->assertEquals($initialStock - 3, $this->sparepart->fresh()->stock);
    }

    #[Test]
    public function adjust_stock_decrement_gagal_jika_stok_tidak_cukup()
    {
        $this->sparepart->update(['stock' => 2]);

        $response = $this->putJson(
            route('api.inventory.adjust-stock', $this->sparepart->id),
            ['type' => 'decrement', 'quantity' => 10],
            $this->tokenHeader()
        );

        $response->assertStatus(400)
            ->assertJsonPath('status', 'error');

        // Stok tidak boleh berubah
        $this->assertEquals(2, $this->sparepart->fresh()->stock);
    }

    // ── Increment (masuk) ──────────────────────────────────────────

    #[Test]
    public function adjust_stock_increment_menambah_stok_dan_membuat_log()
    {
        $initialStock = $this->sparepart->stock;

        $response = $this->putJson(
            route('api.inventory.adjust-stock', $this->sparepart->id),
            ['type' => 'increment', 'quantity' => 10, 'description' => 'Restock dari supplier'],
            $this->tokenHeader()
        );

        $response->assertOk()
            ->assertJsonPath('data.current_stock', $initialStock + 10);

        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $this->sparepart->id,
            'user_id' => $this->superadmin->id,
            'type' => 'masuk',
            'quantity' => 10,
        ]);
    }

    // ── Validasi input ─────────────────────────────────────────────

    #[Test]
    public function adjust_stock_gagal_jika_type_tidak_valid()
    {
        $response = $this->putJson(
            route('api.inventory.adjust-stock', $this->sparepart->id),
            ['type' => 'invalid_type', 'quantity' => 5],
            $this->tokenHeader()
        );

        $response->assertStatus(422);
    }

    #[Test]
    public function adjust_stock_gagal_jika_quantity_nol_atau_negatif()
    {
        $response = $this->putJson(
            route('api.inventory.adjust-stock', $this->sparepart->id),
            ['type' => 'decrement', 'quantity' => 0],
            $this->tokenHeader()
        );

        $response->assertStatus(422);
    }

    #[Test]
    public function adjust_stock_gagal_jika_item_tidak_ditemukan()
    {
        $response = $this->putJson(
            route('api.inventory.adjust-stock', 99999), // ID tidak ada
            ['type' => 'increment', 'quantity' => 1],
            $this->tokenHeader()
        );

        $response->assertStatus(404);
    }

    // ── Auth ───────────────────────────────────────────────────────

    #[Test]
    public function adjust_stock_gagal_tanpa_token_auth()
    {
        $response = $this->putJson(
            route('api.inventory.adjust-stock', $this->sparepart->id),
            ['type' => 'increment', 'quantity' => 1]
            // Tanpa Authorization header
        );

        $response->assertStatus(401);
    }

    #[Test]
    public function adjust_stock_gagal_dengan_token_tidak_valid()
    {
        $response = $this->putJson(
            route('api.inventory.adjust-stock', $this->sparepart->id),
            ['type' => 'increment', 'quantity' => 1],
            ['Authorization' => 'Bearer token_palsu_yang_tidak_valid']
        );

        $response->assertStatus(401);
    }

    // ── Response structure ─────────────────────────────────────────

    #[Test]
    public function adjust_stock_mengembalikan_struktur_response_yang_benar()
    {
        $response = $this->putJson(
            route('api.inventory.adjust-stock', $this->sparepart->id),
            ['type' => 'increment', 'quantity' => 5],
            $this->tokenHeader()
        );

        $response->assertOk()
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'current_stock',
                    'minimum_stock',
                    'is_low_stock',
                    'part_number',
                ],
            ]);
    }

    #[Test]
    public function adjust_stock_menandai_is_low_stock_jika_stok_di_bawah_minimum()
    {
        $this->sparepart->update(['stock' => 6, 'minimum_stock' => 10]);

        $response = $this->putJson(
            route('api.inventory.adjust-stock', $this->sparepart->id),
            ['type' => 'decrement', 'quantity' => 2],
            $this->tokenHeader()
        );

        $response->assertOk()
            ->assertJsonPath('data.is_low_stock', true);
    }

    #[Test]
    public function approved_by_diisi_dengan_user_yang_melakukan_request()
    {
        $this->putJson(
            route('api.inventory.adjust-stock', $this->sparepart->id),
            ['type' => 'increment', 'quantity' => 1],
            $this->tokenHeader()
        );

        // approved_by HARUS diisi, bukan null
        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $this->sparepart->id,
            'approved_by' => $this->superadmin->id,
            'status' => 'approved',
        ]);
    }
}

