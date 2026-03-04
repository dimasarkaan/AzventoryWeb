<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RealTimeBrowserTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_realtime_stock_update_notification_di_halaman_show()
    {
        $this->browse(function (Browser $operatorBrowser, Browser $adminBrowser) {
            
            // 1. Persiapkan data uji dari Factory
            $operator = \App\Models\User::factory()->create(['role' => 'operator']);
            $admin = \App\Models\User::factory()->create(['role' => 'admin']);
            $sparepart = \App\Models\Sparepart::factory()->create(['stock' => 10, 'minimum_stock' => 5]);

            // 2. Operator Login dan buka halaman detail sparepart
            $operatorBrowser->loginAs($operator)
                    ->visitRoute('inventory.show', $sparepart->id)
                    ->waitForText($sparepart->name)
                    ->assertSee('STOK TERSEDIA')
                    ->assertSee('10'); // Verifikasi stok awal

            // 3. Admin Login dan proses penambahan stok via HTTP backend-only untuk memicu event
            $adminBrowser->loginAs($admin)
                         ->visitRoute('inventory.adjust-stock', $sparepart->id)
                         ->type('quantity', 15) // Tambah 15 stok = 25 total
                         ->select('type', 'masuk')
                         ->type('reason', 'Penambahan stok dari admin via Dusk')
                         ->press('Simpan Penyesuaian')
                         ->waitForText('Stok berhasil diperbarui'); // Pastikan berhasil

            // 4. Beralih kembali ke Operator tanpa refresh layar!
            // Harusnya muncul notification banner secara live melalui echo/alpine liveUpdateShow
            $operatorBrowser->waitForText('baru saja diperbarui menjadi 25 Pcs', 10) // Tunggu banner max 10 dtk
                            ->assertSee('25'); // Pastikan DOM berubah menjadi 25 secara reaktif
        });
    }

    public function test_realtime_stock_approval_list_auto_refresh()
    {
        $this->browse(function (Browser $adminBrowser, Browser $operatorBrowser) {

            // 1. Data Uji
            $operator = \App\Models\User::factory()->create(['role' => 'operator']);
            $admin = \App\Models\User::factory()->create(['role' => 'admin']);
            $sparepart = \App\Models\Sparepart::factory()->create(['name' => 'Kabel Tester UX', 'stock' => 10, 'minimum_stock' => 5]);

            // 2. Admin Standby di halaman Persetujuan Stok
            $adminBrowser->loginAs($admin)
                         ->visitRoute('inventory.stock-approvals.index')
                         ->waitForText('Persetujuan Stok')
                         ->assertDontSee('Kabel Tester UX'); // Pastikan belum ada request

            // 3. Operator Mengajukan penyesuaian stok dari peramban sebelahnya
            $operatorBrowser->loginAs($operator)
                            ->visitRoute('inventory.show', $sparepart->id)
                            ->press('Ajukan Perubahan Stok') // Buka modal penyesuaian stok
                            ->waitForText('Tipe Penyesuaian') // Asumsi ada teks ini atau tunggu modal
                            ->pause(1000)
                            ->radio('type', 'masuk') // Pilih tipe masuk
                            ->type('#quantity', 3)
                            ->type('#reason', 'Butuh kabel untuk testing server')
                            ->press('Kirim Pengajuan')
                            ->waitForText('Pengajuan berhasil');

            // 4. Admin melihat layar ter-update otomatis tanpa refresh
            // Toast notifikasi muncul dan row tabel baru ter-render
            $adminBrowser->waitForText('Pengajuan Baru Masuk!', 10)
                         ->waitForText('Kabel Tester UX', 10) // Render tabel sukses via AJAX
                         ->assertSee('Butuh kabel untuk testing server');
            
            // 5. Admin (dari browser Admin) Memproses (Setujui) request tersebut untuk mengecek feedback realtime
            $adminBrowser->press('Setujui')
                         ->waitForText('yakin ingin menyetujui')
                         ->press('Ya, Setujui') // Sweetalert confirm
                         ->waitForText('Pengajuan Diproses', 10) // Notifikasi realtime setelah proses
                         ->waitUntilMissingText('Butuh kabel untuk testing server'); // Lenyap dari pending list otomatis
        });
    }

    public function test_realtime_dashboard_stats_auto_refresh()
    {
        $this->browse(function (Browser $adminBrowser, Browser $workerBrowser) {
            $admin = \App\Models\User::factory()->create(['role' => 'superadmin']);
            $operator = \App\Models\User::factory()->create(['role' => 'operator']);
            $sparepart = \App\Models\Sparepart::factory()->create(['name' => 'Kabel UTP Cat6', 'stock' => 50, 'minimum_stock' => 10, 'type' => 'asset']);

            // 1. Admin login ke Dashboard dan melihat data statistik
            $adminBrowser->loginAs($admin)
                         ->visitRoute('dashboard')
                         ->waitForText('Dashboard');
            
            // 2. Pekerja (Operator) meminjam banyak barang dari browser lain
            $workerBrowser->loginAs($operator)
                          ->visitRoute('inventory.show', $sparepart->id)
                          ->press('Pinjam Barang') 
                          ->waitFor('#borrow_quantity')
                          ->pause(1000)
                          ->type('#borrow_quantity', 10)
                          ->type('#expected_return_at', now()->addDays(2)->format('Y-m-d'))
                          ->type('#notes', 'Tarik kabel jaringan lantai 2')
                          ->press('Kirim Pengajuan')
                          ->waitForText('Pengajuan berhasil'); // Peminjaman instan memotong stok
            
            // 3. Admin yang sedang bengong di Dashboard otomatis menerima toast peminjaman dari public channel 
            // Dan script realtime akan me-redraw chart chart tanpa merefresh
            $adminBrowser->waitForText('Kabel UTP Cat6', 15) // Toast alert stok berubah di dashboard
                         ->assertSee('Tarik kabel jaringan');
        });
    }

    public function test_realtime_activity_log_append()
    {
        $this->browse(function (Browser $adminBrowser, Browser $workerBrowser) {
            $admin = \App\Models\User::factory()->create(['role' => 'superadmin']);
            $operator = \App\Models\User::factory()->create(['role' => 'operator']);
            $sparepart = \App\Models\Sparepart::factory()->create(['name' => 'Mouse Wireless Logi', 'stock' => 20]);

            // 1. Admin memantau halaman laporan log aktivitas
            $adminBrowser->loginAs($admin)
                         ->visit('/reports/activity-logs') // Route default umum log
                         ->waitForText('Log Aktivitas');

            // 2. Operator mengurangi/adjust stock
            $workerBrowser->loginAs($operator)
                          ->visitRoute('inventory.show', $sparepart->id)
                          ->press('Ajukan Perubahan Stok') 
                          ->waitForText('Tipe Penyesuaian') 
                          ->pause(1000)
                          ->radio('type', 'keluar') 
                          ->type('#quantity', 2)
                          ->type('#reason', 'Mouse rusak terjatuh')
                          ->press('Kirim Pengajuan')
                          ->waitForText('Pengajuan berhasil');
            
            // 3. Admin mendapati baris baru ter-append di tabel log aktivitas utamanya (toast jg muncul)
            $adminBrowser->waitForText('Mouse rusak terjatuh', 15)
                         ->assertSee('Mouse Wireless Logi');
        });
    }

    public function test_realtime_global_notification_bell()
    {
        $this->browse(function (Browser $adminBrowser, Browser $workerBrowser) {
            $admin = \App\Models\User::factory()->create(['role' => 'superadmin']);
            $operator = \App\Models\User::factory()->create(['role' => 'operator']);
            $sparepart = \App\Models\Sparepart::factory()->create(['name' => 'RAM 16GB DDR4', 'stock' => 5, 'minimum_stock' => 4, 'type' => 'asset']);

            // 1. Admin login dan buka sembarang halaman web, misalnya blank atau dashboard
            $adminBrowser->loginAs($admin)
                         ->visitRoute('profile.edit'); // Halaman aman yg tidak ada tabel 

            // 2. Pekerja meminjam RAM hingga menyentuh ambang limit krisis (minimum_stock = 4)
            $workerBrowser->loginAs($operator)
                          ->visitRoute('inventory.show', $sparepart->id)
                          ->press('Pinjam Barang') 
                          ->waitFor('#borrow_quantity')
                          ->pause(1000)
                          ->type('#borrow_quantity', 3) // sisa 2, < 4! (Critical)
                          ->type('#expected_return_at', now()->addDays(2)->format('Y-m-d'))
                          ->type('#notes', 'Upgrade PC user')
                          ->press('Kirim Pengajuan')
                          ->waitForText('Pengajuan berhasil');
            
            // 3. Di layar admin tiba2 muncul alert SWEETALERT CRITICAL STOCK popup merah! (dari stock-alerts channel)
            $adminBrowser->waitForText('STOK KRITIS', 15) 
                         ->waitForText('RAM 16GB DDR4')
                         ->press('Tutup'); // Tutup modal SweetAlert
                         
            // 4. Dot notifikasi lonceng di Navigation Bar bertambah/menandakan notif database masuk tanpa reset nav
            // (Asumsi komponen notifikasi lonceng memiliki dropdown)
            $adminBrowser->click('#notification-bell-button') // Selector notifikasi umum
                         ->waitForText('mencapai batas minimum');
        });
    }
}
