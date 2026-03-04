<?php

namespace Tests\Feature\Roles;

use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function admin_dapat_mengakses_dashboard_admin()
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard.admin'));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_tidak_dapat_mengakses_dashboard_superadmin()
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard.superadmin'));
        $response->assertStatus(403);
    }

    #[Test]
    public function admin_dapat_mengakses_dashboard_operator()
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard.operator'));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_mengakses_daftar_inventaris()
    {
        $response = $this->actingAs($this->admin)->get(route('inventory.index'));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_mengakses_persetujuan_stok()
    {
        $response = $this->actingAs($this->admin)->get(route('inventory.stock-approvals.index'));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_mengakses_laporan()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.index'));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_mengakses_log_aktivitas()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.activity-logs.index'));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_tidak_dapat_mengakses_manajemen_user()
    {
        $response = $this->actingAs($this->admin)->get(route('users.index'));
        $response->assertStatus(403);
    }

    #[Test]
    public function admin_dapat_membuat_inventaris()
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
            'status' => 'aktif',
        ];

        $response = $this->actingAs($this->admin)->post(route('inventory.store'), $data);

        $response->assertRedirect(route('inventory.index'));
        $this->assertDatabaseHas('spareparts', [
            'name' => 'Barang Baru Admin',
            'part_number' => 'PN-ADMIN-123',
        ]);
    }

    #[Test]
    public function admin_dapat_memperbarui_inventaris()
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
            'name' => 'Barang Update Admin',
        ]);
    }

    #[Test]
    public function admin_dapat_menghapus_lunak_inventaris()
    {
        $sparepart = Sparepart::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('inventory.destroy', $sparepart->id));

        $response->assertRedirect(route('inventory.index'));
        $this->assertSoftDeleted('spareparts', ['id' => $sparepart->id]);
    }

    #[Test]
    public function admin_tidak_dapat_memulihkan_inventaris()
    {
        $sparepart = Sparepart::factory()->create();
        $sparepart->delete();

        $response = $this->actingAs($this->admin)->patch(route('inventory.restore', $sparepart->id));
        $response->assertStatus(403);
    }

    #[Test]
    public function admin_tidak_dapat_menghapus_permanen_inventaris()
    {
        $sparepart = Sparepart::factory()->create();
        $sparepart->delete();

        $response = $this->actingAs($this->admin)->delete(route('inventory.force-delete', $sparepart->id));
        $response->assertStatus(403);
    }

    #[Test]
    public function admin_tidak_dapat_memulihkan_inventaris_secara_massal()
    {
        $sparepart1 = Sparepart::factory()->create();
        $sparepart1->delete();
        $sparepart2 = Sparepart::factory()->create();
        $sparepart2->delete();

        $response = $this->actingAs($this->admin)->post(route('inventory.bulk-restore'), [
            'ids' => [$sparepart1->id, $sparepart2->id],
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_tidak_dapat_menghapus_permanen_inventaris_secara_massal()
    {
        $sparepart1 = Sparepart::factory()->create();
        $sparepart1->delete();
        $sparepart2 = Sparepart::factory()->create();
        $sparepart2->delete();

        $response = $this->actingAs($this->admin)->delete(route('inventory.bulk-force-delete'), [
            'ids' => [$sparepart1->id, $sparepart2->id],
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_dapat_meminta_stok()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 5]);

        $response = $this->actingAs($this->admin)->post(route('inventory.stock.request.store', $sparepart->id), [
            'type' => 'masuk',
            'quantity' => 10,
            'reason' => 'Perlu tambahan restock',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('stock_logs', [
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->admin->id,
            'quantity' => 10,
            'status' => 'approved',
        ]);
    }

    #[Test]
    public function admin_dapat_meminjam_item()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 15, 'condition' => 'Baik']);

        $response = $this->actingAs($this->admin)->post(route('inventory.borrow.store', $sparepart->id), [
            'quantity' => 2,
            'notes' => 'Untuk event',
            'expected_return_at' => now()->addDays(2)->format('Y-m-d'),
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('borrowings', [
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->admin->id,
            'quantity' => 2,
            'status' => 'borrowed',
        ]);
    }

    #[Test]
    public function admin_dapat_mengakses_profil_dan_notifikasi()
    {
        $response = $this->actingAs($this->admin)->get(route('profile.edit'));
        $response->assertStatus(200);

        $response2 = $this->actingAs($this->admin)->get(route('notifications.index'));
        $response2->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_mengakses_tambah_inventaris()
    {
        $response = $this->actingAs($this->admin)->get(route('inventory.create'));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_mengakses_detail_inventaris()
    {
        $sparepart = Sparepart::factory()->create();
        $response = $this->actingAs($this->admin)->get(route('inventory.show', $sparepart->id));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_mengakses_edit_inventaris()
    {
        $sparepart = Sparepart::factory()->create();
        $response = $this->actingAs($this->admin)->get(route('inventory.edit', $sparepart->id));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_mengakses_scan_qr()
    {
        $response = $this->actingAs($this->admin)->get(route('inventory.scan-qr'));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_memeriksa_nomor_part()
    {
        $sparepart = Sparepart::factory()->create(['part_number' => 'PN-TEST-123']);
        $response = $this->actingAs($this->admin)->get(route('inventory.check-part-number', ['part_number' => 'PN-TEST-123']));
        $response->assertStatus(200);
        $response->assertJson(['exists' => true]);
    }

    #[Test]
    public function admin_dapat_mengunduh_qr_code()
    {
        $sparepart = Sparepart::factory()->create();
        $response = $this->actingAs($this->admin)->get(route('inventory.qr.download', $sparepart->id));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_mencetak_qr_code()
    {
        $sparepart = Sparepart::factory()->create(['qr_code_path' => 'dummy/qrcode.svg']);
        $response = $this->actingAs($this->admin)->get(route('inventory.qr.print', $sparepart->id));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_mengakses_riwayat_peminjaman()
    {
        $sparepart = Sparepart::factory()->create();
        $borrowing = \App\Models\Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->admin->id,
            'borrower_name' => $this->admin->name,
            'quantity' => 1,
            'status' => 'borrowed',
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(2),
        ]);
        $response = $this->actingAs($this->admin)->get(route('inventory.borrow.history', $borrowing->id));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_mengakses_detail_peminjaman()
    {
        $sparepart = Sparepart::factory()->create();
        $borrowing = \App\Models\Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->admin->id,
            'borrower_name' => $this->admin->name,
            'quantity' => 1,
            'status' => 'borrowed',
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(2),
        ]);
        $response = $this->actingAs($this->admin)->get(route('inventory.borrow.show', $borrowing->id));
        $response->assertStatus(200);
    }

    #[Test]
    public function admin_dapat_mengembalikan_item_pinjaman()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 5]);
        $borrowing = clone \App\Models\Borrowing::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->admin->id,
            'borrower_name' => $this->admin->name,
            'quantity' => 2,
            'status' => 'borrowed',
            'borrowed_at' => now(),
            'expected_return_at' => now()->addDays(2),
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->image('bukti_pengembalian.jpg');

        $response = $this->actingAs($this->admin)->post(route('inventory.borrow.return', $borrowing->id), [
            'return_condition' => 'good',
            'return_notes' => 'Dikembalikan dengan aman',
            'return_quantity' => 2,
            'return_photos' => [$file],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('borrowings', [
            'id' => $borrowing->id,
            'status' => 'returned',
        ]);
    }

    #[Test]
    public function admin_dapat_mengunduh_laporan()
    {
        $response = $this->actingAs($this->admin)->get(route('reports.download'));
        $response->assertRedirect();
    }

    #[Test]
    public function admin_dapat_mengakses_data_pergerakan()
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard.admin.movement-data', ['period' => 7]));
        $response->assertStatus(200);
    }
}
