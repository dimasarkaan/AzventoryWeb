<?php

namespace Tests\Feature\General;

use App\Enums\UserRole;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesDebugHalamanTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function debug_route_500_superadmin()
    {
        $superadmin = User::factory()->create([
            'role' => UserRole::SUPERADMIN,
            'password_changed_at' => now(),
        ]);
        Sparepart::factory()->count(3)->create();

        $routes = [
            'dashboard.superadmin',
            'inventory.index',
            'reports.index',
            'reports.activity-logs.index',
            'users.index',
            'profile.edit',
            'notifications.index',
        ];

        foreach ($routes as $routeName) {
            $response = $this->actingAs($superadmin)->get(route($routeName));
            if ($response->getStatusCode() !== 200) {
                $exc = $response->exception;
                $msg = $exc ? get_class($exc).': '.$exc->getMessage() : 'No exception';
                $file = $exc ? ($exc->getFile().':'.$exc->getLine()) : '';
                $this->fail("ROUTE FAILED [{$response->getStatusCode()}]: $routeName\n$msg\n$file");
            }
            $this->assertTrue(true, "Route OK: $routeName");
        }
    }
}
