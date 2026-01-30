<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                 <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                    {{ __('Profil Saya') }}
                </h2>
                <p class="mt-1 text-sm text-secondary-500">
                    Kelola informasi akun, keamanan, dan preferensi Anda.
                </p>
            </div>

            <div class="space-y-4">
                <!-- Profile Information -->
                <div class="card">
                    <div class="card-header px-4 py-2 flex flex-col items-start gap-0.5">
                        <h3 class="text-lg font-bold text-secondary-900">Informasi Profil</h3>
                        <p class="text-sm text-secondary-500">
                            Perbarui informasi profil akun dan alamat email Anda.
                        </p>
                    </div>
                    <div class="card-body p-4 pt-2">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- Update Password -->
                <div class="card">
                    <div class="card-header px-4 py-2 flex flex-col items-start gap-0.5">
                        <h3 class="text-lg font-bold text-secondary-900">Perbarui Password</h3>
                        <p class="text-sm text-secondary-500">
                            Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.
                        </p>
                    </div>
                    <div class="card-body p-4 pt-2">
                         @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Delete Account -->
                <div class="card border-danger-200">
                    <div class="card-header px-4 py-2 bg-danger-50 border-danger-100 flex flex-col items-start gap-1">
                        <h3 class="text-lg font-bold text-danger-900">Hapus Akun</h3>
                        <p class="text-sm text-danger-700">
                            Area berbahaya. Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                     <div class="card-body p-4">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
