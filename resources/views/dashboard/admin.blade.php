<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-secondary-900">Halo, {{ Auth::user()->name }}!</h1>
                <p class="mt-1 text-secondary-500">Selamat datang di Panel Admin. Apa yang ingin Anda kerjakan hari ini?</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Manajemen Inventaris -->
                <a href="#" onclick="alert('Fitur Manajemen Inventaris untuk Admin belum tersedia.')" class="card p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
                    <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center text-primary-600 mb-4 group-hover:bg-primary-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-secondary-900 mb-2">Manajemen Inventaris</h3>
                    <p class="text-sm text-secondary-500">Kelola stok barang, tambah item baru, atau update ketersediaan.</p>
                </a>

                <!-- Scan QR -->
                <a href="#" onclick="alert('Fitur Scan QR untuk Admin belum tersedia.')" class="card p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 mb-4 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-secondary-900 mb-2">Scan QR Code</h3>
                    <p class="text-sm text-secondary-500">Pindai QR Code untuk cek detail barang atau update stok cepat.</p>
                </a>

                <!-- Notifikasi -->
                <a href="{{ route('notifications.index') }}" class="card p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600 mb-4 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-secondary-900 mb-2">Notifikasi</h3>
                    <p class="text-sm text-secondary-500">Lihat peringatan stok menipis dan aktivitas terbaru.</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
