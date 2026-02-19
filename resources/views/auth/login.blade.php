<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center lg:text-left">
            <h3 class="text-2xl font-bold text-secondary-900">{{ __('ui.auth_welcome_title') }}</h3>
            <p class="text-secondary-500 mt-2">{{ __('ui.auth_welcome_desc') }}</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Field Login -->
            <div>
                <label for="login" class="input-label">{{ __('ui.auth_label_login') }}</label>
                <input id="login" type="text" name="login" class="input-field w-full" value="{{ old('login') }}" required autofocus tabindex="1">
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
                <div class="relative">
                    <input id="password" type="password" name="password" class="input-field w-full pr-10" required autocomplete="current-password" tabindex="2">
                    <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-secondary-400 hover:text-secondary-600 focus:outline-none" tabindex="-1">
                        <svg id="eye-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="eye-off-icon" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Ingat Saya -->
            <div class="block">
                <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:ring-primary-500 transition duration-150 ease-in-out cursor-pointer" tabindex="3">
                    <span class="ml-2 text-sm text-secondary-600 group-hover:text-secondary-800 transition-colors">{{ __('ui.auth_remember_me') }}</span>
                </label>
            </div>

            <button type="submit" class="w-full btn btn-primary justify-center py-3 text-base shadow-lg shadow-primary-500/20" tabindex="3">
                {{ __('ui.auth_btn_login') }}
            </button>

            <div class="text-center mt-6">
                <a href="/" class="text-sm font-medium text-secondary-500 hover:text-primary-600 transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    {{ __('ui.auth_back_home') }}
                </a>
            </div>
        </form>
    </div>

    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById('password');
            var eyeIcon = document.getElementById('eye-icon');
            var eyeOffIcon = document.getElementById('eye-off-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }
    </script>
</x-guest-layout>
