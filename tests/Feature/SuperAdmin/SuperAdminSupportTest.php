<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class SuperAdminSupportTest extends TestCase
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

    public function test_superadmin_can_access_reports_page()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.reports.index'));
        $response->assertStatus(200);
    }

    public function test_superadmin_can_access_qr_scan_page()
    {
        $response = $this->actingAs($this->superadmin)->get(route('superadmin.scan-qr'));
        $response->assertStatus(200);
    }
}
