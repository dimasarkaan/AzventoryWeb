<section class="space-y-4">
    <div class="flex items-start gap-4">
        <div class="flex-shrink-0">
             <div class="h-10 w-10 rounded-full bg-danger-100 flex items-center justify-center text-danger-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
        </div>
        <div>
            <h3 class="text-lg font-medium text-danger-900">{{ __('ui.profile_delete_warning_title') }}</h3>
             <p class="mt-1 text-sm text-secondary-600 leading-relaxed">
                {{ __('ui.profile_delete_warning_desc') }}
            </p>
             <div class="mt-4">
                 <button 
                    x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                    class="btn btn-danger"
                >
                    {{ __('ui.profile_btn_delete_account') }}
                </button>
            </div>
        </div>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6" novalidate>
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-secondary-900">
                {{ __('ui.profile_delete_confirm_title') }}
            </h2>

            <p class="mt-2 text-sm text-secondary-600">
                {{ __('ui.profile_delete_confirm_desc') }}
            </p>

            <div class="mt-6">
                <label for="password" class="sr-only">{{ __('ui.auth_label_password') }}</label>
                <div class="relative w-3/4" x-data="{ show: false }">
                    <input
                        id="password"
                        name="password"
                        x-bind:type="show ? 'text' : 'password'"
                        class="input-field w-full pr-10 {{ $errors->userDeletion->has('password') ? '!border-red-500' : '' }}"
                        placeholder="{{ __('ui.profile_placeholder_password') }}"
                        autocomplete="current-password"
                        maxlength="255"
                    />
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-secondary-400 hover:text-secondary-600 focus:outline-none" tabindex="-1">
                        <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="btn btn-secondary">
                    {{ __('ui.cancel') }}
                </button>

                <button type="submit" class="btn btn-danger">
                    {{ __('ui.profile_btn_confirm_delete') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>

