<x-guest-layout>
    <div class="w-full sm:max-w-md">
        <x-card>
            <x-slot name="header">
                {{ __('Ganti Kata Sandi Anda') }}
            </x-slot>

            @if (session('warning'))
                <div class="mb-4 font-medium text-sm text-yellow-600">
                    {{ session('warning') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.change.store') }}">
                @csrf

                <!-- Current Password -->
                <div>
                    <x-input-label for="current_password" :value="__('Kata Sandi Saat Ini')" />
                    <x-text-input id="current_password" class="block mt-1 w-full" type="password" name="current_password" required />
                    <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                </div>

                <!-- New Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Kata Sandi Baru')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm New Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi Baru')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-button type="submit" variant="primary">
                        {{ __('Simpan Kata Sandi Baru') }}
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-guest-layout>
