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
}
