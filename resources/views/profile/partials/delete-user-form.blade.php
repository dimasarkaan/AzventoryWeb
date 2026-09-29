<section class="space-y-4" x-data="{}" 
         x-init="if ({{ session('error') || $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }}) { setTimeout(() => { $el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 300); }">
    <div class="flex items-start gap-4">
        <div class="flex-shrink-0">
             <div class="h-10 w-10 rounded-full bg-danger-100 flex items-center justify-center text-danger-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
        </div>
        <div>
            <h2 class="text-lg font-medium text-danger-900">{{ __('ui.profile_delete_warning_title') }}</h2>
             <p class="mt-1 text-sm text-secondary-600 leading-relaxed">
                {{ __('ui.profile_delete_warning_desc') }}
            </p>
             <div class="mt-4">
                 <button 
                    x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                    data-testid="btn-delete-account-trigger"
                    class="btn btn-danger"
                >
                    {{ __('ui.profile_btn_delete_account') }}
                </button>
            </div>
        </div>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form x-data="{ submitting: false }" @submit="submitting = true" method="post" action="{{ route('profile.destroy') }}" class="p-6" novalidate>
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
                <div class="w-full">
                    <x-password-input id="password" name="password" data-testid="input-delete-password"
                        class="input-field w-full {{ $errors->userDeletion->has('password') ? '!border-red-500' : '' }}" 
                        placeholder="{{ __('ui.profile_placeholder_password') }}" 
                        autocomplete="current-password" 
                        maxlength="255" />
                </div>
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="btn btn-secondary">
                    {{ __('ui.cancel') }}
                </button>

                <button type="submit" data-testid="btn-confirm-delete-account" class="btn btn-danger flex items-center gap-2" :class="{ 'opacity-75 cursor-not-allowed': submitting }" :disabled="submitting">
                    <svg x-show="submitting" x-cloak class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span x-text="submitting ? '{{ __('ui.saving') }}...' : '{{ __('ui.profile_btn_confirm_delete') }}'"></span>
                </button>
            </div>
        </form>
    </x-modal>
</section>


