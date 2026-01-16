<section class="space-y-4">
    <div class="flex items-start gap-4">
        <div class="flex-shrink-0">
             <div class="h-10 w-10 rounded-full bg-danger-100 flex items-center justify-center text-danger-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
        </div>
        <div>
            <h3 class="text-lg font-medium text-danger-900">Perhatian: Tindakan Permanen</h3>
             <p class="mt-1 text-sm text-secondary-600 leading-relaxed">
                Setelah akun dihapus, semua data akan hilang selamanya. Pastikan Anda sudah membackup data penting sebelum melanjutkan.
            </p>
             <div class="mt-4">
                 <button 
                    x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                    class="btn btn-danger"
                >
                    {{ __('Hapus Akun Saya') }}
                </button>
            </div>
        </div>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-secondary-900">
                {{ __('Apakah Anda yakin ingin menghapus akun?') }}
            </h2>

            <p class="mt-2 text-sm text-secondary-600">
                {{ __('Setelah akun dihapus, data tidak bisa dikembalikan. Silakan masukkan password Anda untuk konfirmasi.') }}
            </p>

            <div class="mt-6">
                <label for="password" class="sr-only">{{ __('Password') }}</label>
                <div class="relative">
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="input-field w-3/4"
                        placeholder="{{ __('Password Anda') }}"
                    />
                </div>
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="btn btn-secondary">
                    {{ __('Batal') }}
                </button>

                <button type="submit" class="btn btn-danger">
                    {{ __('Ya, Hapus Akun') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
