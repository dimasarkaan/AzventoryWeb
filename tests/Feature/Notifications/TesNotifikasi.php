<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesNotifikasi extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->create(['role' => 'superadmin']);
    }

    #[Test]
    public function superadmin_dapat_melihat_notifikasi()
    {
        // Seed Notifikasi
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

    #[Test]
    public function superadmin_dapat_menandai_notifikasi_sebagai_dibaca()
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

    #[Test]
    public function test_notifikasi_stok_rendah_dapat_dipicu()
    {
        \Illuminate\Support\Facades\Notification::fake();

        // Buat sparepart dengan stok rendah
        $sparepart = \App\Models\Sparepart::factory()->make([
            'name' => 'Low Stock Item',
            'stock' => 1,
            'minimum_stock' => 5,
        ]);

        // Picu notifikasi secara manual (simulasi logika Controller)
        $this->superAdmin->notify(new \App\Notifications\LowStockNotification($sparepart));

        // Pastikan notifikasi dikirim ke user
        \Illuminate\Support\Facades\Notification::assertSentTo(
            [$this->superAdmin],
            \App\Notifications\LowStockNotification::class
        );
    }
}
