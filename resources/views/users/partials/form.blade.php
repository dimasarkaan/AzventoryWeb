<!-- Nama Lengkap -->
<div class="space-y-2">
    <label for="name" class="input-label">{{ __('ui.full_name') }} <span class="text-danger-500">*</span></label>
    <input id="name" data-testid="input-name" class="input-field" type="text" name="name" value="{{ old('name', $user->name ?? '') }}" autocomplete="name" pattern="^[a-zA-Z][a-zA-Z\s\.\'\-]*$" title="Nama lengkap harus diawali huruf dan hanya boleh berisi huruf, spasi, titik, koma atas, dan strip tanpa angka" minlength="3" maxlength="255" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

@if(isset($user))
<!-- Username (Read Only) -->
<div class="space-y-2">
    <label for="username_display" class="input-label">{{ __('ui.username') }}</label>
    <input type="text" id="username_display" class="input-field bg-secondary-50 text-secondary-500 cursor-not-allowed" value="{{ $user->username }}" disabled />
</div>
@endif

<!-- Email -->
<div class="space-y-2">
    <label for="email" class="input-label">{{ __('ui.email_address') }} <span class="text-danger-500">*</span></label>
    <input type="email" name="email" id="email" data-testid="input-email" class="input-field w-full" value="{{ old('email', $user->email ?? '') }}" autocomplete="email" pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$" title="Format email tidak valid (contoh: nama@domain.com)" minlength="5" maxlength="255" required>
    <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>

<!-- Jabatan -->
<div class="space-y-2">
    <label for="jabatan" class="input-label">{{ __('ui.job_position') }} <span class="text-danger-500">*</span></label>
    <input id="jabatan" data-testid="input-jabatan" class="input-field" type="text" name="jabatan" value="{{ old('jabatan', $user->jabatan ?? '') }}" autocomplete="organization-title" pattern="^(?=.*[a-zA-Z])[a-zA-Z0-9][a-zA-Z0-9\s\.\,\&\-\(\)\/\'&quot;]*$" title="Jabatan harus mengandung huruf, diawali huruf/angka, serta hanya berisi huruf/angka/spasi/simbol (.,&-()/'&quot;)" minlength="3" maxlength="255" required />
    <x-input-error :messages="$errors->get('jabatan')" class="mt-2" />
</div>

<!-- Role -->
<div class="space-y-2">
    <h3 class="lg:hidden text-lg font-bold text-secondary-900 border-b border-secondary-100 pb-2 mb-4 mt-2">{{ __('ui.access_job') }}</h3>
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
