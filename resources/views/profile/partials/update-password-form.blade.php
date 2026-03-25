<section x-data="{ isEditing: {{ $errors->updatePassword->any() ? 'true' : 'false' }}, isSubmitting: false }">
    <form method="post" action="{{ route('password.update') }}" @submit="isSubmitting = true" novalidate>
        @csrf
        @method('put')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6" x-show="isEditing" x-transition style="display: none;">
            <div class="md:col-span-2">
                <x-input-label for="update_password_current_password" :value="__('ui.profile_label_current_password')" />
                <x-text-input id="update_password_current_password" name="current_password" type="password" 
                              class="mt-1 block w-full {{ $errors->updatePassword->has('current_password') ? '!border-red-500' : '' }}" 
                              autocomplete="current-password" x-bind:disabled="!isEditing" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="update_password_password" :value="__('ui.profile_label_new_password')" />
                <x-text-input id="update_password_password" name="password" type="password" 
                              class="mt-1 block w-full {{ $errors->updatePassword->has('password') ? '!border-red-500' : '' }}" 
                              autocomplete="new-password" x-bind:disabled="!isEditing" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="update_password_password_confirmation" :value="__('ui.profile_label_confirm_password')" />
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" 
                              class="mt-1 block w-full {{ $errors->updatePassword->has('password_confirmation') ? '!border-red-500' : '' }}" 
                              autocomplete="new-password" x-bind:disabled="!isEditing" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center gap-4 transition-all" :class="isEditing ? 'pt-4 border-t border-secondary-100' : 'mt-4'">
            <!-- Normal Mode -->
            <button type="button" class="btn btn-secondary flex items-center gap-2" @click="isEditing = true; setTimeout(() => document.getElementById('update_password_current_password').focus(), 100)" x-show="!isEditing">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                Ganti Password
            </button>

            <!-- Edit Mode -->
            <div class="flex items-center gap-2" x-show="isEditing" style="display: none;">
                <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                    <span x-show="!isSubmitting">{{ __('ui.profile_btn_save_password') }}</span>
                    <span x-show="isSubmitting" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        {{ __('ui.saving') }}...
                    </span>
                </button>
                <button type="button" class="btn btn-ghost text-secondary-600" @click="isEditing = false">
                    {{ __('ui.cancel') }}
                </button>
            </div>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-success-600 bg-success-50 px-3 py-1 rounded-full border border-success-100"
                >
                    {{ __('ui.profile_password_success') }}
                </p>
            @endif
        </div>
    </form>
</section>
