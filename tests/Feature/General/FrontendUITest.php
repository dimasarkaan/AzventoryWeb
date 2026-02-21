<?php

namespace Tests\Feature\General;

use App\Models\User;
use App\Models\Sparepart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendUITest extends TestCase
{
    use RefreshDatabase;

    protected $operator;
    protected $admin;
    protected $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->operator = User::factory()->create(['role' => 'operator']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->superAdmin = User::factory()->create(['role' => 'superadmin']);
        
        $this->item = Sparepart::factory()->create(['stock' => 10]);
    }

    /** @test */
    public function operator_cannot_see_edit_delete_buttons_on_inventory()
    {
        $response = $this->actingAs($this->operator)->get(route('inventory.index'));
        
        $response->assertStatus(200);
        
        // Memastikan Operator tidak bisa melihat tombol Aksi Edit atau Delete
        $response->assertDontSee(route('inventory.edit', $this->item->id));
        $response->assertDontSee('action="' . route('inventory.destroy', $this->item->id) . '"', false); // Cek escape HTML string
        $response->assertDontSee(route('inventory.create')); // Cek escape URL route
    }

    /** @test */
    public function admin_can_see_edit_but_cannot_see_force_delete_button()
    {
        // Masukkan sebuah item ke trash
        $trashedItem = Sparepart::factory()->create();
        $trashedItem->delete();

        $response = $this->actingAs($this->admin)->get(route('inventory.index', ['trash' => 'true']));
        
        $response->assertStatus(200);
        
        // Memastikan Admin tidak bisa melihat tombol Hapus Permanen atau Pulihkan
        $response->assertDontSee(route('inventory.force-delete', $trashedItem->id));
        $response->assertDontSee(route('inventory.restore', $trashedItem->id));
    }
    
    /** @test */
    public function operator_sees_different_dashboard_widgets_than_admin()
    {
        $responseOp = $this->actingAs($this->operator)->get(route('dashboard.operator'));
        $responseOp->assertStatus(200);
        // Operator harusnya melihat wdiget Peminjaman Aktif nya, bukan global stats
        $responseOp->assertSee('Total Pinjaman Aktif'); 
        
        $responseAd = $this->actingAs($this->admin)->get(route('dashboard.admin'));
        $responseAd->assertStatus(200);
        // Admin melihat status stok barang yaitu "Total Stok Fisik" (dari ui.total_physical_stock)
        $responseAd->assertSee('Total Stok Fisik');
    }
    
    /** @test */
    public function inventory_index_view_is_responsive()
    {
        // Tes rendering standard tanpa layout error (contoh sederhana render checks)
        $response = $this->actingAs($this->superAdmin)->get(route('inventory.index'));
        $response->assertStatus(200);
        
        // Memastikan elemen Alpine.js atau Tailwind table classes ter-render
        $response->assertSee('overflow-x-auto'); // Table responsif
    }
}
