<?php

namespace Tests\Feature\Roles;

use App\Models\User;
use App\Models\Sparepart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoleTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Buat user admin
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
        
        // Ensure email is verified and password is changed
        $this->admin->email_verified_at = now();
        $this->admin->password_changed_at = now();
        $this->admin->save();
        
        // MOCK image creation and qr code
        \Illuminate\Support\Facades\Storage::fake('public');
        $this->mock(\App\Services\ImageOptimizationService::class, function ($mock) {
            $mock->shouldReceive('optimizeAndSave')->andReturn('dummy/path.webp');
        });
        $this->mock(\App\Services\QrCodeService::class, function ($mock) {
            $mock->shouldReceive('generate')->andReturn('dummy/qrcode.svg');
            $mock->shouldReceive('generateLabelSvg')->andReturn('<svg>...</svg>');
            $mock->shouldReceive('getLabelFilename')->andReturn('dummy_label.svg');
        });
    }

    /** @test */
    public function admin_can_access_admin_dashboard()
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard.admin'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_cannot_access_superadmin_dashboard()
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard.superadmin'));
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_cannot_access_operator_dashboard()
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard.operator'));
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_access_inventory_index()
    {
        $response = $this->actingAs($this->admin)->get(route('inventory.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_stock_approvals()
    {
        $response = $this->actingAs($this->admin)->get(route('inventory.stock-approvals.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_reports()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_activity_logs()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.activity-logs.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_cannot_access_user_management()
    {
        $response = $this->actingAs($this->admin)->get(route('users.index'));
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_create_inventory()
    {
        $data = [
            'name' => 'Barang Baru Admin',
            'category' => 'Testing',
            'brand' => 'Merk XYZ',
            'part_number' => 'PN-ADMIN-123',
            'location' => 'Rak A',
            'condition' => 'Baik',
            'age' => 'Baru',
            'stock' => 10,
            'minimum_stock' => 2,
            'unit' => 'Pcs',
            'type' => 'asset',
            'price' => 10000,
            'status' => 'aktif'
        ];

        $response = $this->actingAs($this->admin)->post(route('inventory.store'), $data);
        
        $response->assertRedirect(route('inventory.index'));
        $this->assertDatabaseHas('spareparts', [
            'name' => 'Barang Baru Admin',
            'part_number' => 'PN-ADMIN-123'
        ]);
    }

    /** @test */
    public function admin_can_update_inventory()
    {
        $sparepart = Sparepart::factory()->create();

        $data = [
            'name' => 'Barang Update Admin',
            'part_number' => $sparepart->part_number,
            'brand' => $sparepart->brand,
            'category' => $sparepart->category,
            'location' => $sparepart->location,
            'condition' => $sparepart->condition,
            'age' => $sparepart->age,
            'type' => $sparepart->type,
            'stock' => $sparepart->stock,
            'minimum_stock' => $sparepart->minimum_stock,
            'unit' => $sparepart->unit,
            'price' => $sparepart->price,
            'status' => $sparepart->status,
        ];

        $response = $this->actingAs($this->admin)->put(route('inventory.update', $sparepart->id), $data);
        
        $response->assertRedirect(route('inventory.index'));
        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'name' => 'Barang Update Admin'
        ]);
    }

    /** @test */
    public function admin_can_soft_delete_inventory()
    {
        $sparepart = Sparepart::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('inventory.destroy', $sparepart->id));
        
        $response->assertRedirect(route('inventory.index'));
        $this->assertSoftDeleted('spareparts', ['id' => $sparepart->id]);
    }
    /** @test */
    public function admin_cannot_restore_inventory()
    {
        $sparepart = Sparepart::factory()->create();
        $sparepart->delete();

        $response = $this->actingAs($this->admin)->patch(route('inventory.restore', $sparepart->id));
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_cannot_force_delete_inventory()
    {
        $sparepart = Sparepart::factory()->create();
        $sparepart->delete();

        $response = $this->actingAs($this->admin)->delete(route('inventory.force-delete', $sparepart->id));
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_cannot_bulk_restore_inventory()
    {
        $sparepart1 = Sparepart::factory()->create();
        $sparepart1->delete();
        $sparepart2 = Sparepart::factory()->create();
        $sparepart2->delete();

        $response = $this->actingAs($this->admin)->post(route('inventory.bulk-restore'), [
            'ids' => [$sparepart1->id, $sparepart2->id]
        ]);
        
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_cannot_bulk_force_delete_inventory()
    {
        $sparepart1 = Sparepart::factory()->create();
        $sparepart1->delete();
        $sparepart2 = Sparepart::factory()->create();
        $sparepart2->delete();

        $response = $this->actingAs($this->admin)->delete(route('inventory.bulk-force-delete'), [
            'ids' => [$sparepart1->id, $sparepart2->id]
        ]);
        
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_request_stock()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 5]);

        $response = $this->actingAs($this->admin)->post(route('inventory.stock.request.store', $sparepart->id), [
            'type' => 'masuk',
            'quantity' => 10,
            'reason' => 'Perlu tambahan restock'
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->admin->id,
            'quantity' => 10,
            'status' => 'approved'
        ]);
    }

    /** @test */
    public function admin_can_borrow_item()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 15, 'condition' => 'Baik']);

        $response = $this->actingAs($this->admin)->post(route('inventory.borrow.store', $sparepart->id), [
            'quantity' => 2,
            'notes' => 'Untuk event',
            'expected_return_at' => now()->addDays(2)->format('Y-m-d')
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('borrowings', [
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->admin->id,
            'quantity' => 2,
            'status' => 'borrowed'
        ]);
    }

    /** @test */
    public function admin_can_access_profile_and_notifications()
    {
        $response = $this->actingAs($this->admin)->get(route('profile.edit'));
        $response->assertStatus(200);

        $response2 = $this->actingAs($this->admin)->get(route('notifications.index'));
        $response2->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_inventory_create()
    {
        $response = $this->actingAs($this->admin)->get(route('inventory.create'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_inventory_show()
    {
        $sparepart = Sparepart::factory()->create();
        $response = $this->actingAs($this->admin)->get(route('inventory.show', $sparepart->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_inventory_edit()
    {
        $sparepart = Sparepart::factory()->create();
        $response = $this->actingAs($this->admin)->get(route('inventory.edit', $sparepart->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_scan_qr()
    {
        $response = $this->actingAs($this->admin)->get(route('inventory.scan-qr'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_check_part_number()
    {
        $sparepart = Sparepart::factory()->create(['part_number' => 'PN-TEST-123']);
        $response = $this->actingAs($this->admin)->get(route('inventory.check-part-number', ['part_number' => 'PN-TEST-123']));
        $response->assertStatus(200);
        $response->assertJson(['exists' => true]);
    }

    /** @test */
    public function admin_can_download_qr_code()
    {
        $sparepart = Sparepart::factory()->create();
        $response = $this->actingAs($this->admin)->get(route('inventory.qr.download', $sparepart->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_print_qr_code()
    {
        $sparepart = Sparepart::factory()->create(['qr_code_path' => 'dummy/qrcode.svg']);
        $response = $this->actingAs($this->admin)->get(route('inventory.qr.print', $sparepart->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_borrow_history()
    {
        $sparepart = Sparepart::factory()->create();
        $borrowing = \App\Models\Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->admin->id,
            'borrower_name' => $this->admin->name,
            'quantity' => 1,
            'status' => 'borrowed',
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(2)
        ]);
        $response = $this->actingAs($this->admin)->get(route('inventory.borrow.history', $borrowing->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_borrow_show()
    {
        $sparepart = Sparepart::factory()->create();
        $borrowing = \App\Models\Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->admin->id,
            'borrower_name' => $this->admin->name,
            'quantity' => 1,
            'status' => 'borrowed',
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(2)
        ]);
        $response = $this->actingAs($this->admin)->get(route('inventory.borrow.show', $borrowing->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_return_borrowed_item()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 5]);
        $borrowing = clone \App\Models\Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->admin->id,
            'borrower_name' => $this->admin->name,
            'quantity' => 2,
            'status' => 'borrowed',
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(2)
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->image('bukti_pengembalian.jpg');

        $response = $this->actingAs($this->admin)->post(route('inventory.borrow.return', $borrowing->id), [
            'return_condition' => 'good',
            'return_notes' => 'Dikembalikan dengan aman',
            'return_quantity' => 2,
            'return_photos' => [$file]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('borrowings', [
            'id' => $borrowing->id,
            'status' => 'returned'
        ]);
    }

    /** @test */
    public function admin_can_download_reports()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.download'));
        $response->assertRedirect();
    }

    /** @test */
    public function admin_can_access_movement_data()
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard.admin.movement-data', ['period' => 7]));
        $response->assertStatus(200);
    }
}
