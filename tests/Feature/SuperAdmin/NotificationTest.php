<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->create(['role' => 'superadmin']);
    }

    /** @test */
    public function superadmin_can_view_notifications()
    {
        // Seed Notification
        $notification = $this->superAdmin->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => 'App\Notifications\StockRequestNotification',
            'data' => [
                'message' => 'Test Notification',
                'url' => '#',
            ],
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Notification');
    }

    /** @test */
    public function superadmin_can_mark_notification_as_read()
    {
        $notification = $this->superAdmin->notifications()->create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'type' => 'App\Notifications\StockRequestNotification',
            'data' => [
                'message' => 'Test Notification',
                'url' => '/dashboard', 
            ],
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->patch(route('notifications.read', $notification->id));

        $response->assertRedirect('/dashboard');
        
        $this->assertNotNull($notification->fresh()->read_at);
    }
    /** @test */
    public function test_low_stock_notification_can_be_triggered()
    {
        \Illuminate\Support\Facades\Notification::fake();

        // Create sparepart with low stock
        $sparepart = \App\Models\Sparepart::factory()->make([
            'name' => 'Low Stock Item',
            'stock' => 1,
            'minimum_stock' => 5
        ]);

        // Trigger notification manually (simulating Controller logic)
        $this->superAdmin->notify(new \App\Notifications\LowStockNotification($sparepart));

        // Assert notification was sent to the user
        \Illuminate\Support\Facades\Notification::assertSentTo(
            [$this->superAdmin],
            \App\Notifications\LowStockNotification::class
        );
    }
}
