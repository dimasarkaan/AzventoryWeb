<?php

namespace Tests\Feature\Inventory;

use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesTransaksiInventaris extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        \Illuminate\Support\Facades\Storage::fake('public');
        \Illuminate\Support\Facades\Notification::fake();

        // MOCK ImageOptimizationService untuk menghindari kebutuhan GD
        $this->mock(\App\Services\ImageOptimizationService::class, function ($mock) {
            $mock->shouldReceive('optimizeAndSave')->andReturn('dummy/path.webp');
        });

        // MOCK QrCodeService untuk menghindari pembuatan file
        $this->mock(\App\Services\QrCodeService::class, function ($mock) {
            $mock->shouldReceive('generate')->andReturn('dummy/qrcode.svg');
            $mock->shouldReceive('generateLabelSvg')->andReturn('<svg>...</svg>');
        });

        // Buat user SuperAdmin
        $this->user = User::factory()->create([
            'role' => 'superadmin',
            'password_changed_at' => now(),
        ]);
    }

    #[Test]
    public function membuat_sparepart_membuat_log_stok_dan_log_aktivitas_secara_atomik()
    {
        $data = [
            'name' => 'Test Item Transaction',
            'part_number' => 'PN-TRANS-001',
            'brand' => 'Test Brand',
            'category' => 'Test Category',
            'location' => 'A1',
            'condition' => 'Baru',
            'age' => 'Baru',
            'type' => 'asset',
            'stock' => 10,
            'minimum_stock' => 2,
            'unit' => 'Pcs',
            'price' => 50000,
            'status' => 'aktif',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('inventory.store'), $data);

        $response->assertRedirect(route('inventory.index'));
        $response->assertSessionHas('success');

        // 1. Periksa Sparepart Dibuat
        $this->assertDatabaseHas('spareparts', [
            'part_number' => 'PN-TRANS-001',
            'stock' => 10,
        ]);

        $sparepart = Sparepart::where('part_number', 'PN-TRANS-001')->first();

        // 2. Periksa Log Stok Dibuat (Penting untuk Integritas Data)
        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $sparepart->id,
            'type' => 'masuk',
            'quantity' => 10,
            'reason' => 'Stok awal (Item baru)',
            'user_id' => $this->user->id,
        ]);

        // 3. Periksa Log Aktivitas Dibuat
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'action' => 'Sparepart Dibuat',
            'description' => "Sparepart '{$sparepart->name}' (PN: {$sparepart->part_number}) telah ditambahkan.",
        ]);
    }

    #[Test]
    public function memperbarui_sparepart_dan_mencatat_aktivitas()
    {
        $sparepart = Sparepart::factory()->create([
            'stock' => 10,
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'part_number' => $sparepart->part_number,
            'brand' => $sparepart->brand,
            'category' => $sparepart->category,
            'location' => $sparepart->location,
            'condition' => $sparepart->condition,
            'age' => 'Baru',
            'type' => $sparepart->type,
            'stock' => 10, // Unchanged stock
            'minimum_stock' => $sparepart->minimum_stock,
            'unit' => $sparepart->unit,
            'price' => $sparepart->price,
            'status' => $sparepart->status,
        ];

        $response = $this->actingAs($this->user)
            ->put(route('inventory.update', $sparepart->id), $updateData);

        $response->assertRedirect(route('inventory.index'));
        $response->assertSessionHas('success');

        // Periksa Pembaruan
        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'name' => 'Updated Name',
        ]);

        // Periksa Log Aktivitas
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'action' => 'Sparepart Diperbarui',
        ]);
    }

    #[Test]
    public function menghapus_lunak_sparepart_dan_mencatat_aktivitas()
    {
        $sparepart = Sparepart::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('inventory.destroy', $sparepart->id));

        $response->assertRedirect(route('inventory.index'));
        $response->assertSessionHas('success');

        // Periksa Soft Delete
        $this->assertSoftDeleted('spareparts', [
            'id' => $sparepart->id,
        ]);

        // Periksa Log Aktivitas
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'action' => 'Sparepart Dihapus',
        ]);
    }

    #[Test]
    public function memulihkan_sparepart_dan_mencatat_aktivitas()
    {
        $sparepart = Sparepart::factory()->create();
        $sparepart->delete(); // Soft delete first

        $response = $this->actingAs($this->user)
            ->patch(route('inventory.restore', $sparepart->id));

        $response->assertRedirect(route('inventory.index').'?trash=true');
        $response->assertSessionHas('success');

        // Periksa Pemulihan
        $this->assertNotSoftDeleted('spareparts', [
            'id' => $sparepart->id,
        ]);

        // Periksa Log Aktivitas
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'action' => 'Sparepart Dipulihkan',
        ]);
    }

    #[Test]
    public function menghapus_permanen_sparepart_dan_mencatat_aktivitas()
    {
        $sparepart = Sparepart::factory()->create();
        $sparepart->delete(); // Soft delete first

        $response = $this->actingAs($this->user)
            ->delete(route('inventory.force-delete', $sparepart->id));

        $response->assertRedirect(route('inventory.index').'?trash=true');
        $response->assertSessionHas('success');

        // Periksa Hapus Permanen
        $this->assertDatabaseMissing('spareparts', [
            'id' => $sparepart->id,
        ]);

        // Periksa Log Aktivitas
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'action' => 'Sparepart Dihapus Permanen',
        ]);
    }

    #[Test]
    public function menangani_penggabungan_entri_duplikat_dengan_benar()
    {
        // 1. Buat Item Awal
        $sparepart = Sparepart::factory()->create([
            'part_number' => 'PN-DUP-001',
            'name' => 'Duplicate Candidate',
            'stock' => 10,
            'brand' => 'Brand A',
            'category' => 'Cat A',
            'location' => 'Loc A',
            'condition' => 'Baru',
            'type' => 'asset',
            'color' => 'Hitam',
            'price' => 50000,
            'unit' => 'Pcs',
        ]);

        // 2. Kirim Data Duplikat
        $data = [
            'part_number' => 'PN-DUP-001',
            'name' => 'Duplicate Candidate',
            'brand' => 'Brand A',
            'category' => 'Cat A',
            'location' => 'Loc A',
            'condition' => 'Baru',
            'age' => 'Baru',
            'type' => 'asset',
            'color' => 'Hitam',
            'price' => 50000,
            'unit' => 'Pcs',
            'stock' => 5, // Add 5
            'minimum_stock' => 2,
            'status' => 'aktif',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('inventory.store'), $data);

        $response->assertSessionHas('success'); // Seharusnya sukses (digabungkan)

        // 3. Verifikasi Stok Digabung
        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'stock' => 15, // 10 + 5
        ]);

        // 4. Verifikasi Log Stok untuk penggabungan
        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $sparepart->id,
            'type' => 'masuk',
            'quantity' => 5,
            'reason' => 'Penambahan stok (Duplicate Entry)',
        ]);

        // 5. Verifikasi TIDAK ada item baru
        $this->assertDatabaseCount('spareparts', 1);
    }
}

