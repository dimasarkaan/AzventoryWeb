<section x-data="{ isEditing: false }">
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-3" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 gap-x-6 gap-y-3 sm:grid-cols-6 user-select-none">
            <!-- Avatar -->
            <div class="sm:col-span-6">
                <label for="avatar" class="block text-sm font-medium text-secondary-700">{{ __('ui.profile_label_photo') }}</label>
                <div class="mt-2 flex items-center gap-x-3">
                    @if($user->avatar)
                        <img class="h-16 w-16 rounded-full object-cover ring-2 ring-secondary-200" src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" />
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
                            <label for="avatar" class="btn btn-secondary text-xs cursor-pointer" x-show="!avatarPreview">
                                {{ __('ui.profile_btn_select_photo') }}
                            </label>

                            <template x-if="avatarPreview">
                                <div class="flex items-center gap-2">
                                     <div class="relative group">
                                        <img :src="avatarPreview" class="h-10 w-10 object-cover rounded-full border border-secondary-200">
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
                           class="input-field w-full disabled:bg-gray-50 disabled:text-gray-500" 
                           value="{{ old('username', $user->username) }}" 
                           :disabled="!isEditing" required>
                    <p x-show="isEditing" x-transition class="mt-1 text-xs text-amber-600 font-medium">
                        Perhatian: Username hanya dapat diubah 1 kali. 
                    </p>
                    <x-input-error class="mt-2" :messages="$errors->get('username')" />
                @else
                    <div class="relative">
                        <input type="text" id="username" class="input-field w-full bg-secondary-50 text-secondary-500 cursor-not-allowed" value="{{ $user->username }}" disabled>
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
                 @if(auth()->user()->role === \App\Enums\UserRole::SUPERADMIN)
                    <input type="email" name="email" id="email" class="input-field w-full disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('email', $user->email) }}" :disabled="!isEditing" required>
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                 @else
                     <div class="relative">
                        <input type="email" id="email" class="input-field w-full bg-secondary-50 text-secondary-500 cursor-not-allowed" value="{{ $user->email }}" readonly>
                         <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                     </div>
                     <p class="mt-1 text-xs text-secondary-400">Hubungi admin untuk ganti email.</p>
                 @endif
            </div>

             <!-- Name -->
             <div class="sm:col-span-3">
                <label for="name" class="input-label">{{ __('ui.profile_label_name') }}</label>
                <input type="text" name="name" id="name" class="input-field w-full disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('name', $user->name) }}" :disabled="!isEditing" required>
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

             <!-- Phone -->
             <div class="sm:col-span-3">
                <label for="phone" class="input-label">{{ __('ui.profile_label_phone') }}</label>
                <input type="text" name="phone" id="phone" class="input-field w-full disabled:bg-gray-50 disabled:text-gray-500" value="{{ old('phone', $user->phone) }}" placeholder="{{ __('ui.profile_placeholder_phone') }}" :disabled="!isEditing">
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>

            <!-- Address -->
            <div class="sm:col-span-6">
                 <label for="address" class="input-label">{{ __('ui.profile_label_address') }}</label>
                 <textarea id="address" name="address" rows="3" class="input-field w-full disabled:bg-gray-50 disabled:text-gray-500" :disabled="!isEditing">{{ old('address', $user->address) }}</textarea>
                 <x-input-error class="mt-2" :messages="$errors->get('address')" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-2 border-t border-secondary-100">
            <!-- Edit Button -->
            <button type="button" class="btn btn-secondary flex items-center gap-2" @click="isEditing = true; setTimeout(() => document.getElementById('name').focus(), 100)" x-show="!isEditing">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                {{ __('ui.profile_btn_edit') }}
            </button>

            <!-- Save & Cancel Buttons -->
            <div class="flex items-center gap-2" x-show="isEditing" style="display: none;">
                <button type="submit" class="btn btn-primary">
                    {{ __('ui.profile_btn_save') }}
                </button>
                <button type="button" class="btn btn-ghost text-secondary-600" @click="isEditing = false">
                    {{ __('ui.cancel') }}
                </button>
            </div>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-success-600 bg-success-50 px-3 py-1 rounded-full border border-success-100"
                >
                    {{ __('ui.profile_save_success') }}
                </p>
            @endif
        </div>
    </form>
</section>
