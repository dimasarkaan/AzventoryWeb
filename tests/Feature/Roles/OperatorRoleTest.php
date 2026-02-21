<?php

namespace Tests\Feature\Roles;

use App\Models\User;
use App\Models\Sparepart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperatorRoleTest extends TestCase
{
    use RefreshDatabase;

    protected $operator;

    protected function setUp(): void
    {
        parent::setUp();
        // Buat user operator
        $this->operator = User::factory()->create([
            'role' => 'operator',
        ]);
        
        // Ensure email is verified and password is changed
        $this->operator->email_verified_at = now();
        $this->operator->password_changed_at = now();
        $this->operator->save();
        
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
    public function operator_can_access_operator_dashboard()
    {
        $response = $this->actingAs($this->operator)->get(route('dashboard.operator'));
        $response->assertStatus(200);
    }

    /** @test */
    public function operator_cannot_access_superadmin_dashboard()
    {
        $response = $this->actingAs($this->operator)->get(route('dashboard.superadmin'));
        $response->assertStatus(403);
    }

    /** @test */
    public function operator_cannot_access_admin_dashboard()
    {
        $response = $this->actingAs($this->operator)->get(route('dashboard.admin'));
        $response->assertStatus(403);
    }

    /** @test */
    public function operator_can_access_inventory_index()
    {
        $response = $this->actingAs($this->operator)->get(route('inventory.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function operator_cannot_access_stock_approvals()
    {
        $response = $this->actingAs($this->operator)->get(route('inventory.stock-approvals.index'));
        $response->assertStatus(403);
    }

    /** @test */
    public function operator_cannot_access_reports_index()
    {
        $response = $this->actingAs($this->operator)->get(route('reports.index'));
        $response->assertStatus(403);
    }

    /** @test */
    public function operator_can_access_activity_logs()
    {
        $response = $this->actingAs($this->operator)->get(route('reports.activity-logs.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function operator_cannot_access_user_management()
    {
        $response = $this->actingAs($this->operator)->get(route('users.index'));
        $response->assertStatus(403);
    }

    /** @test */
    public function operator_cannot_create_inventory()
    {
        $data = [
            'name' => 'Barang Baru Operator',
            'category' => 'Testing',
            'brand' => 'Merk XYZ',
            'part_number' => 'PN-OP-123',
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

        $response = $this->actingAs($this->operator)->post(route('inventory.store'), $data);
        $response->assertStatus(403);
    }

    /** @test */
    public function operator_cannot_update_inventory()
    {
        $sparepart = Sparepart::factory()->create();

        $data = [
            'name' => 'Barang Update Operator',
            'part_number' => $sparepart->part_number,
        ];

        $response = $this->actingAs($this->operator)->put(route('inventory.update', $sparepart->id), $data);
        $response->assertStatus(403);
    }

    /** @test */
    public function operator_cannot_soft_delete_inventory()
    {
        $sparepart = Sparepart::factory()->create();

        $response = $this->actingAs($this->operator)->delete(route('inventory.destroy', $sparepart->id));
        $response->assertStatus(403);
    }

    /** @test */
    public function operator_cannot_restore_inventory()
    {
        $sparepart = Sparepart::factory()->create();
        $sparepart->delete();

        $response = $this->actingAs($this->operator)->patch(route('inventory.restore', $sparepart->id));
        $response->assertStatus(403);
    }

    /** @test */
    public function operator_cannot_force_delete_inventory()
    {
        $sparepart = Sparepart::factory()->create();
        $sparepart->delete();

        $response = $this->actingAs($this->operator)->delete(route('inventory.force-delete', $sparepart->id));
        $response->assertStatus(403);
    }

    /** @test */
    public function operator_cannot_bulk_restore_inventory()
    {
        $sparepart1 = Sparepart::factory()->create();
        $sparepart1->delete();
        $sparepart2 = Sparepart::factory()->create();
        $sparepart2->delete();

        $response = $this->actingAs($this->operator)->post(route('inventory.bulk-restore'), [
            'ids' => [$sparepart1->id, $sparepart2->id]
        ]);
        
        $response->assertStatus(403);
    }

    /** @test */
    public function operator_cannot_bulk_force_delete_inventory()
    {
        $sparepart1 = Sparepart::factory()->create();
        $sparepart1->delete();
        $sparepart2 = Sparepart::factory()->create();
        $sparepart2->delete();

        $response = $this->actingAs($this->operator)->delete(route('inventory.bulk-force-delete'), [
            'ids' => [$sparepart1->id, $sparepart2->id]
        ]);
        
        $response->assertStatus(403);
    }

    /** @test */
    public function operator_can_request_stock()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 5]);

        $response = $this->actingAs($this->operator)->post(route('inventory.stock.request.store', $sparepart->id), [
            'type' => 'masuk',
            'quantity' => 10,
            'reason' => 'Perlu tambahan restock'
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator->id,
            'quantity' => 10,
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function operator_can_borrow_item()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 15, 'condition' => 'Baik']);

        $response = $this->actingAs($this->operator)->post(route('inventory.borrow.store', $sparepart->id), [
            'quantity' => 2,
            'notes' => 'Untuk event',
            'expected_return_at' => now()->addDays(2)->format('Y-m-d')
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('borrowings', [
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator->id,
            'quantity' => 2,
            'status' => 'borrowed'
        ]);
    }

    /** @test */
    public function operator_can_access_profile_and_notifications()
    {
        $response = $this->actingAs($this->operator)->get(route('profile.edit'));
        $response->assertStatus(200);

        $response2 = $this->actingAs($this->operator)->get(route('notifications.index'));
        $response2->assertStatus(200);
    }

    /** @test */
    public function operator_cannot_access_inventory_create()
    {
        $response = $this->actingAs($this->operator)->get(route('inventory.create'));
        $response->assertStatus(403);
    }

    /** @test */
    public function operator_can_access_inventory_show()
    {
        $sparepart = Sparepart::factory()->create();
        $response = $this->actingAs($this->operator)->get(route('inventory.show', $sparepart->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function operator_cannot_access_inventory_edit()
    {
        $sparepart = Sparepart::factory()->create();
        $response = $this->actingAs($this->operator)->get(route('inventory.edit', $sparepart->id));
        $response->assertStatus(403);
    }

    /** @test */
    public function operator_can_access_scan_qr()
    {
        $response = $this->actingAs($this->operator)->get(route('inventory.scan-qr'));
        $response->assertStatus(200);
    }

    /** @test */
    public function operator_can_check_part_number()
    {
        $sparepart = Sparepart::factory()->create(['part_number' => 'PN-TEST-123']);
        $response = $this->actingAs($this->operator)->get(route('inventory.check-part-number', ['part_number' => 'PN-TEST-123']));
        $response->assertStatus(200);
        $response->assertJson(['exists' => true]);
    }

    /** @test */
    public function operator_can_download_qr_code()
    {
        $sparepart = Sparepart::factory()->create();
        $response = $this->actingAs($this->operator)->get(route('inventory.qr.download', $sparepart->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function operator_can_print_qr_code()
    {
        $sparepart = Sparepart::factory()->create(['qr_code_path' => 'dummy/qrcode.svg']);
        $response = $this->actingAs($this->operator)->get(route('inventory.qr.print', $sparepart->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function operator_can_access_borrow_history()
    {
        $sparepart = Sparepart::factory()->create();
        $borrowing = \App\Models\Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator->id,
            'borrower_name' => $this->operator->name,
            'quantity' => 1,
            'status' => 'borrowed',
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(2)
        ]);
        $response = $this->actingAs($this->operator)->get(route('inventory.borrow.history', $borrowing->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function operator_can_access_borrow_show()
    {
        $sparepart = Sparepart::factory()->create();
        $borrowing = \App\Models\Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator->id,
            'borrower_name' => $this->operator->name,
            'quantity' => 1,
            'status' => 'borrowed',
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(2)
        ]);
        $response = $this->actingAs($this->operator)->get(route('inventory.borrow.show', $borrowing->id));
        $response->assertStatus(200);
    }

    /** @test */
    public function operator_can_return_borrowed_item()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 5]);
        $borrowing = clone \App\Models\Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator->id,
            'borrower_name' => $this->operator->name,
            'quantity' => 2,
            'status' => 'borrowed',
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(2)
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->image('bukti_pengembalian.jpg');

        $response = $this->actingAs($this->operator)->post(route('inventory.borrow.return', $borrowing->id), [
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
    public function operator_cannot_download_reports()
    {
        $response = $this->actingAs($this->operator)->get(route('reports.download'));
        $response->assertStatus(403);
    }

}
