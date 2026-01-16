<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center lg:text-left">
            <h3 class="text-2xl font-bold text-secondary-900">Lupa Kata Sandi?</h3>
            <p class="text-secondary-500 mt-2">
                Jangan khawatir. Masukkan email Anda dan kami akan mengirimkan link untuk mereset kata sandi.
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="input-label">Alamat Email Terdaftar</label>
                <input id="email" type="email" name="email" class="input-field w-full" value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <button type="submit" class="w-full btn btn-primary justify-center py-3 text-base shadow-lg shadow-primary-500/20">
                {{ __('Kirim Link Reset Password') }}
            </button>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm font-medium text-secondary-600 hover:text-primary-600 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Halaman Login
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
