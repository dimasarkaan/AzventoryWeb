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
        // 1. Create initial item
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

        // 2. Add EXACT duplicate (same data) with more stock
        $duplicateData = $data;
        $duplicateData['stock'] = 5;

        $response2 = $this->actingAs($this->user)
            ->post(route('inventory.store'), $duplicateData);

        $response2->assertRedirect();
        
        // Assert session has success message about merging
        $response2->assertSessionHas('success');
        
        // Assert stock is merged (10 + 5 = 15) and NO new row created
        $this->assertDatabaseCount('spareparts', 1);
        $this->assertDatabaseHas('spareparts', [
            'part_number' => 'K120-LOGI',
            'stock' => 15
        ]);
        
        // Assert Stock Log was created for the addition
        $this->assertDatabaseCount('stock_logs', 2); // 1 initial + 1 merge
    }

    /** @test */
    public function it_creates_new_item_if_one_attribute_differs()
    {
        // 1. Create initial item
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

        // 2. Add similar item but different CONDITION (e.g. Rusak)
        $diffData = $data;
        $diffData['condition'] = 'Rusak';
        $diffData['stock'] = 3;

        $response = $this->actingAs($this->user)
            ->post(route('inventory.store'), $diffData);

        $response->assertRedirect();

        // Assert NEW row created
        $this->assertDatabaseCount('spareparts', 2);
        $this->assertDatabaseHas('spareparts', ['part_number' => 'K120-LOGI', 'condition' => 'Baik', 'stock' => 10]);
        $this->assertDatabaseHas('spareparts', ['part_number' => 'K120-LOGI', 'condition' => 'Rusak', 'stock' => 3]);
    }
}
