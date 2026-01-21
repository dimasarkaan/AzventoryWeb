<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                    {{ __('Pusat Laporan') }}
                </h2>
                <p class="mt-1 text-sm text-secondary-500">Generate dan download laporan sistem dalam format PDF atau Excel.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Main Form Card -->
                <div class="lg:col-span-2">
                    <form action="{{ route('superadmin.reports.download') }}" method="GET" class="card p-6" x-data="{ reportType: 'inventory_list', period: 'this_month' }">
                        @csrf
                        
                        <!-- Report Categories -->
                        <div class="mb-8">
                            <label class="block text-sm font-bold text-secondary-900 mb-4">Pilih Jenis Laporan</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Inventaris -->
                                <label class="cursor-pointer relative group">
                                    <input type="radio" name="report_type" value="inventory_list" x-model="reportType" class="peer sr-only">
                                    <div class="p-5 rounded-xl border-2 border-secondary-100 hover:border-primary-400 peer-checked:border-primary-600 peer-checked:bg-primary-50 transition-all h-full flex flex-col items-center text-center">
                                        <div class="w-12 h-12 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center mb-3 group-hover:bg-primary-200 transition-colors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        </div>
                                        <span class="font-bold text-secondary-900 block mb-1">Data Inventaris</span>
                                        <span class="text-xs text-secondary-500 leading-tight">Stok saat ini, aset, lokasi</span>
                                    </div>
                                    <div class="absolute inset-0 border-2 border-primary-600 rounded-xl opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                                </label>

                                <!-- Mutasi Stok -->
                                <label class="cursor-pointer relative group">
                                    <input type="radio" name="report_type" value="stock_mutation" x-model="reportType" class="peer sr-only">
                                    <div class="p-5 rounded-xl border-2 border-secondary-100 hover:border-primary-400 peer-checked:border-primary-600 peer-checked:bg-primary-50 transition-all h-full flex flex-col items-center text-center">
                                        <div class="w-12 h-12 rounded-full bg-warning-100 text-warning-600 flex items-center justify-center mb-3 group-hover:bg-warning-200 transition-colors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                                        </div>
                                        <span class="font-bold text-secondary-900 block mb-1">Riwayat Mutasi</span>
                                        <span class="text-xs text-secondary-500 leading-tight">Log masuk & keluar barang</span>
                                    </div>
                                    <div class="absolute inset-0 border-2 border-primary-600 rounded-xl opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                                </label>

                                <!-- Peminjaman -->
                                <label class="cursor-pointer relative group">
                                    <input type="radio" name="report_type" value="borrowing_history" x-model="reportType" class="peer sr-only">
                                    <div class="p-5 rounded-xl border-2 border-secondary-100 hover:border-primary-400 peer-checked:border-primary-600 peer-checked:bg-primary-50 transition-all h-full flex flex-col items-center text-center">
                                        <div class="w-12 h-12 rounded-full bg-info-100 text-info-600 flex items-center justify-center mb-3 group-hover:bg-info-200 transition-colors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <span class="font-bold text-secondary-900 block mb-1">Riwayat Peminjaman</span>
                                        <span class="text-xs text-secondary-500 leading-tight">Log peminjaman user</span>
                                    </div>
                                    <div class="absolute inset-0 border-2 border-primary-600 rounded-xl opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                                </label>

                                 <!-- Low Stock -->
                                <label class="cursor-pointer relative group">
                                    <input type="radio" name="report_type" value="low_stock" x-model="reportType" class="peer sr-only">
                                    <div class="p-5 rounded-xl border-2 border-secondary-100 hover:border-primary-400 peer-checked:border-primary-600 peer-checked:bg-primary-50 transition-all h-full flex flex-col items-center text-center">
                                        <div class="w-12 h-12 rounded-full bg-danger-100 text-danger-600 flex items-center justify-center mb-3 group-hover:bg-danger-200 transition-colors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        </div>
                                        <span class="font-bold text-secondary-900 block mb-1">Stok Menipis</span>
                                        <span class="text-xs text-secondary-500 leading-tight">Barang perlu restock</span>
                                    </div>
                                    <div class="absolute inset-0 border-2 border-primary-600 rounded-xl opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                                </label>
                            </div>
                        </div>

                        <!-- Filters Section -->
                        <div class="border-t border-secondary-200 pt-6">
                            <h3 class="text-sm font-bold text-secondary-900 mb-4 uppercase tracking-wide">Konfigurasi Filter</h3>
                            
                            <!-- Date Period (Hidden for Inventory/Low Stock snapshot) -->
                            <div class="mb-4" x-show="['stock_mutation', 'borrowing_history'].includes(reportType)" x-transition>
                                <label class="block text-sm font-medium text-secondary-700 mb-2">Periode Waktu</label>
                                <select name="period" x-model="period" class="input-field w-full">
                                    <option value="this_month">Bulan Ini</option>
                                    <option value="last_month">Bulan Lalu</option>
                                    <option value="this_year">Tahun Ini</option>
                                    <option value="all">Semua Waktu</option>
                                    <option value="custom">Custom Tanggal</option>
                                </select>
                            </div>

                            <!-- Custom Date Range -->
                            <div class="grid grid-cols-2 gap-4 mb-4" x-show="period === 'custom' && ['stock_mutation', 'borrowing_history'].includes(reportType)" x-transition>
                                <div>
                                    <label class="block text-xs text-secondary-500 mb-1">Mulai</label>
                                    <input type="date" name="start_date" class="input-field w-full">
                                </div>
                                <div>
                                    <label class="block text-xs text-secondary-500 mb-1">Sampai</label>
                                    <input type="date" name="end_date" class="input-field w-full">
                                </div>
                            </div>

                            <!-- Location Filter -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-secondary-700 mb-2">Lokasi Gudang / Rak</label>
                                <input type="text" name="location" placeholder="Ketik lokasi (Contoh: Rak B-1) atau kosongkan untuk SEMUA" class="input-field w-full">
                                <p class="text-xs text-secondary-400 mt-1 italic">Biarkan kosong untuk memilih semua lokasi.</p>
                            </div>
                        </div>

                        <!-- Format & Action -->
                        <div class="bg-secondary-50 -mx-6 -mb-6 p-6 mt-8 rounded-b-lg flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-secondary-100">
                            <div class="flex items-center space-x-6">
                                <span class="text-sm font-medium text-secondary-700">Format:</span>
                                 <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="export_format" value="pdf" checked class="text-primary-600 focus:ring-primary-500 h-4 w-4 border-gray-300">
                                    <span class="ml-2 text-sm text-secondary-700 font-medium">PDF Document</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="export_format" value="excel" class="text-success-600 focus:ring-success-500 h-4 w-4 border-gray-300">
                                    <span class="ml-2 text-sm text-secondary-700 font-medium">Excel (.xls)</span>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-full sm:w-auto flex justify-center items-center gap-2 shadow-lg shadow-primary-500/30">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Download Laporan
                            </button>
                        </div>

                    </form>
                </div>

                <!-- Sidebar Info / Hints -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-primary-50/50 rounded-xl p-6 border border-primary-100 sticky top-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-primary-100 rounded-lg text-primary-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h4 class="font-bold text-primary-900">Tips Reporting</h4>
                        </div>
                        
                        <ul class="text-sm text-primary-800 space-y-4">
                            <li class="flex gap-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary-400 mt-1.5 flex-shrink-0"></span>
                                <div>
                                    <strong class="block text-primary-900">Format PDF</strong>
                                    Laporan resmi dengan Kop Surat, cocok untuk dicetak, diajukan ke atasan, atau arsip fisik.
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary-400 mt-1.5 flex-shrink-0"></span>
                                <div>
                                    <strong class="block text-primary-900">Format Excel</strong>
                                    Data siap olah dengan tabel yang rapi. Gunakan untuk analisis mendalam (Pivot Table, Grafik Custom).
                                </div>
                            </li>
                             <li class="flex gap-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary-400 mt-1.5 flex-shrink-0"></span>
                                <div>
                                    <strong class="block text-primary-900">Filter Lokasi</strong>
                                    Sangat berguna untuk melakukan <span class="font-semibold underline decoration-primary-300">Stock Opname per Lokasi</span>. Cukup ketik nama lokasi (misal: "Gudang A") untuk mendapatkan daftar barang di lokasi tersebut saja.
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
