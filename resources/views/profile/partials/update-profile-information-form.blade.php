<section x-data="{ isEditing: false, isSubmitting: false }">
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-3" enctype="multipart/form-data" @submit="isSubmitting = true" novalidate>
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 gap-x-6 gap-y-3 sm:grid-cols-6 user-select-none mb-6" x-show="isEditing" x-transition style="display: none;">
            <!-- Avatar -->
            <div class="sm:col-span-6">
                <label for="avatar" class="block text-sm font-medium text-secondary-700">{{ __('ui.profile_label_photo') }}</label>
                <div class="mt-2 flex items-center gap-x-3">
                    @if($user->avatar)
                        <div class="relative group">
                            <img class="h-16 w-16 rounded-full object-cover ring-2 ring-secondary-200" src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" />
                            <button type="button" 
                                    x-show="isEditing"
                                    x-transition
                                    x-on:click.prevent="$dispatch('open-modal', 'confirm-avatar-deletion')"
                                    class="absolute -bottom-1 -right-1 bg-danger-500 text-white rounded-full p-1.5 shadow-lg hover:bg-danger-600 focus:outline-none focus:ring-2 ring-offset-2 ring-danger-500 transition-all" 
                                    title="Hapus Foto">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    @else
                        <div class="h-16 w-16 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold text-2xl ring-2 ring-secondary-200">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                    
                    <div class="relative" x-show="isEditing" x-transition x-data="{ avatarPreview: null, fileName: null }">
                        <!-- Hidden Input -->
                        <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*" x-ref="avatarInput"
                               @change="fileName = $event.target.files[0].name;
                                        const file = $event.target.files[0];
                                        const reader = new FileReader();
                                        reader.onload = (e) => { avatarPreview = e.target.result; };
                                        reader.readAsDataURL(file);">

                        <!-- Buttons & Preview -->
                         <div class="flex items-center gap-2">
                            <label for="avatar" class="btn btn-secondary text-xs cursor-pointer {{ $errors->has('avatar') ? '!border-red-500' : '' }}" x-show="!avatarPreview">
                                {{ __('ui.profile_btn_select_photo') }}
                            </label>

                            <template x-if="avatarPreview">
                                <div class="flex items-center gap-2">
                                     <div class="relative group">
                                        <img :src="avatarPreview" class="h-10 w-10 object-cover rounded-full border {{ $errors->has('avatar') ? 'border-red-500' : 'border-secondary-200' }}">
                                        <button type="button" @click="avatarPreview = null; fileName = null; $refs.avatarInput.value = ''"
                                                class="absolute -top-1 -right-1 bg-danger-500 text-white rounded-full p-0.5 shadow-md hover:bg-danger-600 focus:outline-none">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                     </div>
                                     <span class="text-xs text-secondary-500 truncate max-w-[150px]" x-text="fileName"></span>
                                </div>
                            </template>
                            
                             <span x-show="!fileName" class="text-xs text-secondary-500">{{ __('ui.profile_no_file') }}</span>
                         </div>
                    </div>
                </div>
                 <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            </div>

            <!-- Username -->
            <div class="sm:col-span-3">
                <label for="username" class="input-label">{{ __('ui.profile_label_username') }}</label>
                @if(!$user->is_username_changed)
                    <input type="text" name="username" id="username" 
                           class="input-field w-full disabled:bg-gray-50 disabled:text-gray-500 {{ $errors->has('username') ? '!border-red-500' : '' }}" 
                           value="{{ old('username', $user->username) }}" 
                           autocomplete="username"
                           x-bind:disabled="!isEditing">
                    <p x-show="isEditing" x-transition class="mt-1 text-xs text-amber-600 font-medium">
                        Perhatian: Username hanya dapat diubah 1 kali. 
                    </p>
                    <x-input-error class="mt-2" :messages="$errors->get('username')" />
                @else
                    <div class="relative">
                        <input type="text" id="username" class="input-field w-full bg-secondary-50 text-secondary-500 cursor-not-allowed" value="{{ $user->username }}" autocomplete="username" disabled>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                    </div>
                    <p x-show="isEditing" class="mt-1 text-xs text-secondary-400">{{ __('ui.profile_username_locked') }}</p>
                @endif
            </div>

            <!-- Email -->
            <div class="sm:col-span-3">
                 <label for="email" class="input-label">{{ __('ui.profile_label_email') }}</label>
                @can('update', $user)
                    <input type="email" name="email" id="email" class="input-field w-full disabled:bg-gray-50 disabled:text-gray-500 {{ $errors->has('email') ? '!border-red-500' : '' }}" value="{{ old('email', $user->email) }}" autocomplete="email" x-bind:disabled="!isEditing">
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                @else
                     <div class="relative">
                        <input type="email" name="email" id="email" class="input-field w-full bg-secondary-50 text-secondary-500 cursor-not-allowed" value="{{ $user->email }}" autocomplete="email" readonly>
                         <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                     </div>
                     <p class="mt-1 text-xs text-secondary-400">Hubungi superadmin untuk ganti email.</p>
                @endcan
            </div>

             <!-- Name -->
             <div class="sm:col-span-3">
                <label for="name" class="input-label">{{ __('ui.profile_label_name') }}</label>
                <input type="text" name="name" id="name" class="input-field w-full disabled:bg-gray-50 disabled:text-gray-500 {{ $errors->has('name') ? '!border-red-500' : '' }}" value="{{ old('name', $user->name) }}" autocomplete="name" x-bind:disabled="!isEditing">
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

             <!-- Phone -->
             <div class="sm:col-span-3">
                <label for="phone" class="input-label">{{ __('ui.profile_label_phone') }}</label>
                <input type="text" name="phone" id="phone" class="input-field w-full disabled:bg-gray-50 disabled:text-gray-500 {{ $errors->has('phone') ? '!border-red-500' : '' }}" value="{{ old('phone', $user->phone) }}" placeholder="{{ __('ui.profile_placeholder_phone') }}" autocomplete="tel" x-bind:disabled="!isEditing">
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>

            <!-- Address -->
            <div class="sm:col-span-6">
                 <label for="address" class="input-label">{{ __('ui.profile_label_address') }}</label>
                 <textarea id="address" name="address" rows="3" class="input-field w-full disabled:bg-gray-50 disabled:text-gray-500 {{ $errors->has('address') ? '!border-red-500' : '' }}" autocomplete="street-address" x-bind:disabled="!isEditing">{{ old('address', $user->address) }}</textarea>
                 <x-input-error class="mt-2" :messages="$errors->get('address')" />
            </div>
        </div>

        <div class="flex items-center gap-4 transition-all" :class="isEditing ? 'pt-4 border-t border-secondary-100' : 'mt-4'">
            <!-- Edit Button -->
            <button type="button" class="btn btn-secondary flex items-center gap-2" @click="isEditing = true; setTimeout(() => document.getElementById('name').focus(), 100)" x-show="!isEditing">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                {{ __('ui.profile_btn_edit') }}
            </button>

            <!-- Save & Cancel Buttons -->
            <div class="flex items-center gap-2" x-show="isEditing" style="display: none;">
                <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                    <span x-show="!isSubmitting">{{ __('ui.profile_btn_save') }}</span>
                    <span x-show="isSubmitting" class="flex items-center gap-2">
                         <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        {{ __('ui.saving') }}...
                    </span>
                </button>
                <button type="button" class="btn btn-ghost text-secondary-600" @click="isEditing = false">
                    {{ __('ui.cancel') }}
                </button>
            </div>

            @if (session('status') === 'profile-updated' || session('status') === 'avatar-deleted')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-success-600 bg-success-50 px-3 py-1 rounded-full border border-success-100"
                >
                    {{ session('status') === 'avatar-deleted' ? 'Foto profil berhasil dihapus.' : __('ui.profile_save_success') }}
                </p>
            @endif
        </div>
    </form>

    <form id="delete-avatar-form" method="POST" action="{{ route('profile.avatar.delete') }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <x-modal name="confirm-avatar-deletion" focusable>
        <div class="p-6">
            <h2 class="text-lg font-bold text-secondary-900">
                Hapus Foto Profil?
            </h2>

            <p class="mt-2 text-sm text-secondary-600">
                Apakah Anda yakin ingin menghapus foto profil saat ini? Tindakan ini akan mengembalikan foto profil Anda ke inisial nama.
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="btn btn-secondary">
                    {{ __('ui.cancel') }}
                </button>

                <button type="button" 
                        class="btn btn-danger"
                        onclick="document.getElementById('delete-avatar-form').submit();">
                    Hapus
                </button>
            </div>
        </div>
    </x-modal>
</section>
