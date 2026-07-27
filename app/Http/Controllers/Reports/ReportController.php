<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateReportJob;
use App\Notifications\ReportReadyNotification;
use App\Services\InventoryService;
use App\Services\ReportService;
use App\Traits\ActivityLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Controller khusus untuk menangani proses pembuatan laporan (Report) sistem inventaris.
// Mengatur penarikan data, pilihan format keluaran (PDF/Excel), hingga logika pengunduhan file laporan.
class ReportController extends Controller
{
    use ActivityLogger;

    protected $reportService;

    protected $inventoryService;

    public function __construct(ReportService $reportService, InventoryService $inventoryService)
    {
        $this->reportService = $reportService;
        $this->inventoryService = $inventoryService;
    }

    // Menampilkan halaman utama menu Laporan (tempat pengguna memilih form filter seperti bulan, tahun, dll)
    public function index()
    {
        $options = $this->inventoryService->getDropdownOptions();
        $locations = $options['locationOptions'];

        return view('reports.index', compact('locations'));
    }

    // Memproses alur permintaan saat tombol "Cetak Laporan" ditekan
    public function download(Request $request)
    {
        // Validasi perlindungan agar user tidak memasukkan batas waktu yang keliru (contoh: tgl akhir lebih tua dari awal)
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'end_date.after_or_equal' => 'Tanggal akhir tidak boleh lebih awal dari tanggal mulai.',
        ]);

        // Menampung filter apa saja yang diklik oleh pengguna di form depan
        $type = $request->input('report_type', 'inventory'); // Tipe laporan (Mutasi, Peminjaman, dll)
        $period = $request->input('period', 'all'); // Rentang waktu yang dipilih
        $format = $request->input('export_format', 'pdf'); // Pilihan cetak ke PDF atau Excel
        $location = $request->input('location', 'all'); // Pilihan saringan gudang/lokasi tertentu

        // Mengonversi kata 'bulan ini' atau 'tahun ini' menjadi format tanggal mutlak (Tgl Mulai & Tgl Akhir)
        [$startDate, $endDate] = $this->reportService->resolveDateRange(
            $period,
            $request->input('start_date'),
            $request->input('end_date')
        );

        // Menarik susunan data murni (Query Data) langsung dari database untuk kebutuhan Excel
        $reportQuery = $this->reportService->getReportQuery($type, $location, $startDate, $endDate);
        $query = $reportQuery['query'];

        if (! $query) {
            return back()->with('error', 'Tipe laporan tidak ditemukan atau tidak valid.');
        }

        // ================= PENAMAAN NAMA FILE (FILE NAMING) =================
        $prefix = match ($type) {
            'inventory_list' => 'LaporanInventaris',
            'stock_mutation' => 'LaporanMutasi',
            'borrowing_history' => 'LaporanPeminjaman',
            'low_stock' => 'LaporanStokMenipis',
            default => 'Laporan'
        };

        if ($startDate && $endDate) {
            $start = $startDate->format('d-m-Y');
            $end = $endDate->format('d-m-Y');
            $filename = "{$prefix}_{$start}sd{$end}";
        } elseif ($startDate) {
            $start = $startDate->format('d-m-Y');
            $filename = "{$prefix}_Sejak{$start}";
        } else {
            $filename = "{$prefix}SemuaRiwayat_".now()->format('d-m-Y');
        }

        // ================= JALUR EKSPOR KE PDF =================
        if ($format !== 'excel') {
            // Menarik hasil data matang yang sudah jadi Array untuk digambar di lembar PDF
            $reportData = $this->reportService->getReportData($type, $location, $startDate, $endDate);

            // Jika jumlah datanya wajar (dibawah 1000 data), proses langsung detik itu juga karena server pasti kuat
            if (count($reportData['data']) <= 1000) {
                $reportTypeLabel = match ($type) {
                    'inventory_list' => 'Inventaris',
                    'stock_mutation' => 'Mutasi Stok',
                    'borrowing_history' => 'Peminjaman',
                    'low_stock' => 'Stok Menipis',
                    'activity_log' => 'Aktivitas Sistem',
                    default => 'Sistem'
                };
                $this->logActivity('Laporan Diunduh', "Mengunduh PDF Laporan {$reportTypeLabel}");

                // Memuat layout tampilan kertas menggunakan library DOMPDF
                $pdf = app()->make('dompdf.wrapper')->loadView($reportData['view'], [
                    'data' => $reportData['data'],
                    'title' => $reportData['title'],
                    'isPdf' => true, // Bendera (flag) agar view sadar kalau ia sedang dicetak ke PDF, bukan ke web
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'location' => $location,
                ]);

                // Mencetak hasil rakitan PDF dan menyimpannya secara fisik di penyimpanan rahasia server (local storage)
                $pdfOutput = $pdf->output();
                $filenameWithExt = $filename.'.pdf';
                $path = 'reports/'.$filenameWithExt;
                Storage::disk('local')->put($path, $pdfOutput);

                // Kirim notifikasi ke sistem Lonceng user, agar file hasil unduhan ini tersimpan riwayatnya dan bisa didownload ulang nanti
                $url = route('reports.file', ['filename' => $filenameWithExt]);
                $notifyTitle = match ($type) {
                    'inventory_list' => 'Laporan Inventaris',
                    'stock_mutation' => 'Laporan Mutasi Stok',
                    'borrowing_history' => 'Laporan Peminjaman',
                    'low_stock' => 'Laporan Stok Menipis',
                    default => 'Laporan Sistem'
                };
                $request->user()->notify(new ReportReadyNotification($notifyTitle, $url));

                // Langsung paksa browser pengguna untuk mendownload/menampilkan file yang barusan kita buat
                return response($pdfOutput)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="'.$filenameWithExt.'"')
                    ->header('Access-Control-Expose-Headers', 'Content-Disposition');
            }

            // Jika ukuran datanya RAKSASA (>1000 row), akan bahaya jika diproses langsung karena bisa bikin error 504 Timeout
            // Maka pembuatannya kita lemparkan ke 'pekerja belakang layar' (Queue / Background Job)
            GenerateReportJob::dispatch($request->user(), $startDate, $endDate, $location, $type);

            $reportTypeLabel = match ($type) {
                'inventory_list' => 'Inventaris',
                'stock_mutation' => 'Mutasi Stok',
                'borrowing_history' => 'Peminjaman',
                'low_stock' => 'Stok Menipis',
                'activity_log' => 'Aktivitas Sistem',
                default => 'Sistem'
            };
            $this->logActivity('Laporan Diproses', "Meminta antrean sistem untuk memproses PDF Laporan {$reportTypeLabel}");

            $message = 'Laporan sedang diproses karena ukuran data yang besar. Silakan cek menu Notifikasi dalam beberapa saat untuk mengunduh file.';

            // Beri konfirmasi ke browser bahwa perintah berhasil masuk antrean
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'type' => 'info',
                ]);
            }

            return back()->with('info', $message);
        }

        // ================= JALUR EKSPOR KE EXCEL =================
        // Langsung oper susunan query database murni kita ke layanan khusus ExcelExportService
        $excelService = new \App\Services\ExcelExportService;
        $reportTypeLabel = match ($type) {
            'inventory_list' => 'Inventaris',
            'stock_mutation' => 'Mutasi Stok',
            'borrowing_history' => 'Peminjaman',
            'low_stock' => 'Stok Menipis',
            'activity_log' => 'Aktivitas Sistem',
            default => 'Sistem'
        };
        $this->logActivity('Laporan Diunduh (Excel)', "Mengunduh Excel Laporan {$reportTypeLabel}");

        return match ($type) {
            'inventory_list' => $excelService->exportInventoryList($query, $filename),
            'stock_mutation' => $excelService->exportStockMutation($query, $filename),
            'borrowing_history' => $excelService->exportBorrowingHistory($query, $filename),
            'low_stock' => $excelService->exportLowStock($query, $filename),
            default => back()->with('error', 'Tipe laporan tidak didukung untuk export Excel.'),
        };
    }

    // Endpoint khusus (Link rahasia) untuk mengunduh file PDF yang sudah berhasil dicetak dan masuk ke lonceng notifikasi
    public function downloadGeneratedFile(string $filename)
    {
        $path = 'reports/' . $filename;

        // Cek dulu apakah file masih ada di dalam brankas penyimpanan (local disk) yang aman dari pihak luar
        if (Storage::disk('local')->exists($path)) {
            $this->logActivity('Laporan Diunduh (Secure)', "Mengunduh file laporan secara aman: {$filename}");
            return Storage::disk('local')->download($path);
        }

        // Rencana B: Jika tak ditemukan, cari di folder bebas (public) -- Ini berguna kalau server sebelumnya memakai sistem public
        if (Storage::disk('public')->exists($path)) {
            $this->logActivity('Laporan Diunduh (Fallback Public)', "Mengunduh file laporan dari disk public: {$filename}");
            return Storage::disk('public')->download($path);
        }

        // Tolak dan usir kembali ke dashboard jika file tersebut hilang / memang sudah dibersihkan oleh server karena kadaluarsa
        return redirect()->route('dashboard')->with('error', 'Laporan tersebut sudah kedaluwarsa atau telah dihapus oleh sistem.');
    }
}
