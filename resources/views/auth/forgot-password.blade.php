<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center lg:text-left">
            <h3 class="text-2xl font-bold text-secondary-900">{{ __('ui.auth_forgot_password') }}</h3>
            <p class="text-secondary-500 mt-2">
                {{ __('ui.auth_forgot_desc') }}
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5" x-data="{ isSubmitting: false }" @submit="isSubmitting = true" novalidate>
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="input-label">{{ __('ui.auth_label_email_registered') }}</label>
                <input id="email" type="email" name="email" class="input-field w-full" value="{{ old('email') }}" required autofocus placeholder="nama@email.com" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" title="Format email tidak valid (contoh: nama@domain.com)" maxlength="255">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <button type="submit" x-bind:disabled="isSubmitting" class="w-full btn btn-primary justify-center py-3 text-base shadow-lg shadow-primary-500/20 disabled:opacity-70 disabled:cursor-not-allowed">
                <span x-show="!isSubmitting">{{ __('ui.auth_btn_send_reset') }}</span>
                <span x-show="isSubmitting" class="flex items-center justify-center gap-2" x-cloak>
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses...
                </span>
            </button>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm font-medium text-secondary-600 hover:text-primary-600 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    {{ __('ui.auth_back_login') }}
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>


