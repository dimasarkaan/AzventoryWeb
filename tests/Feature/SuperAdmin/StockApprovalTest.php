<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StockApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;
    protected $operator;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->superAdmin = User::factory()->create(['role' => 'superadmin']);
        $this->operator = User::factory()->create(['role' => 'operator']);

        Notification::fake();
    }

    /** @test */
    public function superadmin_can_view_pending_approvals()
    {
        $item = Sparepart::factory()->create(['stock' => 10]);
        
        // Seed Pending Log
        StockLog::create([
            'sparepart_id' => $item->id,
            'user_id' => $this->operator->id,
            'type' => 'masuk',
            'quantity' => 5,
            'reason' => 'Restock',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.stock-approvals.index'));

        $response->assertStatus(200);
        $response->assertSee('Restock');
        $response->assertSee($item->name);
    }

    /** @test */
    public function superadmin_can_approve_stock_request()
    {
        $item = Sparepart::factory()->create(['stock' => 10]);
        
        $log = StockLog::create([
            'sparepart_id' => $item->id,
            'user_id' => $this->operator->id,
            'type' => 'masuk',
            'quantity' => 5,
            'reason' => 'Restock',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.stock-approvals.update', $log->id), [
                'status' => 'approved'
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check DB Status
        $this->assertDatabaseHas('stock_logs', [
            'id' => $log->id,
            'status' => 'approved',
            'approved_by' => $this->superAdmin->id,
        ]);

        // Check Inventory Update
        $this->assertDatabaseHas('spareparts', [
            'id' => $item->id,
            'stock' => 15, // 10 + 5
        ]);

        // Check Notification Sent to Operator
        Notification::assertSentTo(
            [$this->operator],
            \App\Notifications\StockRequestNotification::class
        );
    }

    /** @test */
    public function superadmin_can_reject_stock_request()
    {
        $item = Sparepart::factory()->create(['stock' => 10]);
        
        $log = StockLog::create([
            'sparepart_id' => $item->id,
            'user_id' => $this->operator->id,
            'type' => 'masuk',
            'quantity' => 5,
            'reason' => 'Incorrect',
            'status' => 'pending',
        ]);



        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.stock-approvals.update', $log->id), [
                'status' => 'rejected'
            ]);

        // Check DB
        $this->assertDatabaseHas('stock_logs', [
            'id' => $log->id,
            'status' => 'rejected',
        ]);

        // Check Inventory UNCHANGED
        $this->assertDatabaseHas('spareparts', [
            'id' => $item->id,
            'stock' => 10,
        ]);
    }
}
