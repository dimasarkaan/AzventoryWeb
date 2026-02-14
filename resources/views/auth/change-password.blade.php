<x-guest-layout>
    <div class="w-full sm:max-w-md">
        <x-card>
            <x-slot name="header">
                {{ __('ui.auth_change_password_title') }}
            </x-slot>

            @if (session('warning'))
                <div class="mb-4 font-medium text-sm text-yellow-600">
                    {{ session('warning') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.change.store') }}">
                @csrf

                <!-- Username (Only on First Login / Activation) -->
                @if(is_null(auth()->user()->password_changed_at))
                    <div class="mb-4">
                        <x-input-label for="username" :value="__('ui.auth_label_username_optional')" />
                        <x-text-input id="username" class="block mt-1 w-full bg-gray-50" type="text" name="username" :value="old('username', auth()->user()->username)" required autofocus />
                        <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        <p class="mt-1 text-xs text-gray-500">
                            {{ __('ui.auth_username_hint') }}
                        </p>
                    </div>
                @endif

                <!-- Current Password -->
                @if(!is_null(auth()->user()->password_changed_at))
                    <div>
                        <x-input-label for="current_password" :value="__('ui.auth_label_current_password')" />
                        <x-text-input id="current_password" class="block mt-1 w-full" type="password" name="current_password" required />
                        <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                    </div>
                @endif

                <!-- New Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('ui.auth_label_new_password')" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm New Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('ui.auth_label_new_password_confirmation')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-button type="submit" variant="primary">
                        {{ __('ui.auth_btn_save_password') }}
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-guest-layout>
