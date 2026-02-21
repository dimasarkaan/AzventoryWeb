<?php

namespace Tests\Feature\Inventory;

use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReturnBorrowingTest extends TestCase
{
    use RefreshDatabase;

    public function test_return_item_validation_good_condition_needs_photo()
    {
        Storage::fake('public');
        
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $sparepart = Sparepart::factory()->create(['stock' => 10]);
        $borrowing = Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $admin->id,
            'borrower_name' => $admin->name,
            'quantity' => 5,
            'borrowed_at' => now(),
            'status' => 'borrowed',
        ]);

        $this->actingAs($admin);

        // Case 1: Photos explicitly null/missing for 'good' condition
        // Expectations: Should fail validation 422 because required_if:return_condition,good
        $response = $this->postJson(route('inventory.borrow.return', $borrowing), [
            'return_quantity' => 1,
            'return_condition' => 'good',
            'return_photos' => null, 
        ]);

        // If my theory about closure skipping is correct, this might PASS or fail on min:1?
        // But if it fails, it returns 422.
        // Let's see.
        
        // Actually, logic is: user sees 422. So something IS failing.
        // If it requires photo and none sent, it fails.
        // If we verify this fails 422, then the frontend issue is confirmed (photo not sent).
        
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['return_photos']);
    }

    public function test_return_item_validation_max_quantity()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $sparepart = Sparepart::factory()->create(['stock' => 10]);
        $borrowing = Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $admin->id,
            'borrower_name' => $admin->name,
            'quantity' => 5,
            'borrowed_at' => now(),
            'status' => 'borrowed',
        ]);

        $this->actingAs($admin);

        $response = $this->postJson(route('inventory.borrow.return', $borrowing), [
            'return_quantity' => 6, // Exceeds 5
            'return_condition' => 'lost',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['return_quantity']);
    }

    public function test_return_item_success_with_photos()
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $sparepart = Sparepart::factory()->create(['stock' => 10]);
        $borrowing = Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $admin->id,
            'borrower_name' => $admin->name,
            'quantity' => 5,
            'borrowed_at' => now(),
            'status' => 'borrowed',
        ]);

        $this->actingAs($admin);

        $response = $this->postJson(route('inventory.borrow.return', $borrowing), [
            'return_quantity' => 1,
            'return_condition' => 'good',
            'return_photos' => [
                UploadedFile::fake()->image('evidence.jpg')
            ]
        ]);

        $response->assertStatus(200); // Json success
    }

    public function test_return_item_with_damaged_condition_updates_inventory()
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $sparepart = Sparepart::factory()->create([
            'stock' => 8, // Stock became 8 after borrowing 2
            'condition' => 'Baik'
        ]);
        $borrowing = Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $admin->id,
            'borrower_name' => $admin->name,
            'quantity' => 2,
            'borrowed_at' => now(),
            'status' => 'borrowed',
        ]);

        $this->actingAs($admin);

        $response = $this->postJson(route('inventory.borrow.return', $borrowing), [
            'return_quantity' => 2,
            'return_condition' => 'bad', // Rusak
            'return_notes' => 'Ternyata rusak pas dipakai',
            'return_photos' => [
                UploadedFile::fake()->image('broken_evidence.jpg')
            ]
        ]);

        $response->assertStatus(200);

        // Verify borrowing is updated (status only)
        $this->assertDatabaseHas('borrowings', [
            'id' => $borrowing->id,
            'status' => 'returned'
        ]);
        
        // Verify borrowing_returns receives the 'bad' condition
        $this->assertDatabaseHas('borrowing_returns', [
            'borrowing_id' => $borrowing->id,
            'condition' => 'bad'
        ]);

        // Verify that a new sparepart record for 'Rusak' is created or the general stock logic handles it
        // Actually, based on InventoryController/Service logic, returning a damaged item creates a new item with 'Rusak' condition
        $this->assertDatabaseHas('spareparts', [
            'name' => $sparepart->name,
            'condition' => 'Rusak',
            'stock' => 2
        ]);
        
        // And the original 'Baik' item's stock remains reduced by the borrowed amount (10 - 2 = 8)
        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'stock' => 8,
            'condition' => 'Baik'
        ]);
    }
}
