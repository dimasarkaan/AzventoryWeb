<?php

namespace Tests\Feature\Inventory;

use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TesInputJumlah extends TestCase
{
    use RefreshDatabase;

    public function test_input_stok_halaman_tambah_defaultnya_kosong()
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get(route('inventory.create'));

        $response->assertStatus(200);

        // Assert Stock input does NOT have value="0"
        $response->assertDontSee('name="stock" value="0"', false);

        // Assert Stock input has empty value (or just no value attribute if that's how Laravel renders null)
        // Laravel's old('stock') returns null, so blade renders value=""
        $response->assertSee('name="stock" value=""', false);
    }

    public function test_halaman_show_alpine_menginisialisasi_input_ke_kosong()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $sparepart = Sparepart::factory()->create();

        $response = $this->actingAs($user)->get(route('inventory.show', $sparepart));

        $response->assertStatus(200);

        // Check Alpine.js data initialization in alpine_script (included in show)
        // returnQty should be ''
        $response->assertSee("returnQty: ''", false);

        // Check Borrow Modal (in show.blade.php)
        // x-data="{ quantity: '', ... }"
        $response->assertSee("quantity: ''", false);
    }
}
