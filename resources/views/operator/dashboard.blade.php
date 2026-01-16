<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-secondary-900">Halo, {{ Auth::user()->name }}!</h1>
                <p class="mt-1 text-secondary-500">Panel Operator untuk pengelolaan arus barang.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Scan QR Action -->
                <a href="#" onclick="alert('Fitur Scan QR untuk Operator belum tersedia.')" class="card p-8 flex flex-col items-center text-center hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-2 border-transparent hover:border-primary-500 group cursor-pointer bg-gradient-to-br from-white to-primary-50">
                    <div class="w-20 h-20 bg-primary-100 rounded-2xl flex items-center justify-center text-primary-600 mb-6 group-hover:scale-110 transition-transform duration-300 shadow-inner">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-secondary-900 mb-2">Scan QR Code</h2>
                    <p class="text-secondary-500 max-w-sm">Gunakan fitur ini untuk melakukan scan Barang Masuk atau Barang Keluar secara cepat.</p>
                    <div class="mt-6 btn btn-primary px-8">Mulai Scan</div>
                </a>

                <!-- Inventory Action -->
                <a href="#" onclick="alert('Fitur Daftar Inventaris untuk Operator belum tersedia.')" class="card p-8 flex flex-col items-center text-center hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-2 border-transparent hover:border-secondary-500 group cursor-pointer bg-white">
                    <div class="w-20 h-20 bg-secondary-100 rounded-2xl flex items-center justify-center text-secondary-600 mb-6 group-hover:scale-110 transition-transform duration-300 shadow-inner">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-secondary-900 mb-2">Daftar Inventaris</h2>
                    <p class="text-secondary-500 max-w-sm">Lihat daftar lengkap barang, cek stok, dan detail lokasi penyimpanan.</p>
                     <div class="mt-6 btn btn-secondary px-8">Lihat Inventaris</div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
