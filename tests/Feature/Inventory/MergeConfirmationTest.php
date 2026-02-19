<?php

namespace Tests\Feature\Inventory;

use App\Models\Sparepart;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MergeConfirmationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Normal update tanpa duplikat tidak muncul konfirmasi
     */
    public function test_edit_barang_tanpa_duplikat_tidak_muncul_konfirmasi()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $sparepart = Sparepart::factory()->create([
            'part_number' => 'ABC123',
            'name' => 'Mouse',
            'brand' => 'Logitech',
            'category' => 'Elektronik',
            'condition' => 'Hilang',
            'location' => 'Cibubur',
            'type' => 'asset',
            'stock' => 2,
        ]);

        // Update yang tidak membuat duplikat
        $response = $this->actingAs($admin)->put(route('inventory.update', $sparepart), [
            'part_number' => 'ABC123',
            'name' => 'Mouse',
            'brand' => 'Logitech',
            'category' => 'Elektronik',
            'condition' => 'Baik', // Berubah tapi tidak ada duplikat
            'location' => 'Jakarta', // Berubah
            'type' => 'asset',
            'stock' => 3,
            'unit' => 'Pcs',
            'age' => 'Baru',
            'minimum_stock' => 1,
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('inventory.index'));
        $response->assertSessionHasNoErrors();
        $response->assertSessionMissing('duplicate_detected');

        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'condition' => 'Baik',
            'location' => 'Jakarta',
        ]);
    }

    /**
     * Test: Edit yang menjadi duplikat muncul konfirmasi
     */
    public function test_edit_barang_yang_menjadi_duplikat_muncul_konfirmasi()
    {
        $admin = User::factory()->create(['role' => UserRole::SUPERADMIN]);

        // Existing item dengan kondisi "Baik"
        $existingItem = Sparepart::factory()->create([
            'part_number' => 'ABC123',
            'name' => 'Mouse',
            'brand' => 'Logitech',
            'category' => 'Elektronik',
            'condition' => 'Baik',
            'location' => 'Cibubur',
            'type' => 'asset',
            'stock' => 5,
            'color' => 'Hitam',
            'price' => 100000,
            'unit' => 'Pcs',
        ]);

        // New item dengan kondisi "Hilang"
        $itemToEdit = Sparepart::factory()->create([
            'part_number' => 'ABC123',
            'name' => 'Mouse',
            'brand' => 'Logitech',
            'category' => 'Elektronik',
            'condition' => 'Hilang',
            'location' => 'Cibubur',
            'type' => 'asset',
            'stock' => 2,
            'color' => 'Hitam',
            'price' => 100000,
            'unit' => 'Pcs',
        ]);

        // Edit kondisi jadi "Baik" - akan membuat duplikat
        $response = $this->from(route('inventory.edit', $itemToEdit))->actingAs($admin)->put(route('inventory.update', $itemToEdit), [
            'part_number' => 'ABC123',
            'name' => 'Mouse',
            'brand' => 'Logitech',
            'category' => 'Elektronik',
            'condition' => 'Baik', // Ubah jadi sama dengan existing
            'location' => 'Cibubur',
            'type' => 'asset',
            'stock' => 2,
            'unit' => 'Pcs',
            'color' => 'Hitam',
            'price' => 100000,
            'age' => 'Baru',
            'minimum_stock' => 1,
            'status' => 'aktif',
        ]);

        // Harus redirect kembali dengan session duplicate_detected
        $response->assertRedirect();
        $response->assertSessionHas('duplicate_detected', true);
        $response->assertSessionHas('duplicate_item');
        $response->assertSessionHas('current_item');
    }

    /**
     * Test: User pilih merge, stock bergabung
     */
    public function test_user_pilih_merge_stock_bergabung()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $existingItem = Sparepart::factory()->create([
            'part_number' => 'ABC123',
            'name' => 'Mouse',
            'brand' => 'Logitech',
            'category' => 'Elektronik',
            'condition' => 'Baik',
            'location' => 'Cibubur',
            'type' => 'asset',
            'stock' => 5,
            'color' => null,
            'price' => null,
            'unit' => 'Pcs',
        ]);

        $itemToMerge = Sparepart::factory()->create([
            'part_number' => 'ABC123',
            'name' => 'Mouse',
            'brand' => 'Logitech',
            'category' => 'Elektronik',
            'condition' => 'Hilang',
            'location' => 'Cibubur',
            'type' => 'asset',
            'stock' => 2,
            'color' => null,
            'price' => null,
            'unit' => 'Pcs',
        ]);

        // Kirim request dengan merge_confirmed
        $response = $this->actingAs($admin)->put(route('inventory.update', $itemToMerge), [
            'part_number' => 'ABC123',
            'name' => 'Mouse',
            'brand' => 'Logitech',
            'category' => 'Elektronik',
            'condition' => 'Baik',
            'location' => 'Cibubur',
            'type' => 'asset',
            'stock' => 2,
            'unit' => 'Pcs',
            'age' => 'Baru',
            'minimum_stock' => 1,
            'status' => 'aktif',
            'merge_confirmed' => 'true',
            'duplicate_id' => $existingItem->id,
        ]);

        $response->assertRedirect(route('inventory.index'));
        $response->assertSessionHas('success');

        // Verify: existing item stock bertambah
        $existingItem->refresh();
        $this->assertEquals(7, $existingItem->stock); // 5 + 2

        // Verify: item yang di-merge sudah di-soft delete
        $this->assertSoftDeleted('spareparts', [
            'id' => $itemToMerge->id
        ]);
    }

    /**
     * Test: User pilih keep separate, tetap terpisah
     */
    public function test_user_pilih_keep_separate_tetap_terpisah()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $existingItem = Sparepart::factory()->create([
            'part_number' => 'ABC123',
            'name' => 'Mouse',
            'brand' => 'Logitech',
            'category' => 'Elektronik',
            'condition' => 'Baik',
            'location' => 'Cibubur',
            'type' => 'asset',
            'stock' => 5,
            'color' => null,
            'price' => null,
            'unit' => 'Pcs',
        ]);

        $itemToEdit = Sparepart::factory()->create([
            'part_number' => 'ABC123',
            'name' => 'Mouse',
            'brand' => 'Logitech',
            'category' => 'Elektronik',
            'condition' => 'Hilang',
            'location' => 'Cibubur',
            'type' => 'asset',
            'stock' => 2,
            'color' => null,
            'price' => null,
            'unit' => 'Pcs',
        ]);

        // Kirim request dengan keep_separate
        $response = $this->actingAs($admin)->put(route('inventory.update', $itemToEdit), [
            'part_number' => 'ABC123',
            'name' => 'Mouse',
            'brand' => 'Logitech',
            'category' => 'Elektronik',
            'condition' => 'Baik',
            'location' => 'Cibubur',
            'type' => 'asset',
            'stock' => 2,
            'unit' => 'Pcs',
            'age' => 'Baru',
            'minimum_stock' => 1,
            'status' => 'aktif',
            'keep_separate' => 'true',
        ]);

        $response->assertRedirect(route('inventory.index'));
        $response->assertSessionHas('success');

        // Verify: Kedua item tetap ada
        $this->assertDatabaseHas('spareparts', [
            'id' => $existingItem->id,
            'stock' => 5, // Tidak berubah
        ]);

        $this->assertDatabaseHas('spareparts', [
            'id' => $itemToEdit->id,
            'condition' => 'Baik', // Berubah
            'stock' => 2,
        ]);
    }

    /**
     * Test: Merge transfer borrowing history
     */
    public function test_merge_transfer_borrowing_history()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $borrower = User::factory()->create(['role' => UserRole::OPERATOR]);

        $existingItem = Sparepart::factory()->create([
            'part_number' => 'ABC123',
            'name' => 'Mouse',
            'brand' => 'Logitech',
            'category' => 'Elektronik',
            'condition' => 'Baik',
            'location' => 'Cibubur',
            'type' => 'asset',
            'stock' => 5,
            'color' => null,
            'price' => null,
            'unit' => 'Pcs',
        ]);

        $itemToMerge = Sparepart::factory()->create([
            'part_number' => 'ABC123',
            'name' => 'Mouse',
            'brand' => 'Logitech',
            'category' => 'Elektronik',
            'condition' => 'Hilang',
            'location' => 'Cibubur',
            'type' => 'asset',
            'stock' => 2,
            'color' => null,
            'price' => null,
            'unit' => 'Pcs',
        ]);

        // Create borrowing history untuk item yang akan di-merge (sudah returned)
        $borrowing = $itemToMerge->borrowings()->create([
            'user_id' => $borrower->id,
            'borrower_name' => $borrower->name,
            'quantity' => 1,
            'borrowed_at' => now()->subDays(5),
            'expected_return_at' => now()->subDays(1),
            'returned_at' => now()->subDays(1),
            'status' => 'returned',
        ]);

        // Merge items
        $response = $this->actingAs($admin)->put(route('inventory.update', $itemToMerge), [
            'part_number' => 'ABC123',
            'name' => 'Mouse',
            'brand' => 'Logitech',
            'category' => 'Elektronik',
            'condition' => 'Baik',
            'location' => 'Cibubur',
            'type' => 'asset',
            'stock' => 2,
            'unit' => 'Pcs',
            'age' => 'Baru',
            'minimum_stock' => 1,
            'status' => 'aktif',
            'merge_confirmed' => 'true',
            'duplicate_id' => $existingItem->id,
        ]);

        $response->assertRedirect(route('inventory.index'));

        // Verify borrowing history dipindahkan
        $this->assertDatabaseHas('borrowings', [
            'id' => $borrowing->id,
            'sparepart_id' => $existingItem->id, // Pindah ke existing item
        ]);
    }

    /**
     * Test: Tidak bisa merge jika item sedang dipinjam
     */
    public function test_cannot_merge_if_item_currently_borrowed()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $borrower = User::factory()->create(['role' => UserRole::OPERATOR]);

        $existingItem = Sparepart::factory()->create([
            'part_number' => 'ABC123',
            'name' => 'Mouse',
            'brand' => 'Logitech',
            'category' => 'Elektronik',
            'condition' => 'Baik',
            'location' => 'Cibubur',
            'type' => 'asset',
            'stock' => 5,
            'color' => null,
            'price' => null,
            'unit' => 'Pcs',
        ]);

        $itemToMerge = Sparepart::factory()->create([
            'part_number' => 'ABC123',
            'name' => 'Mouse',
            'brand' => 'Logitech',
            'category' => 'Elektronik',
            'condition' => 'Hilang',
            'location' => 'Cibubur',
            'type' => 'asset',
            'stock' => 3,
            'color' => null,
            'price' => null,
            'unit' => 'Pcs',
        ]);

        // Create active borrowing (status borrowed)
        $itemToMerge->borrowings()->create([
            'user_id' => $borrower->id,
            'borrower_name' => $borrower->name,
            'quantity' => 1,
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(7),
            'status' => 'borrowed',
        ]);

        // Try to merge
        $response = $this->actingAs($admin)->put(route('inventory.update', $itemToMerge), [
            'part_number' => 'ABC123',
            'name' => 'Mouse',
            'brand' => 'Logitech',
            'category' => 'Elektronik',
            'condition' => 'Baik',
            'location' => 'Cibubur',
            'type' => 'asset',
            'stock' => 3,
            'unit' => 'Pcs',
            'age' => 'Baru',
            'minimum_stock' => 1,
            'status' => 'aktif',
            'merge_confirmed' => 'true',
            'duplicate_id' => $existingItem->id,
        ]);

        // Should redirect with error
        $response->assertRedirect(route('inventory.edit', $itemToMerge));
        $response->assertSessionHas('error');

        // Verify tidak di-merge
        $existingItem->refresh();
        $this->assertEquals(5, $existingItem->stock); // Tidak berubah

        $this->assertDatabaseHas('spareparts', [
            'id' => $itemToMerge->id,
            'deleted_at' => null, // Tidak di-delete
        ]);
    }
}
