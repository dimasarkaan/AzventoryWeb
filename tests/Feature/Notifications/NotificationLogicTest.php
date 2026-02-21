<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use App\Models\Sparepart;
use App\Notifications\LowStockNotification;
use App\Notifications\MissingPriceNotification;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationLogicTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->superadmin = User::factory()->create([
            'role' => UserRole::SUPERADMIN,
        ]);
        
        // Ensure email is verified and password is changed
        $this->superadmin->email_verified_at = now();
        $this->superadmin->password_changed_at = now();
        $this->superadmin->save();
    }

    public function test_low_stock_notification_triggered_on_borrow()
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $sparepart = Sparepart::factory()->create([
            'stock' => 5,
            'minimum_stock' => 3,
            'condition' => 'Baik',
        ]);

        // Admins borrow 3 items, making stock 2 (below minimum 3)
        $response = $this->actingAs($admin)->postJson(route('inventory.borrow.store', $sparepart->id), [
            'quantity' => 3,
            'notes' => 'Keperluan proyek',
            'expected_return_at' => now()->addDays(2)->format('Y-m-d'),
        ]);

        $response->assertStatus(302); // Redirects usually
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'stock' => 2,
        ]);

        // Notification should be sent to superadmin
        Notification::assertSentTo(
            [$this->superadmin],
            LowStockNotification::class,
            function ($notification, $channels) use ($sparepart) {
                return $notification->sparepart->id === $sparepart->id;
            }
        );
    }

    public function test_missing_price_notification_triggered_on_create()
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $data = [
            'name' => 'Barang Baru Tanpa Harga',
            'category' => 'Testing',
            'brand' => 'Merk T',
            'part_number' => 'PN-TEST-999',
            'location' => 'Rak Z',
            'condition' => 'Baik',
            'age' => 'Baru',
            'stock' => 10,
            'minimum_stock' => 2,
            'unit' => 'Pcs',
            'type' => 'sale',
            'price' => 0, // Admin inputs 0 to bypass validation but triggers notification
            'status' => 'aktif'
        ];

        $response = $this->actingAs($admin)->post(route('inventory.store'), $data);
        $response->assertSessionHas('success');

        $sparepart = Sparepart::where('part_number', 'PN-TEST-999')->first();
        $this->assertNotNull($sparepart);

        Notification::assertSentTo(
            [$this->superadmin],
            MissingPriceNotification::class,
            function ($notification, $channels) use ($sparepart) {
                return $notification->sparepart->id === $sparepart->id;
            }
        );
    }
}
