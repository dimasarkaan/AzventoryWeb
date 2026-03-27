<?php

namespace Tests\Feature\Notifications;

use App\Models\Sparepart;
use App\Models\User;
use App\Notifications\ApproachingStockNotification;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TesLowStock extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an admin user to receive notifications
        User::factory()->create(['role' => \App\Enums\UserRole::ADMIN]);
    }

    public function test_approaching_stock_notification_is_sent_when_stock_is_between_100_and_150_percent()
    {
        Notification::fake();

        $service = app(InventoryService::class);
        $sparepart = Sparepart::factory()->create([
            'stock' => 15, // Currently 15
            'minimum_stock' => 10,
        ]);

        // Simulasikan pembaruan stok menjadi 12 (120% dari 10, yaitu antara 11-15)
        $service->updateSparepart($sparepart, [
            'stock' => 12,
        ]);

        // Pastikan ApproachingStockNotification dikirim
        $admins = User::whereIn('role', [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])->get();
        Notification::assertSentTo(
            $admins,
            ApproachingStockNotification::class,
            function ($notification, $channels) use ($sparepart) {
                return $notification->sparepart->id === $sparepart->id;
            }
        );
    }

    public function test_approaching_notification_not_sent_if_stock_still_above_150_percent()
    {
        Notification::fake();

        $service = app(InventoryService::class);
        $sparepart = Sparepart::factory()->create([
            'stock' => 20, // Currently 20
            'minimum_stock' => 10,
        ]);

        // Update stok menjadi 16 (160% dari 10, lebih dari 150%)
        $service->updateSparepart($sparepart, [
            'stock' => 16,
        ]);

        $admins = User::whereIn('role', [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])->get();
        Notification::assertNotSentTo(
            $admins,
            ApproachingStockNotification::class
        );
    }
}

