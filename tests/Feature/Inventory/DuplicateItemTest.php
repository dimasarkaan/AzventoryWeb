<?php

namespace Tests\Feature\Inventory;

use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DuplicateItemTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'role' => \App\Enums\UserRole::SUPERADMIN,
        ]);
    }

    /** @test */
    public function it_merges_stock_when_exact_duplicate_is_added()
    {
        // 1. Buat item awal
        $data = [
            'name' => 'Keyboard Logitech K120',
            'part_number' => 'K120-LOGI',
            'brand' => 'Logitech',
            'category' => 'Keyboard',
            'location' => 'Rak A1',
            'status' => 'aktif',
            'condition' => 'Baik',
            'age' => 'Baru',
            'type' => 'asset',
            'stock' => 10,
            'unit' => 'Unit',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('inventory.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('spareparts', ['part_number' => 'K120-LOGI', 'stock' => 10]);

        // 2. Tambahkan duplikat PERSIS (data sama) dengan stok lebih banyak
        $duplicateData = $data;
        $duplicateData['stock'] = 5;

        $response2 = $this->actingAs($this->user)
            ->post(route('inventory.store'), $duplicateData);

        $response2->assertRedirect();
        
        // Pastikan sesi memiliki pesan sukses tentang penggabungan
        $response2->assertSessionHas('success');
        
        // Pastikan stok digabungkan (10 + 5 = 15) dan TIDAK ada baris baru dibuat
        $this->assertDatabaseCount('spareparts', 1);
        $this->assertDatabaseHas('spareparts', [
            'part_number' => 'K120-LOGI',
            'stock' => 15
        ]);
        
        // Pastikan Log Stok dibuat untuk penambahan
        $this->assertDatabaseCount('stock_logs', 2); // 1 initial + 1 merge
    }

    /** @test */
    public function it_creates_new_item_if_one_attribute_differs()
    {
        // 1. Buat item awal
        $data = [
            'name' => 'Keyboard Logitech K120',
            'part_number' => 'K120-LOGI',
            'brand' => 'Logitech',
            'category' => 'Keyboard',
            'location' => 'Rak A1',
            'status' => 'aktif',
            'condition' => 'Baik',
            'age' => 'Baru',
            'type' => 'asset',
            'stock' => 10,
            'unit' => 'Unit',
        ];

        $this->actingAs($this->user)
            ->post(route('inventory.store'), $data);

        // 2. Tambahkan item serupa tapi KONDISI beda (misal Rusak)
        $diffData = $data;
        $diffData['condition'] = 'Rusak';
        $diffData['stock'] = 3;

        $response = $this->actingAs($this->user)
            ->post(route('inventory.store'), $diffData);

        $response->assertRedirect();

        // Pastikan baris BARU dibuat
        $this->assertDatabaseCount('spareparts', 2);
        $this->assertDatabaseHas('spareparts', ['part_number' => 'K120-LOGI', 'condition' => 'Baik', 'stock' => 10]);
        $this->assertDatabaseHas('spareparts', ['part_number' => 'K120-LOGI', 'condition' => 'Rusak', 'stock' => 3]);
    }
}
