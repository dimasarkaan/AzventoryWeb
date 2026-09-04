<x-guest-layout>
    <x-card>
        <x-slot name="header">
            {{ __('ui.auth_reset_title') }}
        </x-slot>

        <form method="POST" action="{{ route('password.store') }}" x-data="{ isSubmitting: false }" @submit="isSubmitting = true" novalidate>
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('ui.auth_label_email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('ui.auth_label_new_password')" />
                <x-password-input id="password" class="block mt-1 w-full" name="password" required autocomplete="new-password" minlength="8" maxlength="16" pattern="(?=.*\d)(?=.*[a-zA-Z]).{8,16}" title="Kata sandi harus 8-16 karakter dan mengandung kombinasi huruf dan angka" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-input-label for="password_confirmation" :value="__('ui.auth_label_new_password_confirmation')" />
                <x-password-input id="password_confirmation" class="block mt-1 w-full" name="password_confirmation" required autocomplete="new-password" minlength="8" maxlength="16" pattern="(?=.*\d)(?=.*[a-zA-Z]).{8,16}" title="Konfirmasi kata sandi harus sama dengan kata sandi baru" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button type="submit" variant="primary" x-bind:disabled="isSubmitting" class="disabled:opacity-70 disabled:cursor-not-allowed w-full sm:w-auto justify-center">
                    <span x-show="!isSubmitting">{{ __('ui.auth_reset_title') }}</span>
                    <span x-show="isSubmitting" class="flex items-center gap-2" x-cloak>
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses...
                    </span>
                </x-button>
            </div>
        </form>
    </x-card>
</x-guest-layout>


