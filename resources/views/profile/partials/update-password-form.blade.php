<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-3">
        @csrf
        @method('put')

        <div class="grid grid-cols-1 gap-x-6 gap-y-3 sm:grid-cols-6">
             <div class="sm:col-span-6">
                <label for="update_password_current_password" class="input-label">Password Saat Ini</label>
                <input type="password" name="current_password" id="update_password_current_password" class="input-field w-full" autocomplete="current-password">
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div class="sm:col-span-3">
                <label for="update_password_password" class="input-label">Password Baru</label>
                <input type="password" name="password" id="update_password_password" class="input-field w-full" autocomplete="new-password">
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div class="sm:col-span-3">
                <label for="update_password_password_confirmation" class="input-label">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" id="update_password_password_confirmation" class="input-field w-full" autocomplete="new-password">
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-2 border-t border-secondary-100">
            <button type="submit" class="btn btn-primary">
                {{ __('Simpan Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-success-600 bg-success-50 px-3 py-1 rounded-full border border-success-100"
                >
                    {{ __('Password berhasil diubah.') }}
                </p>
            @endif
        </div>
    </form>
</section>
