<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-3" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 gap-x-6 gap-y-3 sm:grid-cols-6">
            <!-- Avatar -->
            <div class="sm:col-span-6">
                <label for="avatar" class="block text-sm font-medium text-secondary-700">Foto Profil</label>
                <div class="mt-2 flex items-center gap-x-3">
                    @if($user->avatar)
                        <img class="h-16 w-16 rounded-full object-cover ring-2 ring-secondary-200" src="{{ $user->avatar }}" alt="{{ $user->name }}" />
                    @else
                        <div class="h-16 w-16 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold text-2xl ring-2 ring-secondary-200">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                    
                    <div class="relative">
                        <input type="file" id="avatar" name="avatar" class="hidden" onchange="document.getElementById('file-chosen').textContent = this.files[0].name" accept="image/*">
                        <label for="avatar" class="btn btn-secondary text-xs cursor-pointer">
                            Pilih Foto Baru
                        </label>
                        <span id="file-chosen" class="ml-2 text-xs text-secondary-500">Tidak ada file dipilih</span>
                    </div>
                </div>
                 <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
            </div>

            <!-- Username (Read Only) -->
             <div class="sm:col-span-3">
                <label for="username" class="input-label">Username</label>
                <div class="relative">
                    <input type="text" id="username" class="input-field w-full bg-secondary-50 text-secondary-500 cursor-not-allowed" value="{{ $user->username }}" disabled>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                </div>
                <p class="mt-1 text-xs text-secondary-400">Username bersifat permanen.</p>
            </div>

            <!-- Email (Read Only) -->
            <div class="sm:col-span-3">
                 <label for="email" class="input-label">Email</label>
                 <div class="relative">
                    <input type="email" id="email" class="input-field w-full bg-secondary-50 text-secondary-500 cursor-not-allowed" value="{{ $user->email }}" readonly>
                     <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                 </div>
                 <p class="mt-1 text-xs text-secondary-400">Hubungi admin untuk ganti email.</p>
                 <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

             <!-- Name -->
             <div class="sm:col-span-3">
                <label for="name" class="input-label">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="input-field w-full" value="{{ old('name', $user->name) }}" required autofocus>
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

             <!-- Phone -->
             <div class="sm:col-span-3">
                <label for="phone" class="input-label">No. WhatsApp</label>
                <input type="text" name="phone" id="phone" class="input-field w-full" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 08123456789">
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>

            <!-- Address -->
            <div class="sm:col-span-6">
                 <label for="address" class="input-label">Alamat</label>
                 <textarea id="address" name="address" rows="3" class="input-field w-full">{{ old('address', $user->address) }}</textarea>
                 <x-input-error class="mt-2" :messages="$errors->get('address')" />
            </div>
        </div>

        <div class="flex items-center gap-4 pt-2 border-t border-secondary-100">
            <button type="submit" class="btn btn-primary">
                {{ __('Simpan Perubahan') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-success-600 bg-success-50 px-3 py-1 rounded-full border border-success-100"
                >
                    {{ __('Data berhasil disimpan.') }}
                </p>
            @endif
        </div>
    </form>
</section>
