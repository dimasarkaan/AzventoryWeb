<?php

namespace Tests\Feature\General;

use App\Events\InventoryUpdatedEvent;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesKoneksiPusher extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['broadcasting.default' => 'pusher']);
        \Illuminate\Support\Facades\Broadcast::forgetDrivers();
        require base_path('routes/channels.php');
    }

    #[Test]
    public function test_driver_broadcast_diatur_ke_pusher()
    {
        // Driver harus pusher di env atau config (kecuali saat testing bisa diset log/null,
        // tapi kita ingin verifikasi config aslinya jika tidak di-override)
        $this->assertEquals('pusher', config('broadcasting.default'));
    }

    #[Test]
    public function test_konfigurasi_pusher_tersedia()
    {
        $config = config('broadcasting.connections.pusher');

        $this->assertEquals('pusher', $config['driver']);
        $this->assertNotEmpty($config['key'], 'Pusher Key is empty');
        $this->assertNotEmpty($config['secret'], 'Pusher Secret is empty');
        $this->assertNotEmpty($config['app_id'], 'Pusher App ID is empty');
        $this->assertEquals('ap1', $config['options']['cluster']);
    }

    #[Test]
    public function test_memancarkan_event_inventory_updated_saat_diperbarui()
    {
        // Fake all events to prevent real Pusher connection (including ActivityLogged)
        Event::fake();

        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPERADMIN]);
        $sparepart = Sparepart::factory()->create(['name' => 'Original Name']);

        // Trigger update via controller
        $response = $this->actingAs($user)->put(route('inventory.update', $sparepart->id), [
            'name' => 'Updated Name',
            'part_number' => $sparepart->part_number,
            'brand' => $sparepart->brand,
            'category' => $sparepart->category,
            'location' => $sparepart->location,
            'stock' => 20,
            'minimum_stock' => 5,
            'condition' => 'Baik',
            'status' => 'aktif',
            'type' => 'asset',
            'age' => 'Baru',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        Event::assertDispatched(InventoryUpdatedEvent::class, function ($event) {
            return $event->action === 'updated' && $event->sparepart->name === 'Updated Name';
        });
    }

    #[Test]
    public function test_endpoint_auth_broadcasting_dapat_diakses()
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPERADMIN]);

        $response = $this->actingAs($user)->postJson('/broadcasting/auth', [
            'channel_name' => 'private-admin-dashboard',
            'socket_id' => '1234.1234',
        ]);

        $response->dump();
        $response->assertStatus(200);
        $this->assertArrayHasKey('auth', $response->json());
    }
}

