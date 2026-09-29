<!-- Kiri: Informasi Akun -->
<div class="space-y-6">
    <div class="border-b border-secondary-100 pb-2 mb-4">
        <h2 class="text-lg font-bold text-secondary-900">{{ __('ui.account_info') }}</h2>
    </div>

    <!-- Nama Lengkap -->
    <div class="space-y-2">
        <label for="name" class="input-label">{{ __('ui.full_name') }} <span class="text-danger-500">*</span></label>
        <input id="name" data-testid="input-name" class="input-field" type="text" name="name" value="{{ old('name', $user->name ?? '') }}" placeholder="Contoh: Budi Santoso" autocomplete="name" pattern="^[a-zA-Z][a-zA-Z\s\.\'\-]*$" title="Nama lengkap harus diawali huruf dan hanya boleh berisi huruf, spasi, titik, koma atas, dan strip tanpa angka" minlength="3" maxlength="255" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    @if(isset($user))
    <!-- Username (Read Only) -->
    <div class="space-y-2">
        <label for="username_display" class="input-label">{{ __('ui.username') }}</label>
        <input type="text" id="username_display" class="input-field bg-secondary-50 text-secondary-500 cursor-not-allowed" value="{{ $user->username }}" disabled />
    </div>
    @else
    <!-- Username (Realtime Preview) -->
    <div class="space-y-2">
        <label class="input-label">{{ __('ui.username') }} <span class="text-secondary-400 text-xs font-normal">(Dibuat otomatis)</span></label>
        <div class="relative">
            <input type="text" class="input-field bg-primary-50 border-primary-200 text-primary-700 font-mono font-medium opacity-80 cursor-not-allowed" disabled :value="email.split('@')[0].replace(/[^a-zA-Z0-9]/g, '').toLowerCase()" />
            <div class="absolute right-3 top-2.5 text-primary-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
        </div>
    </div>
    @endif

    <!-- Email -->
    <div class="space-y-2">
        <label for="email" class="input-label">{{ __('ui.email_address') }} <span class="text-danger-500">*</span></label>
        <input type="email" name="email" id="email" data-testid="input-email" class="input-field w-full" x-model="email" value="{{ old('email', $user->email ?? '') }}" placeholder="Contoh: budi@contoh.com" autocomplete="email" pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$" title="Format email tidak valid (contoh: nama@domain.com)" minlength="5" maxlength="255" required>
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>
</div>

<!-- Kanan: Akses & Jabatan (Jabatan, Role, Status) -->
<div class="space-y-6">
    <div class="border-b border-secondary-100 pb-2 mb-4">
        <h2 class="text-lg font-bold text-secondary-900">{{ __('ui.access_job') }}</h2>
    </div>

    <!-- Jabatan -->
    <div class="space-y-2">
        <label for="jabatan" class="input-label">{{ __('ui.job_position') }} <span class="text-danger-500">*</span></label>
        <input id="jabatan" data-testid="input-jabatan" class="input-field" type="text" name="jabatan" value="{{ old('jabatan', $user->jabatan ?? '') }}" placeholder="Contoh: Staff IT / Supervisor" autocomplete="organization-title" pattern="^(?=.*[a-zA-Z])[a-zA-Z0-9][a-zA-Z0-9\s\.\,\&\-\(\)\/\'&quot;]*$" title="Jabatan harus mengandung huruf, diawali huruf/angka, serta hanya berisi huruf/angka/spasi/simbol (.,&-()/'&quot;)" minlength="3" maxlength="255" required />
        <x-input-error :messages="$errors->get('jabatan')" class="mt-2" />
    </div>

    <!-- Role -->
    <div class="space-y-2">
        <label for="role" class="input-label">{{ __('ui.access_role') }} <span class="text-danger-500">*</span></label>
        @php
            $roleOptions = [
                \App\Enums\UserRole::OPERATOR->value => __('ui.role_operator_desc'),
                \App\Enums\UserRole::ADMIN->value => __('ui.role_admin_desc'),
                \App\Enums\UserRole::SUPERADMIN->value => __('ui.role_superadmin_desc'),
            ];
        @endphp
        <x-select name="role" id="role" :options="$roleOptions" :selected="old('role', isset($user) ? $user->role->value : null)" placeholder="{{ __('ui.select_role') }}" width="w-full" />
        <x-input-error :messages="$errors->get('role')" class="mt-2" />
    </div>

    <!-- Status -->
    <div class="space-y-2">
        <label for="status" class="input-label">{{ __('ui.account_status') }} <span class="text-danger-500">*</span></label>
        @php
            $statusOptions = [
                'aktif' => __('ui.active'),
                'nonaktif' => __('ui.inactive'),
            ];
        @endphp
        <x-select name="status" id="status" :options="$statusOptions" :selected="old('status', $user->status ?? 'aktif')" placeholder="{{ __('ui.select_status') }}" width="w-full" />
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>

    @if(!isset($user))
    <!-- Info Box (Security Warning) -->
    <div class="bg-warning-50 border border-warning-200 rounded-lg p-4 flex items-start gap-3 mt-6">
        <svg class="w-5 h-5 text-warning-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <div class="text-sm text-warning-800">
            <p class="font-bold">{{ __('ui.default_system_info') }} {{ __('ui.security_warning') }}</p>
            <ul class="list-disc list-inside mt-2 space-y-1 text-warning-700">
                <li><strong>{{ __('ui.default_username_info') }}</strong></li>
                <li><strong>{{ __('ui.default_password_info') }}</strong> <code class="bg-warning-100 px-1.5 py-0.5 rounded text-warning-900 font-bold border border-warning-200">password123</code></li>
                <li class="mt-2 text-warning-900 text-xs font-semibold bg-warning-100 p-2 rounded-md border border-warning-200 block">{{ __('ui.default_password_warning') }}</li>
            </ul>
        </div>
    </div>
    @endif
</div>
