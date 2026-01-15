<x-guest-layout>
    <x-card>
        <x-slot name="header">
            {{ __('Lupa Kata Sandi') }}
        </x-slot>

        <div class="mb-4 text-sm text-secondary-600">
            {{ __('Lupa kata sandi Anda? Tidak masalah. Cukup beritahu kami alamat email Anda dan kami akan mengirimkan link untuk mengatur ulang kata sandi Anda.') }}
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Alamat Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between mt-4">
                <a class="underline text-sm text-secondary-600 hover:text-secondary-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" href="{{ route('login') }}">
                    {{ __('Kembali ke Login') }}
                </a>

                <x-button type="submit" variant="primary">
                    {{ __('Kirim Link Reset') }}
                </x-button>
            </div>
        </form>
    </x-card>
</x-guest-layout>
