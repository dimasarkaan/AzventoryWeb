<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center lg:text-left">
            <h3 class="text-2xl font-bold text-secondary-900">{{ __('ui.auth_welcome_title') }}</h3>
            <p class="text-secondary-500 mt-2">{{ __('ui.auth_welcome_desc') }}</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ loading: false }" @submit="loading = true" novalidate>
            @csrf

            <!-- Field Login -->
            <div>
                <label for="login" class="input-label">{{ __('ui.auth_label_login') }}</label>
                <input id="login" type="text" name="login" data-testid="input-login" class="input-field w-full" value="{{ old('login') }}" required autocomplete="username" autofocus tabindex="1" minlength="3" maxlength="255" title="Silakan masukkan username atau email yang valid">
                <x-input-error :messages="$errors->get('login')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label for="password" class="input-label mb-0">{{ __('ui.auth_label_password') }}</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-medium text-primary-600 hover:text-primary-500 transition-colors" tabindex="4">
                            {{ __('ui.auth_forgot_password') }}
                        </a>
                    @endif
                </div>
                <x-password-input id="password" name="password" data-testid="input-password" class="input-field w-full {{ $errors->has('password') ? '!border-red-500' : '' }}" required autocomplete="current-password" tabindex="2" maxlength="255" />
                <div id="caps-lock-warning" class="mt-2 p-2 bg-warning-50 border border-warning-200 rounded-lg flex items-center gap-2 text-warning-700 text-xs animate-pulse" style="display: none;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <strong>Perhatian:</strong> Tombol Caps Lock Anda sedang menyala!
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Ingat Saya -->
            <div class="block">
                <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember" data-testid="checkbox-remember" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:ring-primary-500 transition duration-150 ease-in-out cursor-pointer" tabindex="3">
                    <span class="ml-2 text-sm text-secondary-600 group-hover:text-secondary-800 transition-colors">{{ __('ui.auth_remember_me') }}</span>
                </label>
            </div>

            <button type="submit" 
                    data-testid="btn-login"
                    class="w-full btn btn-primary justify-center py-3 text-base shadow-lg shadow-primary-500/20 disabled:opacity-70 disabled:cursor-not-allowed" 
                    tabindex="3"
                    :disabled="loading">
                <span x-show="!loading">{{ __('ui.auth_btn_login') }}</span>
                <span x-show="loading" class="flex items-center gap-2" x-cloak>
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('ui.loading') }}
                </span>
            </button>

            <div class="text-center mt-6">
                <a href="/" class="text-sm font-medium text-secondary-500 hover:text-primary-600 transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    {{ __('ui.auth_back_home') }}
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        const passwordInput = document.getElementById('password');
        const capsWarning = document.getElementById('caps-lock-warning');

        passwordInput.addEventListener('keyup', function(e) {
            if (e.getModifierState('CapsLock')) {
                capsWarning.style.display = 'flex';
            } else {
                capsWarning.style.display = 'none';
            }
        });

        passwordInput.addEventListener('mousedown', function(e) {
            if (e.getModifierState('CapsLock')) {
                capsWarning.style.display = 'flex';
            } else {
                capsWarning.style.display = 'none';
            }
        });
    </script>
    @endpush
</x-guest-layout>


