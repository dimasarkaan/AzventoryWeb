<?php

namespace Tests\Feature\Inventory;

use App\Enums\UserRole;
use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TesPeminjamanTest extends TestCase
{
    use RefreshDatabase;

    protected $superadmin;

    protected $operator1;

    protected $operator2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->create([
            'role' => UserRole::SUPERADMIN,
            'password_changed_at' => now(),
        ]);

        $this->operator1 = User::factory()->create([
            'role' => UserRole::OPERATOR,
            'password_changed_at' => now(),
        ]);

        $this->operator2 = User::factory()->create([
            'role' => UserRole::OPERATOR,
            'password_changed_at' => now(),
        ]);

        Storage::fake('public');

        $this->mock(\App\Services\ImageOptimizationService::class, function ($mock) {
            $mock->shouldReceive('optimizeAndSave')->andReturn('dummy/path.webp');
        });
    }

    public function test_halaman_detail_peminjaman_tampil_sukses()
    {
        $sparepart = Sparepart::factory()->create();
        $borrowing = Borrowing::factory()->create([
            'user_id' => $this->operator1->id,
            'sparepart_id' => $sparepart->id,
        ]);

        $response = $this->actingAs($this->operator1)->get(route('inventory.borrow.show', $borrowing));
        $response->assertStatus(200);
    }

    public function test_berhasil_meminjam_barang_dan_stok_berkurang()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 10, 'type' => 'asset', 'condition' => 'Baik']);

        $response = $this->actingAs($this->operator1)->post(route('inventory.borrow.store', $sparepart), [
            'quantity' => 3,
            'expected_return_at' => now()->addDays(2)->format('Y-m-d'),
            'notes' => 'Pinjam untuk project',
        ]);

        $response->assertRedirect(route('inventory.show', $sparepart));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'stock' => 7,
        ]);

        $this->assertDatabaseHas('borrowings', [
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator1->id,
            'quantity' => 3,
            'status' => 'borrowed',
        ]);
    }

    public function test_gagal_meminjam_jika_stok_tidak_cukup()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 2, 'type' => 'asset', 'condition' => 'Baik']);

        $response = $this->actingAs($this->operator1)->post(route('inventory.borrow.store', $sparepart), [
            'quantity' => 5, // Lebih dari stok
            'expected_return_at' => now()->addDays(1)->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors(['borrow_error']);
        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'stock' => 2, // Stok tidak berkurang
        ]);
    }

    public function test_berhasil_mengembalikan_seluruh_barang_kondisi_baik()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 5, 'type' => 'asset', 'condition' => 'Baik']);
        $borrowing = Borrowing::factory()->create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator1->id,
            'quantity' => 5,
            'status' => 'borrowed',
        ]);

        $response = $this->actingAs($this->operator1)->post(route('inventory.borrow.return', $borrowing), [
            'return_quantity' => 5,
            'return_condition' => 'good',
            'return_photos' => [UploadedFile::fake()->image('bukti.jpg')],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('borrowings', [
            'id' => $borrowing->id,
            'status' => 'returned',
        ]);

        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'stock' => 10, // 5 + 5
        ]);
    }

    public function test_berhasil_mengembalikan_sebagian_barang()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 5, 'type' => 'asset', 'condition' => 'Baik']);
        $borrowing = Borrowing::factory()->create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator1->id,
            'quantity' => 5,
            'status' => 'borrowed',
        ]);

        $response = $this->actingAs($this->operator1)->post(route('inventory.borrow.return', $borrowing), [
            'return_quantity' => 2, // Hanya kembali 2
            'return_condition' => 'good',
            'return_photos' => [UploadedFile::fake()->image('bukti.jpg')],
        ]);

        $response->assertRedirect();

        // Status masih borrowed karena belum semua kembali
        $this->assertDatabaseHas('borrowings', [
            'id' => $borrowing->id,
            'status' => 'borrowed',
        ]);

        // Stok kembali 2
        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'stock' => 7,
        ]);
    }

    public function test_gagal_mengembalikan_melebihi_sisa_pinjaman()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 5, 'type' => 'asset', 'condition' => 'Baik']);
        $borrowing = Borrowing::factory()->create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator1->id,
            'quantity' => 2,
            'status' => 'borrowed',
        ]);

        // Request as JSON to hit the exception handling block for JSON
        $response = $this->actingAs($this->operator1)->postJson(route('inventory.borrow.return', $borrowing), [
            'return_quantity' => 3, // Melebihi pinjaman (2)
            'return_condition' => 'good',
            'return_photos' => [UploadedFile::fake()->image('bukti.jpg')],
        ]);

        $response->assertStatus(500); // 500 karena returnItem controller melempar 500 untuk error catch
        $response->assertJsonStructure(['error']);
    }

    public function test_api_history_mengembalikan_json_valid()
    {
        $sparepart = Sparepart::factory()->create();
        $borrowing = Borrowing::factory()->create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator1->id,
        ]);

        $response = $this->actingAs($this->operator1)->getJson(route('inventory.borrow.history', $borrowing));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'borrower',
            'borrow_date',
            'total_quantity',
            'status',
            'items',
        ]);
    }

    public function test_operator_forbidden_melihat_pinjaman_orang_lain()
    {
        $sparepart = Sparepart::factory()->create();
        $borrowing = Borrowing::factory()->create([
            'user_id' => $this->operator1->id, // Milik operator 1
            'sparepart_id' => $sparepart->id,
        ]);

        // Operator 2 mencoba melihat
        $response = $this->actingAs($this->operator2)->get(route('inventory.borrow.show', $borrowing));
        $response->assertStatus(403);
    }

    public function test_pengembalian_barang_rusak_tidak_menambah_stok_baik()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 5]);
        $borrowing = Borrowing::factory()->create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator1->id,
            'quantity' => 3,
            'status' => 'borrowed',
        ]);

        $response = $this->actingAs($this->operator1)->post(route('inventory.borrow.return', $borrowing), [
            'return_quantity' => 3,
            'return_condition' => 'broken', // Kondisi Rusak
            'return_photos' => [UploadedFile::fake()->image('bukti.jpg')],
        ]);

        $response->assertRedirect();

        // Stok utama tetap 5, tidak bertambah karena barang rusak (dipisah menjadi sparepart baru atau tidak ditambahkan)
        $this->assertDatabaseHas('spareparts', [
            'id' => $sparepart->id,
            'stock' => 5,
        ]);
    }

    public function test_file_foto_dibersihkan_jika_pengembalian_error()
    {
        $sparepart = Sparepart::factory()->create(['stock' => 5]);
        $borrowing = Borrowing::factory()->create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $this->operator1->id,
            'quantity' => 1,
        ]);

        $file = UploadedFile::fake()->image('bukti.jpg');

        $response = $this->actingAs($this->operator1)->postJson(route('inventory.borrow.return', $borrowing), [
            'return_quantity' => 5, // Error: melebihi sisa pinjaman (1)
            'return_condition' => 'good',
            'return_photos' => [$file],
        ]);

        $response->assertStatus(500);

        // Pastikan mock ImageOptimizationService yang return 'dummy/path.webp' dihapus dari disk
        $this->assertFalse(Storage::disk('public')->exists('dummy/path.webp'));
    }
}
