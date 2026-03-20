<?php

namespace Tests\Feature\Notifications;

use App\Enums\UserRole;
use App\Models\Sparepart;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test untuk NotificationController.
 * Memastikan operasi notifikasi (lihat, tandai baca) berfungsi dengan benar.
 */
class TesKontrollerNotifikasi extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => UserRole::OPERATOR]);
        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
    }

    #[Test]
    public function halaman_notifikasi_dapat_diakses_oleh_user_yang_login()
    {
        $response = $this->actingAs($this->user)->get(route('notifications.index'));
        $response->assertOk();
    }

    #[Test]
    public function halaman_notifikasi_tidak_dapat_diakses_tanpa_login()
    {
        $response = $this->get(route('notifications.index'));
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function api_notifikasi_mengembalikan_json_untuk_request_json()
    {
        $sparepart = Sparepart::factory()->create();
        $this->user->notify(new LowStockNotification($sparepart));

        $response = $this->actingAs($this->user)
            ->getJson(route('notifications.index'));

        $response->assertOk()
            ->assertJsonStructure([
                '*' => ['id', 'type', 'data'],
            ]);
    }

    #[Test]
    public function pengguna_dapat_menandai_satu_notifikasi_sebagai_dibaca()
    {
        $sparepart = Sparepart::factory()->create();
        $this->user->notify(new LowStockNotification($sparepart));

        $notification = $this->user->unreadNotifications()->first();
        $this->assertNotNull($notification);

        $this->actingAs($this->user)
            ->patch(route('notifications.read', $notification->id))
            ->assertRedirect();

        $this->assertNotNull($this->user->notifications()->find($notification->id)->read_at);
    }

    #[Test]
    public function pengguna_dapat_menandai_semua_notifikasi_sebagai_dibaca()
    {
        $spareparts = Sparepart::factory()->count(3)->create();
        foreach ($spareparts as $sp) {
            $this->user->notify(new LowStockNotification($sp));
        }

        $this->assertEquals(3, $this->user->unreadNotifications()->count());

        $this->actingAs($this->user)
            ->patch(route('notifications.markAllRead'))
            ->assertRedirect();

        $this->assertEquals(0, $this->user->unreadNotifications()->count());
    }

    #[Test]
    public function api_mark_all_read_mengembalikan_json_sukses()
    {
        $sparepart = Sparepart::factory()->create();
        $this->user->notify(new LowStockNotification($sparepart));

        $response = $this->actingAs($this->user)
            ->patchJson(route('notifications.markAllRead'));

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertEquals(0, $this->user->fresh()->unreadNotifications()->count());
    }

    #[Test]
    public function pengguna_tidak_dapat_menandai_notifikasi_milik_pengguna_lain()
    {
        $sparepart = Sparepart::factory()->create();
        $this->admin->notify(new LowStockNotification($sparepart));

        $adminNotification = $this->admin->unreadNotifications()->first();

        // User biasa mencoba menandai notifikasi milik admin
        $response = $this->actingAs($this->user)
            ->patch(route('notifications.read', $adminNotification->id));

        $response->assertStatus(404);
    }
}

