<?php

namespace Tests\Feature\Inventory;

use App\Models\User;
use App\Models\Sparepart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InventoryCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Buat user admin untuk melakukan aksi
        $this->user = User::factory()->create([
            'role' => 'superadmin',
            'password_changed_at' => now(), // Lewati persyaratan ganti password
        ]);
    }

    // Pastikan halaman daftar inventaris dapat dirender.
    public function test_inventory_list_can_be_rendered()
    {
        $response = $this->actingAs($this->user)->get(route('inventory.index'));
        $response->assertStatus(200);
    }

    // Pastikan dapat membuat sparepart baru.
    public function test_can_create_new_sparepart()
    {
        $response = $this->actingAs($this->user)->post(route('inventory.store'), [
            'name' => 'Keyboard Mechanical',
            'part_number' => 'KB-MECH-001',
            'brand' => 'Logitech', // Ditambahkan
            'category' => 'Peripheral', 
            'location' => 'Rak A1',
            'age' => 'Baru', // Ditambahkan
            'condition' => 'Baik', // Diperbarui
            'age' => 'Baru',
            'color' => 'Hitam', // Ditambahkan
            'type' => 'asset', // Ditambahkan
            'stock' => 10,
            'minimum_stock' => 2,
            'unit' => 'Unit',
            'price' => 500000,
            'status' => 'aktif',
        ]);



        if (session('errors')) {
            dump(session('errors')->all());
        }

        $response->assertRedirect(route('inventory.index'));
        $this->assertDatabaseHas('spareparts', [
            'name' => 'Keyboard Mechanical',
            'stock' => 10
        ]);
    }

    // Pastikan dapat memperbarui stok sparepart.
    public function test_can_update_sparepart_stock()
    {
        $sparepart = Sparepart::factory()->create();

        $response = $this->actingAs($this->user)->put(route('inventory.update', $sparepart), [
            'name' => $sparepart->name,
            'part_number' => $sparepart->part_number,
            'brand' => $sparepart->brand, // Ditambahkan
            'category' => $sparepart->category,
            'location' => $sparepart->location,
            'age' => $sparepart->age, // Ditambahkan
            'condition' => $sparepart->condition,
            'color' => $sparepart->color, // Ditambahkan
            'type' => $sparepart->type, // Ditambahkan
            'stock' => 50, // Stok diperbarui
            'minimum_stock' => 5,
            'unit' => 'Unit',
            'price' => $sparepart->price,
            'status' => $sparepart->status,
        ]);

        $response->assertRedirect(route('inventory.index'));
        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'stock' => 50
        ]);
    }

    // Pastikan dapat menghapus sparepart (soft delete).
    public function test_can_delete_sparepart()
    {
        $this->withoutExceptionHandling();
        $sparepart = Sparepart::factory()->create();
        
        $this->assertDatabaseCount('spareparts', 1); // Pastikan ada

        $response = $this->actingAs($this->user)->delete(route('inventory.destroy', $sparepart));

        $response->assertRedirect(route('inventory.index'));
        


        // $this->assertDatabaseCount('spareparts', 0); // Seharusnya 0 jika hard delete
        $this->assertSoftDeleted('spareparts', ['id' => $sparepart->id]);
    }
    // Pastikan superadmin dapat memeriksa ketersediaan part number.
    public function test_superadmin_can_check_part_number_availability()
    {
        // Buat part yang sudah ada
        Sparepart::factory()->create(['part_number' => 'EXISTING-001']);

        // Cek yang sudah ada
        $response = $this->actingAs($this->user)->get(route('inventory.check-part-number', ['part_number' => 'EXISTING-001']));
        $response->assertStatus(200);
        $response->assertJson(['exists' => true]);

        // Cek yang baru
        $response2 = $this->actingAs($this->user)->get(route('inventory.check-part-number', ['part_number' => 'NEW-001']));
        $response2->assertStatus(200);
        $response2->assertJson(['exists' => false]);
    }
}
