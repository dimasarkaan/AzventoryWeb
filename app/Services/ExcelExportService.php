<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelExportService
{
    // Desain Grafis Excel: Mengatur judul laporan (Title) agar terlihat mewah dan profesional
    protected function setupReportTitle($sheet, $title, $lastColumn)
    {
        $sheet->mergeCells("A1:{$lastColumn}2");
        $sheet->setCellValue('A1', mb_strtoupper($title));
        $sheet->getStyle("A1:{$lastColumn}2")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['argb' => 'FF0F172A'], // Slate-900
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF8FAFC'], // Slate-50
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['argb' => 'FF0284C7'], // Sky-600
                ],
            ],
        ]);

        $sheet->mergeCells("A3:{$lastColumn}3");
        $sheet->setCellValue('A3', 'Dicetak pada: '.now()->format('d/m/Y H:i:s').' oleh '.(auth()->user()?->name ?? 'Sistem')); // FIX: null-safe agar tidak crash jika context tanpa user
        $sheet->getStyle("A3:{$lastColumn}3")->applyFromArray([
            'font' => [
                'italic' => true,
                'size' => 10,
                'color' => ['argb' => 'FF64748B'], // Slate-500
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);
    }

    // Desain Grafis Excel: Memberikan warna dan gaya tebal (Bold) pada baris judul kolom (Header Tabel)
    protected function setupHeaderStyle($sheet, $headerRow, $lastColumn)
    {
        $headerRange = "A{$headerRow}:{$lastColumn}{$headerRow}";

        // Style Header
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => Color::COLOR_WHITE],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF0369A1', // Primary-700
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF0C4A6E'], // Primary-900 border
                ],
            ],
        ]);

        // Freeze the rows above data
        $sheet->freezePane('A'.($headerRow + 1));

        // Enable AutoFilter
        $sheet->setAutoFilter($headerRange);
    }

    // Format Otomatis: Melebarkan ukuran kolom secara otomatis agar teks panjang tidak terpotong
    protected function autoSizeColumns($sheet, $lastColumn)
    {
        foreach (range('A', $lastColumn) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
    }

    // Desain Grafis Excel: Memberikan garis batas (Border) atau tabel pada seluruh baris data
    protected function applyDataBorders($sheet, $headerRow, $lastColumn, $lastRow)
    {
        $startRow = $headerRow + 1;
        if ($lastRow >= $startRow) {
            $dataRange = "A{$startRow}:{$lastColumn}{$lastRow}";
            $sheet->getStyle($dataRange)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFCBD5E1'], // Slate-300
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }
    }

    // Desain Grafis Excel: Memberikan warna belang-belang (Zebra) pada baris data (Putih dan Biru Muda)
    // Tujuannya agar mata pengguna tidak lelah saat membaca ratusan baris data
    protected function applyAlternatingRowColors($sheet, $headerRow, $lastColumn, $lastRow)
    {
        $startRow = $headerRow + 1;
        for ($row = $startRow; $row <= $lastRow; $row++) {
            // Even rows get a light blue, odd rows stay white
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF0F9FF'], // Sky-50 (Very light blue)
                    ],
                ]);
            }
        }
    }

    // Tahap Penyimpanan: Menyimpan sementara file Excel ke dalam folder server sebelum dikirimkan ke komputer pengguna
    public function saveToFile($spreadsheet, $filename)
    {
        $writer = new Xlsx($spreadsheet);
        $path = storage_path('app/public/reports/temp_'.uniqid().'_'.$filename.'.xlsx');

        // Ensure directory exists
        if (! file_exists(storage_path('app/public/reports'))) {
            mkdir(storage_path('app/public/reports'), 0755, true);
        }

        $writer->save($path);

        return $path;
    }

    // Tahap Unduhan: Memulai proses download otomatis ke komputer pengguna.
    // Setelah file sukses di-download, file sementara di server akan langsung dihapus agar tidak menjadi sampah.
    protected function downloadResponse($spreadsheet, $filename)
    {
        $path = $this->saveToFile($spreadsheet, $filename);

        return response()->download($path, $filename.'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    // Fitur Utama: Membuat laporan Excel untuk Riwayat Aktivitas Sistem
    public function exportActivityLogs($logs, $filename)
    {
        $spreadsheet = $this->generateActivityLogsSpreadsheet($logs);

        return $this->downloadResponse($spreadsheet, $filename);
    }

    protected function generateActivityLogsSpreadsheet($logs)
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Log Aktivitas');

        $lastColumn = 'E';
        $headerRow = 5;

        // Setup Title
        $this->setupReportTitle($sheet, 'Laporan Riwayat Aktivitas Sistem', $lastColumn);

        // Headers
        $headers = ['Waktu', 'Pengguna', 'Role', 'Aksi', 'Deskripsi'];
        foreach ($headers as $index => $header) {
            $column = chr(ord('A') + $index);
            $sheet->setCellValue("{$column}{$headerRow}", $header);
        }

        $this->setupHeaderStyle($sheet, $headerRow, $lastColumn);

        // Data (Stream via lazy loading if it's a query builder)
        $row = $headerRow + 1;
        $items = ($logs instanceof \Illuminate\Database\Eloquent\Builder) ? $logs->lazy() : $logs;

        foreach ($items as $log) {
            $sheet->setCellValue("A{$row}", $log->created_at->format('d/m/Y H:i:s'));
            $sheet->setCellValue("B{$row}", $log->user ? $log->user->name : 'Sistem');
            $sheet->setCellValue("C{$row}", $log->user ? $log->user->role->label() : '-');
            $sheet->setCellValue("D{$row}", $log->action);
            $sheet->setCellValue("E{$row}", $log->description);
            $row++;
        }

        $this->applyAlternatingRowColors($sheet, $headerRow, $lastColumn, $row - 1);
        $this->applyDataBorders($sheet, $headerRow, $lastColumn, $row - 1);
        $this->autoSizeColumns($sheet, $lastColumn);

        return $spreadsheet;
    }

    // Fitur Utama: Membuat laporan Excel untuk Daftar Barang (Aset & Sparepart)
    public function exportInventoryList($data, $filename)
    {
        $spreadsheet = $this->generateInventoryListSpreadsheet($data);

        return $this->downloadResponse($spreadsheet, $filename);
    }

    protected function generateInventoryListSpreadsheet($data)
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Inventaris');

        $lastColumn = 'F';
        $headerRow = 5;

        // Setup Title
        $this->setupReportTitle($sheet, 'Laporan Daftar Inventaris (Sparepart & Aset)', $lastColumn);

        // Headers
        $headers = ['Nomor Part', 'Nama Barang', 'Kategori', 'Status', 'Lokasi', 'Stok Saat Ini'];
        foreach ($headers as $index => $header) {
            $column = chr(ord('A') + $index);
            $sheet->setCellValue("{$column}{$headerRow}", $header);
        }

        $this->setupHeaderStyle($sheet, $headerRow, $lastColumn);

        // Data (Stream via lazy loading if it's a query builder)
        $row = $headerRow + 1;
        $items = ($data instanceof \Illuminate\Database\Eloquent\Builder) ? $data->lazy() : $data;

        foreach ($items as $item) {
            $sheet->setCellValue("A{$row}", $item->part_number);
            $sheet->setCellValue("B{$row}", $item->name);
            $sheet->setCellValue("C{$row}", $item->category?->name ?? '-');

            // Status Logic: Handle both Enum object and string
            $statusText = ($item->status instanceof \BackedEnum) ? $item->status->value : (is_string($item->status) ? ucfirst($item->status) : $item->status);

            if ($item->stock <= 0 && (strtolower($item->status) === 'aktif')) {
                $statusText = 'Habis';
            }
            $sheet->setCellValue("D{$row}", $statusText);

            // Status Color Coding
            $color = match (strtolower($statusText ?? '')) {
                'aman', 'aktif' => 'FF059669', // Emerald-600
                'menipis' => 'FFD97706', // Amber-600
                'habis', 'rusak', 'nonaktif', 'hilang' => 'FFDC2626', // Red-600
                default => 'FF64748B', // Slate-500
            };
            $sheet->getStyle("D{$row}")->applyFromArray([
                'font' => ['color' => ['argb' => $color], 'bold' => true],
            ]);

            $sheet->setCellValue("E{$row}", $item->location?->name ?? '-');
            $sheet->setCellValueExplicit("F{$row}", $item->stock, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $row++;
        }

        // Add Total Formula
        if ($row > ($headerRow + 1)) {
            $sheet->setCellValue("E{$row}", 'TOTAL STOK KESELURUHAN:');
            $sheet->getStyle("E{$row}")->applyFromArray(['font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]]);

            $lastDataRow = $row - 1;
            $sheet->setCellValue("F{$row}", '=SUM(F'.($headerRow + 1).":F{$lastDataRow})");
            $sheet->getStyle("F{$row}")->applyFromArray(['font' => ['bold' => true]]);

            $row++; // Increment so borders cover this
        }

        $this->applyAlternatingRowColors($sheet, $headerRow, $lastColumn, $row - 1);
        $this->applyDataBorders($sheet, $headerRow, $lastColumn, $row - 1);
        $this->autoSizeColumns($sheet, $lastColumn);

        return $spreadsheet;
    }

    // Fitur Utama: Membuat laporan Excel untuk Riwayat Mutasi (Keluar/Masuk) Stok Barang
    public function exportStockMutation($data, $filename)
    {
        $spreadsheet = $this->generateStockMutationSpreadsheet($data);

        return $this->downloadResponse($spreadsheet, $filename);
    }

    protected function generateStockMutationSpreadsheet($data)
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Mutasi Stok');

        $lastColumn = 'H';
        $headerRow = 5;

        // Setup Title
        $this->setupReportTitle($sheet, 'Laporan Harga & Mutasi Stok', $lastColumn);

        // Headers
        $headers = ['Tanggal', 'Nomor Part', 'Nama Barang', 'Tipe Mutasi', 'Jumlah', 'Diproses Oleh', 'Keterangan'];
        foreach ($headers as $index => $header) {
            $column = chr(ord('A') + $index);
            $sheet->setCellValue("{$column}{$headerRow}", $header);
        }

        $this->setupHeaderStyle($sheet, $headerRow, $lastColumn);

        // Data (Stream via lazy loading if it's a query builder)
        $row = $headerRow + 1;
        $items = ($data instanceof \Illuminate\Database\Eloquent\Builder) ? $data->lazy() : $data;

        foreach ($items as $log) {
            $sheet->setCellValue("A{$row}", $log->created_at->format('d/m/Y H:i'));
            $sheet->setCellValue("B{$row}", $log->sparepart->part_number ?? '-');
            $sheet->setCellValue("C{$row}", $log->sparepart->name ?? '-');

            $typeLabel = $log->type === 'masuk' ? 'Masuk' : 'Keluar';

            $sheet->setCellValue("D{$row}", $typeLabel);

            // Format quantity (+ / -)
            $qtyPrefix = $log->type === 'masuk' ? '+' : '-';
            // Store as explicit string or number, keep format (+ / -)
            $sheet->setCellValueExplicit("E{$row}", $qtyPrefix.$log->quantity, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

            $sheet->setCellValue("F{$row}", $log->user->name ?? '-');
            $sheet->setCellValue("G{$row}", $log->reason);
            $row++;
        }

        $this->applyAlternatingRowColors($sheet, $headerRow, $lastColumn, $row - 1);
        $this->applyDataBorders($sheet, $headerRow, $lastColumn, $row - 1);
        $this->autoSizeColumns($sheet, $lastColumn);

        return $spreadsheet;
    }

    // Fitur Utama: Membuat laporan Excel untuk Riwayat Peminjaman Barang
    public function exportBorrowingHistory($data, $filename)
    {
        $spreadsheet = $this->generateBorrowingHistorySpreadsheet($data);

        return $this->downloadResponse($spreadsheet, $filename);
    }

    protected function generateBorrowingHistorySpreadsheet($data)
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat Peminjaman');

        $lastColumn = 'G';
        $headerRow = 5;

        // Setup Title
        $this->setupReportTitle($sheet, 'Laporan Riwayat Peminjaman Barang', $lastColumn);

        // Headers
        $headers = ['Nama Peminjam', 'Nama Barang', 'Jumlah', 'Tgl Pinjam', 'Tenggat Waktu', 'Tgl Kembali', 'Status'];
        foreach ($headers as $index => $header) {
            $column = chr(ord('A') + $index);
            $sheet->setCellValue("{$column}{$headerRow}", $header);
        }

        $this->setupHeaderStyle($sheet, $headerRow, $lastColumn);

        // Data (Stream via lazy loading if it's a query builder)
        $row = $headerRow + 1;
        $items = ($data instanceof \Illuminate\Database\Eloquent\Builder) ? $data->lazy() : $data;

        foreach ($items as $borrowing) {
            $sheet->setCellValue("A{$row}", $borrowing->user->name ?? $borrowing->borrower_name ?? '-');
            $sheet->setCellValue("B{$row}", $borrowing->sparepart->name ?? '-');
            $sheet->setCellValueExplicit("C{$row}", $borrowing->quantity, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);

            $borrowedAt = $borrowing->borrowed_at ? (\Carbon\Carbon::parse($borrowing->borrowed_at)->format('d/m/Y')) : '-';
            $dueDate = $borrowing->expected_return_at ? (\Carbon\Carbon::parse($borrowing->expected_return_at)->format('d/m/Y')) : ($borrowing->due_date ? (\Carbon\Carbon::parse($borrowing->due_date)->format('d/m/Y')) : '-');
            $returnedAt = $borrowing->returned_at ? \Carbon\Carbon::parse($borrowing->returned_at)->format('d/m/Y') : '-';

            $sheet->setCellValue("D{$row}", $borrowedAt);
            $sheet->setCellValue("E{$row}", $dueDate);
            $sheet->setCellValue("F{$row}", $returnedAt);

            // Handle Enum or String for Borrowing Status
            $statusText = ($borrowing->status instanceof \BackedEnum) ? $borrowing->status->value : (is_string($borrowing->status) ? ucfirst($borrowing->status) : $borrowing->status);
            $sheet->setCellValue("G{$row}", $statusText);

            // Status Color Coding
            $color = match (strtolower($statusText ?? '')) {
                'dikembalikan', 'selesai', 'disetujui' => 'FF059669', // Emerald-600
                'dipinjam', 'menunggu', 'pending' => 'FFD97706', // Amber-600
                'terlambat', 'ditolak', 'hilang' => 'FFDC2626', // Red-600
                default => 'FF64748B', // Slate-500
            };
            $sheet->getStyle("G{$row}")->applyFromArray([
                'font' => ['color' => ['argb' => $color], 'bold' => true],
            ]);

            // Format Quantity Column
            $sheet->getStyle("C{$row}")->getNumberFormat()->setFormatCode('#,##0');

            $row++;
        }

        // Add Total Formula
        if ($row > ($headerRow + 1)) {
            $sheet->setCellValue("B{$row}", 'TOTAL ITEM DIPINJAM:');
            $sheet->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]]);

            $lastDataRow = $row - 1;
            $sheet->setCellValue("C{$row}", '=SUM(C'.($headerRow + 1).":C{$lastDataRow})");
            $sheet->getStyle("C{$row}")->applyFromArray(['font' => ['bold' => true]]);

            $row++;
        }

        $this->applyAlternatingRowColors($sheet, $headerRow, $lastColumn, $row - 1);
        $this->applyDataBorders($sheet, $headerRow, $lastColumn, $row - 1);
        $this->autoSizeColumns($sheet, $lastColumn);

        return $spreadsheet;
    }

    // Fitur Utama: Membuat laporan Excel untuk Barang yang Stoknya Menipis (Butuh Restock Cepat)
    public function exportLowStock($data, $filename)
    {
        $spreadsheet = $this->generateLowStockSpreadsheet($data);

        return $this->downloadResponse($spreadsheet, $filename);
    }

    protected function generateLowStockSpreadsheet($data)
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Stok Menipis');

        $lastColumn = 'F';
        $headerRow = 5;

        // Setup Title
        $this->setupReportTitle($sheet, 'Laporan Barang Stok Menipis (Butuh Restock)', $lastColumn);

        // Headers
        $headers = ['Nomor Part', 'Nama Barang', 'Kategori', 'Lokasi', 'Batas Minimum', 'Stok Saat Ini'];
        foreach ($headers as $index => $header) {
            $column = chr(ord('A') + $index);
            $sheet->setCellValue("{$column}{$headerRow}", $header);
        }

        $this->setupHeaderStyle($sheet, $headerRow, $lastColumn);

        // Data (Stream via lazy loading if it's a query builder)
        $row = $headerRow + 1;
        $items = ($data instanceof \Illuminate\Database\Eloquent\Builder) ? $data->lazy() : $data;

        foreach ($items as $item) {
            $sheet->setCellValue("A{$row}", $item->part_number);
            $sheet->setCellValue("B{$row}", $item->name);
            $sheet->setCellValue("C{$row}", $item->category->name ?? '-');
            $sheet->setCellValue("D{$row}", $item->location->name ?? '-');
            $sheet->setCellValueExplicit("E{$row}", $item->minimum_stock, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $sheet->setCellValueExplicit("F{$row}", $item->stock, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $sheet->getStyle("E{$row}:F{$row}")->getNumberFormat()->setFormatCode('#,##0');

            // Highlight low stock
            if ($item->stock <= 0) {
                $sheet->getStyle("F{$row}")->applyFromArray([
                    'font' => ['color' => ['argb' => 'FFDC2626'], 'bold' => true], // Red-600
                ]);
            } else {
                $sheet->getStyle("F{$row}")->applyFromArray([
                    'font' => ['color' => ['argb' => 'FFD97706'], 'bold' => true], // Amber-600
                ]);
            }

            $row++;
        }

        $this->applyAlternatingRowColors($sheet, $headerRow, $lastColumn, $row - 1);
        $this->applyDataBorders($sheet, $headerRow, $lastColumn, $row - 1);
        $this->autoSizeColumns($sheet, $lastColumn);

        return $spreadsheet;
    }
}
