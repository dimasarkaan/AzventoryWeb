<?php

namespace Tests\Unit\Inventory;

use App\Models\Sparepart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TesUuidDebugTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function debug_uuid_generation()
    {
        $sparepart = Sparepart::factory()->create(['part_number' => 'UUID-DEBUG-001']);

        $this->assertNotNull($sparepart->uuid, 'UUID is null after create!');
        $this->assertNotNull($sparepart->getRouteKey(), 'Route key is null!');

        // Refresh from DB
        $fresh = $sparepart->fresh();
        $this->assertNotNull($fresh->uuid, 'UUID is null after fresh()!');
    }
}
