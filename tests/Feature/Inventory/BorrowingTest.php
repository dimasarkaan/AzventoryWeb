<?php

namespace Tests\Feature\Inventory;

use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BorrowingTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->superAdmin = User::factory()->create(['role' => 'superadmin']);
        
        // Mock Storage for photos
        Storage::fake('public');

        // MOCK ImageOptimizationService
        $this->mock(\App\Services\ImageOptimizationService::class, function ($mock) {
            $mock->shouldReceive('optimizeAndSave')->andReturn('dummy/path.webp');
        });
    }

    /** @test */
    public function superadmin_can_borrow_item()
    {
        $item = Sparepart::factory()->create([
            'stock' => 10,
            'condition' => 'Baik',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('inventory.borrow.store', $item->id), [
                'quantity' => 2,
                'expected_return_at' => now()->addDays(3)->format('Y-m-d'),
                'notes' => 'Testing Borrow',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check Inventory Reduced
        $this->assertDatabaseHas('spareparts', [
            'id' => $item->id,
            'stock' => 8,
        ]);

        // Check Borrowing Record
        $this->assertDatabaseHas('borrowings', [
            'sparepart_id' => $item->id,
            'user_id' => $this->superAdmin->id,
            'quantity' => 2,
            'status' => 'borrowed',
        ]);
    }

    /** @test */
    public function superadmin_cannot_borrow_more_than_stock()
    {
        $item = Sparepart::factory()->create(['stock' => 5]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('inventory.borrow.store', $item->id), [
                'quantity' => 10,
                'expected_return_at' => now()->addDays(1)->format('Y-m-d'),
            ]);

        $response->assertSessionHasErrors(['borrow_error']);
    }

    /** @test */
    public function superadmin_can_return_borrowed_item_good_condition()
    {
        $item = Sparepart::factory()->create(['stock' => 8]);
        
        $borrowing = Borrowing::create([
            'sparepart_id' => $item->id,
            'user_id' => $this->superAdmin->id,
            'borrower_name' => $this->superAdmin->name,
            'quantity' => 2,
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(3),
            'status' => 'borrowed',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('inventory.borrow.return', $borrowing->id), [
                'return_quantity' => 2,
                'return_condition' => 'good',
                'return_notes' => 'Returned successfully',
                'return_photos' => [UploadedFile::fake()->image('proof.jpg')],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check Borrowing Status Updated
        $this->assertDatabaseHas('borrowings', [
            'id' => $borrowing->id,
            'status' => 'returned',
        ]);

        // Check Inventory Restored
        $this->assertDatabaseHas('spareparts', [
            'id' => $item->id,
            'stock' => 10, // 8 + 2
        ]);
    }
}
