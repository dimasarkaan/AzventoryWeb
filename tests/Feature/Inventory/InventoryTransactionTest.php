<?php

namespace Tests\Feature\Inventory;

use App\Models\Action;
use App\Models\ActivityLog;
use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InventoryTransactionTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        \Illuminate\Support\Facades\Storage::fake('public');
        \Illuminate\Support\Facades\Notification::fake();

        // MOCK ImageOptimizationService to avoid GD requirement
        $this->mock(\App\Services\ImageOptimizationService::class, function ($mock) {
            $mock->shouldReceive('optimizeAndSave')->andReturn('dummy/path.webp');
        });

        // MOCK QrCodeService to avoid file generation
        $this->mock(\App\Services\QrCodeService::class, function ($mock) {
            $mock->shouldReceive('generate')->andReturn('dummy/qrcode.svg');
            $mock->shouldReceive('generateLabelSvg')->andReturn('<svg>...</svg>');
        });

        // Create SuperAdmin user
        $this->user = User::factory()->create([
            'role' => 'superadmin',
            'password_changed_at' => now(),
        ]);
    }

    /** @test */
    public function it_creates_sparepart_creates_stock_log_and_activity_log_atomically()
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

        // 1. Check Sparepart Created
        $this->assertDatabaseHas('spareparts', [
            'part_number' => 'PN-TRANS-001',
            'stock' => 10,
        ]);

        $sparepart = Sparepart::where('part_number', 'PN-TRANS-001')->first();

        // 2. Check Stock Log Created (Critical for Data Integrity)
        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $sparepart->id,
            'type' => 'masuk',
            'quantity' => 10,
            'reason' => 'Stok awal (Item baru)',
            'user_id' => $this->user->id,
        ]);

        // 3. Check Activity Log Created
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'action' => 'Sparepart Dibuat',
            'description' => "Sparepart '{$sparepart->name}' (PN: {$sparepart->part_number}) telah ditambahkan.",
        ]);
    }

    /** @test */
    public function it_updates_sparepart_and_logs_activity()
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

        // Check Update
        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'name' => 'Updated Name',
        ]);

        // Check Activity Log
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'action' => 'Sparepart Diperbarui',
        ]);
    }

    /** @test */
    public function it_soft_deletes_sparepart_and_logs_activity()
    {
        $sparepart = Sparepart::factory()->create();

        $response = $this->actingAs($this->user)
            ->delete(route('inventory.destroy', $sparepart->id));

        $response->assertRedirect(route('inventory.index'));
        $response->assertSessionHas('success');

        // Check Soft Delete
        $this->assertSoftDeleted('spareparts', [
            'id' => $sparepart->id,
        ]);

        // Check Activity Log
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'action' => 'Sparepart Dihapus',
        ]);
    }

    /** @test */
    public function it_restores_sparepart_and_logs_activity()
    {
        $sparepart = Sparepart::factory()->create();
        $sparepart->delete(); // Soft delete first

        $response = $this->actingAs($this->user)
            ->patch(route('inventory.restore', $sparepart->id));

        $response->assertRedirect(route('inventory.index') . '?trash=true');
        $response->assertSessionHas('success');

        // Check Restore
        $this->assertNotSoftDeleted('spareparts', [
            'id' => $sparepart->id,
        ]);

        // Check Activity Log
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'action' => 'Sparepart Dipulihkan',
        ]);
    }

    /** @test */
    public function it_force_deletes_sparepart_and_logs_activity()
    {
        $sparepart = Sparepart::factory()->create();
        $sparepart->delete(); // Soft delete first

        $response = $this->actingAs($this->user)
            ->delete(route('inventory.force-delete', $sparepart->id));

        $response->assertRedirect(route('inventory.index') . '?trash=true');
        $response->assertSessionHas('success');

        // Check Force Delete
        $this->assertDatabaseMissing('spareparts', [
            'id' => $sparepart->id,
        ]);

        // Check Activity Log
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->user->id,
            'action' => 'Sparepart Dihapus Permanen',
        ]);
    }

    /** @test */
    public function it_handles_duplicate_entry_merging_correctly()
    {
        // 1. Create Initial Item
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

        // 2. Submit Duplicate Data
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
            
        $response->assertSessionHas('success'); // Should be success (merged)

        // 3. Verify Stock Merged
        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'stock' => 15, // 10 + 5
        ]);

        // 4. Verify Stock Log for merging
        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $sparepart->id,
            'type' => 'masuk',
            'quantity' => 5,
            'reason' => 'Penambahan stok (Duplicate Entry)',
        ]);

        // 5. Verify NO new item
        $this->assertDatabaseCount('spareparts', 1);
    }
}
