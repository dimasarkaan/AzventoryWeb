<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\User;
use App\Models\Sparepart;
use App\Notifications\LowStockNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminAdvancedTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superadmin = User::factory()->create([
            'role' => 'superadmin',
            'password_changed_at' => now(),
            'password' => Hash::make('password'),
        ]);
    }

    // 1. REPORTING TEST (Testing Filter Logic via HTTP)
    public function test_superadmin_can_download_inventory_report_pdf()
    {
        // Create dummy data
        Sparepart::factory()->create(['name' => 'Barang A', 'stock' => 100]);

        // Request PDF download
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.reports.download', [
            'report_type' => 'inventory_list',
            'export_format' => 'pdf',
            'location' => 'all'
        ]));

        $response->assertStatus(200);
        // Assert it's a PDF stream or download
        $response->assertHeader('content-type', 'application/pdf');
    }

    // 2. NOTIFICATION TEST (Testing System Logic)
    public function test_low_stock_notification_is_sent()
    {
        Notification::fake(); // Mock Notification facade

        // Create sparepart with low stock
        $sparepart = Sparepart::factory()->make([
            'name' => 'Low Stock Item',
            'stock' => 1,
            'minimum_stock' => 5
        ]);

        // Trigger notification manually (simulating Controller logic)
        // In real controller, this happens inside update(). We test the notification class itself here.
        $this->superadmin->notify(new LowStockNotification($sparepart));

        // Assert notification was sent to the user
        Notification::assertSentTo(
            [$this->superadmin],
            LowStockNotification::class
        );
    }
    
    // 3. PROFILE TEST (Self-Management)
    public function test_superadmin_can_update_profile()
    {
        $response = $this->actingAs($this->superadmin)->patch(route('profile.update'), [
            'name' => 'New Superadmin Name',
            'email' => $this->superadmin->email, // Keep email same
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('status', 'profile-updated');
        
        $this->assertDatabaseHas('users', [
            'id' => $this->superadmin->id,
            'name' => 'New Superadmin Name'
        ]);
    }
}
