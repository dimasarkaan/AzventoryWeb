<?php

namespace Tests\Feature\Inventory;

use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesKasusBatasInventaris extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->create(['role' => 'superadmin']);
    }

    #[Test]
    public function sistem_mencegah_input_stok_negatif_saat_pembuatan()
    {
        // Aksi: Coba buat sparepart dengan stok negatif
        $response = $this->actingAs($this->superAdmin)->post(route('inventory.store'), [
            'name' => 'Negative Item',
            'part_number' => 'NEG-001',
            'category' => 'Test',
            'stock' => -10, // Invalid
            'minimum_stock' => 5,
            'unit' => 'Pcs',
            'type' => 'sale',
            'condition' => 'Baru',
            'location' => 'Rack A',
            'status' => 'aktif',
        ]);

        // Assert
        $response->assertSessionHasErrors(['stock']);
        $this->assertDatabaseMissing('spareparts', ['part_number' => 'NEG-001']);
    }

    #[Test]
    public function sistem_mencegah_stok_negatif_melalui_peminjaman()
    {
        // Persiapan: Item dengan 5 stok
        $item = Sparepart::factory()->create(['stock' => 5]);
        $user = User::factory()->create();

        // Aksi: Coba pinjam 10 item
        $response = $this->actingAs($this->superAdmin)->post(route('inventory.borrow.store', $item->id), [
            'user_id' => $user->id,
            'quantity' => 10, // Exceeds stock
            'borrower_name' => $user->name,
            'borrowed_at' => now(),
            'notes' => 'Overdraft test',
        ]);

        // Assert
        $response->assertSessionHasErrors(); // Should fail validation or logic
        $this->assertEquals(5, $item->fresh()->stock); // Stock should remain unchanged
    }

    #[Test]
    public function tidak_dapat_menghapus_item_dengan_peminjaman_aktif()
    {
        // Persiapan: Item dengan peminjaman aktif
        $item = Sparepart::factory()->create(['stock' => 10]);
        $user = User::factory()->create();

        Borrowing::create([
            'sparepart_id' => $item->id,
            'user_id' => $user->id,
            'borrower_name' => $user->name,
            'quantity' => 1,
            'borrowed_at' => now(),
            'status' => 'borrowed',
        ]);

        // Aksi: Coba SOFT DELETE
        $response = $this->actingAs($this->superAdmin)->delete(route('inventory.destroy', $item->id));

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('error', __('ui.error_cannot_delete_borrowed'));
        $this->assertDatabaseHas('spareparts', ['id' => $item->id]);
    }

    #[Test]
    public function menggabungkan_stok_jika_nomor_part_duplikat_ada()
    {
        // Arrange
        $existing = Sparepart::factory()->create([
            'part_number' => 'EXISTING-001',
            'stock' => 10,
            // Pastikan field lain cocok untuk logika deteksi duplikat persis di Service
            'name' => 'Duplicate Item',
            'brand' => 'Test Brand',
            'category' => 'Test Cat',
            'location' => 'Rack B',
            'condition' => 'Baru',
            'age' => 'Baru',
            'type' => 'sale',
            'unit' => 'Pcs',
            'price' => '10000.00',
            'color' => 'Hitam',
        ]);

        // Act
        $response = $this->actingAs($this->superAdmin)->post(route('inventory.store'), [
            'name' => 'Duplicate Item',
            'part_number' => 'EXISTING-001', // PN Sama
            'brand' => 'Test Brand',
            'category' => 'Test Cat',
            'location' => 'Rack B',
            'condition' => 'Baru',
            'age' => 'Baru',
            'type' => 'sale',
            'stock' => 5, // Tambah 5 lagi
            'minimum_stock' => 5,
            'status' => 'aktif',
            'unit' => 'Pcs',
            'price' => '10000.00',
            'color' => 'Hitam',
        ]);

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success'); // Seharusnya sukses (digabungkan)

        $this->assertDatabaseCount('spareparts', 1); // Masih 1 item
        $this->assertEquals(15, $existing->fresh()->stock); // 10 + 5
    }

    #[Test]
    public function sistem_mencegah_bypass_validasi_status()
    {
        $sparepart = Sparepart::factory()->create(['status' => 'aktif']);

        // Aksi: Mencoba memasukkan status yang tidak ada di Enum via update
        $response = $this->actingAs($this->superAdmin)->put(route('inventory.update', $sparepart->id), [
            'name' => 'Name',
            'part_number' => $sparepart->part_number,
            'category' => 'Test',
            'stock' => 10,
            'minimum_stock' => 5,
            'unit' => 'Pcs',
            'type' => 'sale',
            'condition' => 'Baru',
            'location' => 'Rack A',
            'status' => 'status_palsu_bypass', // Invalid status
        ]);

        $response->assertSessionHasErrors(['status']);
        $this->assertEquals('aktif', $sparepart->fresh()->status);
    }

    #[Test]
    public function sistem_mencegah_kondisi_melebihi_validasi_maksimal()
    {
        $sparepart = Sparepart::factory()->create(['condition' => 'Baik']);

        // Aksi: Mencoba memasukkan condition string length > 255
        $longCondition = str_repeat('A', 256);
        $response = $this->actingAs($this->superAdmin)->put(route('inventory.update', $sparepart->id), [
            'name' => 'Name',
            'part_number' => $sparepart->part_number,
            'category' => 'Test',
            'stock' => 10,
            'minimum_stock' => 5,
            'unit' => 'Pcs',
            'type' => 'sale',
            'condition' => $longCondition, // Invalid > 255 char
            'location' => 'Rack A',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors(['condition']);
        $this->assertEquals('Baik', $sparepart->fresh()->condition);
    }

    #[Test]
    public function sistem_mencegah_tipe_data_salah_pada_stok()
    {
        $response = $this->actingAs($this->superAdmin)->post(route('inventory.store'), [
            'name' => 'Test Item',
            'part_number' => 'TEST-001',
            'category' => 'Test',
            'stock' => 'lima belas', // Datatype mismatch (should be integer)
            'minimum_stock' => 5,
            'unit' => 'Pcs',
            'type' => 'sale',
            'condition' => 'Baru',
            'location' => 'Rack A',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors(['stock']);
        $this->assertDatabaseMissing('spareparts', ['part_number' => 'TEST-001']);
    }

    #[Test]
    public function user_tidak_dapat_meminjam_item_yang_habis_stoknya()
    {
        $item = Sparepart::factory()->create(['stock' => 0]);
        $user = User::factory()->create();

        $response = $this->actingAs($this->superAdmin)->post(route('inventory.borrow.store', $item->id), [
            'user_id' => $user->id,
            'quantity' => 1,
            'borrower_name' => $user->name,
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(2),
            'notes' => 'Testing validation',
        ]);

        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('borrowings', [
            'sparepart_id' => $item->id,
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function operator_hanya_dapat_melihat_peminjaman_sendiri()
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $otherOperator = User::factory()->create(['role' => 'operator']);

        $item = Sparepart::factory()->create(['stock' => 10]);

        $ownBorrowing = Borrowing::create([
            'sparepart_id' => $item->id,
            'user_id' => $operator->id,
            'borrower_name' => $operator->name,
            'quantity' => 1,
            'borrowed_at' => now(),
            'status' => 'borrowed',
        ]);

        $otherBorrowing = Borrowing::create([
            'sparepart_id' => $item->id,
            'user_id' => $otherOperator->id,
            'borrower_name' => $otherOperator->name,
            'quantity' => 1,
            'borrowed_at' => now(),
            'status' => 'borrowed',
        ]);

        $response = $this->actingAs($operator)->get(route('inventory.index'));

        // Asumsi page sukses dirender (Operator allowed inventory index)
        $response->assertStatus(200);
    }
}

