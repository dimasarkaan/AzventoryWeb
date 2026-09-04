<section x-data="{ 
    isEditing: {{ $errors->has('token_name') ? 'true' : 'false' }}, 
    isSubmitting: false,
    tokenIdToDelete: null, 
    tokenNameToDelete: '',
    tokenOwnerToDelete: '',
    preset: '{{ old('preset_select', 'custom') }}',
    abilities: {
        'inventory:read': {{ in_array('inventory:read', old('abilities', [])) ? 'true' : 'false' }}, 
        'inventory:create': {{ in_array('inventory:create', old('abilities', [])) ? 'true' : 'false' }}, 
        'inventory:update': {{ in_array('inventory:update', old('abilities', [])) ? 'true' : 'false' }}, 
        'inventory:delete': {{ in_array('inventory:delete', old('abilities', [])) ? 'true' : 'false' }},
        'borrowing:read': {{ in_array('borrowing:read', old('abilities', [])) ? 'true' : 'false' }}, 
        'borrowing:create': {{ in_array('borrowing:create', old('abilities', [])) ? 'true' : 'false' }}, 
        'borrowing:update': {{ in_array('borrowing:update', old('abilities', [])) ? 'true' : 'false' }},
        'category:read': {{ in_array('category:read', old('abilities', [])) ? 'true' : 'false' }}, 
        'category:create': {{ in_array('category:create', old('abilities', [])) ? 'true' : 'false' }}, 
        'category:update': {{ in_array('category:update', old('abilities', [])) ? 'true' : 'false' }}, 
        'category:delete': {{ in_array('category:delete', old('abilities', [])) ? 'true' : 'false' }},
        'brand:read': {{ in_array('brand:read', old('abilities', [])) ? 'true' : 'false' }}, 
        'brand:create': {{ in_array('brand:create', old('abilities', [])) ? 'true' : 'false' }}, 
        'brand:update': {{ in_array('brand:update', old('abilities', [])) ? 'true' : 'false' }}, 
        'brand:delete': {{ in_array('brand:delete', old('abilities', [])) ? 'true' : 'false' }},
        'location:read': {{ in_array('location:read', old('abilities', [])) ? 'true' : 'false' }}, 
        'location:create': {{ in_array('location:create', old('abilities', [])) ? 'true' : 'false' }}, 
        'location:update': {{ in_array('location:update', old('abilities', [])) ? 'true' : 'false' }}, 
        'location:delete': {{ in_array('location:delete', old('abilities', [])) ? 'true' : 'false' }},
        'user:read': {{ in_array('user:read', old('abilities', [])) ? 'true' : 'false' }}, 
        'user:create': {{ in_array('user:create', old('abilities', [])) ? 'true' : 'false' }}, 
        'user:update': {{ in_array('user:update', old('abilities', [])) ? 'true' : 'false' }}, 
        'user:delete': {{ in_array('user:delete', old('abilities', [])) ? 'true' : 'false' }},
        'log:read': {{ in_array('log:read', old('abilities', [])) ? 'true' : 'false' }}
    },
    setPreset() {
        if (this.preset === 'superadmin') {
            for (const key in this.abilities) this.abilities[key] = true;
        } else if (this.preset === 'admin') {
            for (const key in this.abilities) this.abilities[key] = false;
            ['inventory:read', 'inventory:create', 'inventory:update', 'borrowing:read', 'borrowing:create', 'borrowing:update', 'category:read', 'category:create', 'brand:read', 'brand:create', 'location:read', 'location:create', 'log:read'].forEach(k => this.abilities[k] = true);
        } else if (this.preset === 'operator') {
            for (const key in this.abilities) this.abilities[key] = false;
            ['inventory:read', 'borrowing:read', 'borrowing:create', 'category:read', 'brand:read', 'location:read'].forEach(k => this.abilities[k] = true);
        }
    },
    checkPresetMatch() {
        const adminAbilities = ['inventory:read', 'inventory:create', 'inventory:update', 'borrowing:read', 'borrowing:create', 'borrowing:update', 'category:read', 'category:create', 'brand:read', 'brand:create', 'location:read', 'location:create', 'log:read'];
        const operatorAbilities = ['inventory:read', 'borrowing:read', 'borrowing:create', 'category:read', 'brand:read', 'location:read'];

        let isSuperadmin = true;
        let isAdmin = true;
        let isOperator = true;

        for (const key in this.abilities) {
            if (!this.abilities[key]) isSuperadmin = false;
            
            if (adminAbilities.includes(key)) {
                if (!this.abilities[key]) isAdmin = false;
            } else {
                if (this.abilities[key]) isAdmin = false;
            }
            
            if (operatorAbilities.includes(key)) {
                if (!this.abilities[key]) isOperator = false;
            } else {
                if (this.abilities[key]) isOperator = false;
            }
        }

        let matchedPreset = 'custom';
        if (isSuperadmin) matchedPreset = 'superadmin';
        else if (isAdmin) matchedPreset = 'admin';
        else if (isOperator) matchedPreset = 'operator';

        this.preset = matchedPreset;
        this.$dispatch('set-selected', {name: 'preset_select', value: matchedPreset});
    },
    selectAll() {
        for (const key in this.abilities) this.abilities[key] = true;
        this.checkPresetMatch();
    },
    resetAll() {
        for (const key in this.abilities) this.abilities[key] = false;
        this.checkPresetMatch();
    }
}"
         x-init="if ({{ session('new_api_token') || session('api_token_deleted') || $errors->has('token_name') ? 'true' : 'false' }}) { setTimeout(() => { $el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }, 300); }">

    @if (session('new_api_token'))
        <div class="mt-4 p-4 border border-success-200 bg-success-50 rounded-lg shadow-sm" x-data="{ copied: false }">
            <p class="text-sm font-bold text-success-800 mb-2">Token Berhasil Dibuat!</p>
            <div class="flex items-center gap-2">
                <code id="new-api-token" class="px-3 py-2 bg-white border border-success-300 rounded-lg font-mono text-sm font-semibold break-all w-full select-all text-success-900">{{ session('new_api_token') }}</code>
                <button type="button" 
                        @click="navigator.clipboard.writeText('{{ session('new_api_token') }}'); copied = true; setTimeout(() => copied = false, 2000)"
                        class="btn btn-success py-2 px-3 flex items-center gap-2 shrink-0">
                    <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                    <svg x-show="copied" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                </button>
            </div>
            <p class="mt-2 text-xs font-medium text-success-700">Harap salin token ini sekarang. Anda tidak akan bisa melihatnya lagi setelah memuat ulang halaman.</p>
        </div>
    @endif

    <!-- Tombol untuk membuka form (Mode Normal) -->
    <div x-show="!isEditing" class="flex items-center gap-4 mt-4">
        <button type="button" @click="isEditing = true; $nextTick(() => $refs.tokenNameInput.focus())" class="btn btn-secondary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            {{ __('Buat Token Baru') }}
        </button>
    </div>

    <!-- Form Pembuatan Token (Mode Edit) -->
    <form x-show="isEditing" 
          x-transition:enter="transition ease-out duration-300"
          x-transition:enter-start="opacity-0 transform -translate-y-4"
          x-transition:enter-end="opacity-100 transform translate-y-0"
          x-transition:leave="transition ease-in duration-200"
          x-transition:leave-start="opacity-100 transform translate-y-0"
          x-transition:leave-end="opacity-0 transform -translate-y-4"
          method="post" action="{{ route('profile.api-tokens.store') }}" @submit="isSubmitting = true" class="space-y-4" x-cloak novalidate>
        @csrf

        <div>
            <x-input-label for="token_name" :value="__('Nama Perangkat / Integrasi')" />
            <x-text-input x-ref="tokenNameInput" id="token_name" name="token_name" type="text" 
                          class="mt-1 block w-full sm:w-1/2 {{ $errors->has('token_name') ? '!border-red-500' : '' }}" 
                          :value="old('token_name')"
                          placeholder="Contoh: Web Ecommerce Utama" />
            <x-input-error class="mt-2" :messages="$errors->get('token_name')" />
            <p class="text-xs text-secondary-500 mt-1">Beri nama yang jelas agar Anda mudah mengenalinya.</p>
        </div>

        <div class="mt-4 border border-secondary-200 rounded-lg overflow-hidden">
            <div class="bg-secondary-50 p-3 border-b border-secondary-200 flex sm:flex-row flex-col justify-between sm:items-center gap-3">
                <div>
                    <label class="text-sm font-bold text-secondary-900">Hak Akses Modul (Abilities)</label>
                    <p class="text-xs text-secondary-500">Pilih aksi spesifik yang diizinkan untuk token ini.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="selectAll()" class="text-xs text-primary-600 hover:underline font-bold whitespace-nowrap">Pilih Semua</button>
                    <button type="button" @click="resetAll()" class="text-xs text-danger-600 hover:underline font-bold whitespace-nowrap mr-2">Bersihkan</button>
                    <div x-on:selected="if($event.detail) { preset = $event.detail; setPreset() }">
                        <x-select name="preset_select" 
                                  :options="['custom' => '-- Preset Cepat (Role) --', 'superadmin' => 'Setara Superadmin', 'admin' => 'Setara Admin', 'operator' => 'Setara Operator']" 
                                  selected="{{ old('preset_select', 'custom') }}" 
                                  width="w-full sm:w-auto" 
                                  :allowClear="false" />
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-white border-b border-secondary-200 text-secondary-600">
                        <tr>
                            <th class="px-4 py-2 font-semibold">Modul</th>
                            <th class="px-4 py-2 font-semibold text-center">Lihat</th>
                            <th class="px-4 py-2 font-semibold text-center">Buat</th>
                            <th class="px-4 py-2 font-semibold text-center">Edit</th>
                            <th class="px-4 py-2 font-semibold text-center">Hapus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-secondary-100 bg-white">
                        <!-- Inventaris -->
                        <tr class="hover:bg-secondary-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-secondary-900">Inventaris & Stok</td>
                            <td @click="abilities['inventory:read'] = !abilities['inventory:read']; checkPresetMatch()" class="px-4 py-3 text-center cursor-pointer hover:bg-primary-50 transition-colors" :class="{ 'bg-primary-50': abilities['inventory:read'] }"><input @click.stop type="checkbox" name="abilities[]" value="inventory:read" x-model="abilities['inventory:read']" @change="checkPresetMatch()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"></td>
                            <td @click="abilities['inventory:create'] = !abilities['inventory:create']; checkPresetMatch()" class="px-4 py-3 text-center cursor-pointer hover:bg-primary-50 transition-colors" :class="{ 'bg-primary-50': abilities['inventory:create'] }"><input @click.stop type="checkbox" name="abilities[]" value="inventory:create" x-model="abilities['inventory:create']" @change="checkPresetMatch()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"></td>
                            <td @click="abilities['inventory:update'] = !abilities['inventory:update']; checkPresetMatch()" class="px-4 py-3 text-center cursor-pointer hover:bg-primary-50 transition-colors" :class="{ 'bg-primary-50': abilities['inventory:update'] }"><input @click.stop type="checkbox" name="abilities[]" value="inventory:update" x-model="abilities['inventory:update']" @change="checkPresetMatch()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"></td>
                            <td @click="abilities['inventory:delete'] = !abilities['inventory:delete']; checkPresetMatch()" class="px-4 py-3 text-center cursor-pointer hover:bg-primary-50 transition-colors" :class="{ 'bg-primary-50': abilities['inventory:delete'] }"><input @click.stop type="checkbox" name="abilities[]" value="inventory:delete" x-model="abilities['inventory:delete']" @change="checkPresetMatch()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"></td>
                        </tr>
                        <!-- Peminjaman -->
                        <tr class="hover:bg-secondary-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-secondary-900">Peminjaman</td>
                            <td @click="abilities['borrowing:read'] = !abilities['borrowing:read']; checkPresetMatch()" class="px-4 py-3 text-center cursor-pointer hover:bg-primary-50 transition-colors" :class="{ 'bg-primary-50': abilities['borrowing:read'] }"><input @click.stop type="checkbox" name="abilities[]" value="borrowing:read" x-model="abilities['borrowing:read']" @change="checkPresetMatch()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"></td>
                            <td @click="abilities['borrowing:create'] = !abilities['borrowing:create']; checkPresetMatch()" class="px-4 py-3 text-center cursor-pointer hover:bg-primary-50 transition-colors" :class="{ 'bg-primary-50': abilities['borrowing:create'] }"><input @click.stop type="checkbox" name="abilities[]" value="borrowing:create" x-model="abilities['borrowing:create']" @change="checkPresetMatch()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"></td>
                            <td @click="abilities['borrowing:update'] = !abilities['borrowing:update']; checkPresetMatch()" class="px-4 py-3 text-center cursor-pointer hover:bg-primary-50 transition-colors" :class="{ 'bg-primary-50': abilities['borrowing:update'] }"><input @click.stop type="checkbox" name="abilities[]" value="borrowing:update" x-model="abilities['borrowing:update']" @change="checkPresetMatch()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"></td>
                            <td class="px-4 py-3 text-center"><span class="text-secondary-300 text-xs italic">-</span></td>
                        </tr>
                        <!-- Kategori -->
                        <tr class="hover:bg-secondary-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-secondary-900">Master Kategori</td>
                            <td @click="abilities['category:read'] = !abilities['category:read']; checkPresetMatch()" class="px-4 py-3 text-center cursor-pointer hover:bg-primary-50 transition-colors" :class="{ 'bg-primary-50': abilities['category:read'] }"><input @click.stop type="checkbox" name="abilities[]" value="category:read" x-model="abilities['category:read']" @change="checkPresetMatch()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"></td>
                            <td @click="abilities['category:create'] = !abilities['category:create']; checkPresetMatch()" class="px-4 py-3 text-center cursor-pointer hover:bg-primary-50 transition-colors" :class="{ 'bg-primary-50': abilities['category:create'] }"><input @click.stop type="checkbox" name="abilities[]" value="category:create" x-model="abilities['category:create']" @change="checkPresetMatch()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"></td>
                            <td @click="abilities['category:update'] = !abilities['category:update']; checkPresetMatch()" class="px-4 py-3 text-center cursor-pointer hover:bg-primary-50 transition-colors" :class="{ 'bg-primary-50': abilities['category:update'] }"><input @click.stop type="checkbox" name="abilities[]" value="category:update" x-model="abilities['category:update']" @change="checkPresetMatch()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"></td>
                            <td @click="abilities['category:delete'] = !abilities['category:delete']; checkPresetMatch()" class="px-4 py-3 text-center cursor-pointer hover:bg-primary-50 transition-colors" :class="{ 'bg-primary-50': abilities['category:delete'] }"><input @click.stop type="checkbox" name="abilities[]" value="category:delete" x-model="abilities['category:delete']" @change="checkPresetMatch()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"></td>
                        </tr>
                        <!-- Merk -->
                        <tr class="hover:bg-secondary-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-secondary-900">Master Merk</td>
                            <td @click="abilities['brand:read'] = !abilities['brand:read']; checkPresetMatch()" class="px-4 py-3 text-center cursor-pointer hover:bg-primary-50 transition-colors" :class="{ 'bg-primary-50': abilities['brand:read'] }"><input @click.stop type="checkbox" name="abilities[]" value="brand:read" x-model="abilities['brand:read']" @change="checkPresetMatch()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"></td>
                            <td @click="abilities['brand:create'] = !abilities['brand:create']; checkPresetMatch()" class="px-4 py-3 text-center cursor-pointer hover:bg-primary-50 transition-colors" :class="{ 'bg-primary-50': abilities['brand:create'] }"><input @click.stop type="checkbox" name="abilities[]" value="brand:create" x-model="abilities['brand:create']" @change="checkPresetMatch()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"></td>
                            <td @click="abilities['brand:update'] = !abilities['brand:update']; checkPresetMatch()" class="px-4 py-3 text-center cursor-pointer hover:bg-primary-50 transition-colors" :class="{ 'bg-primary-50': abilities['brand:update'] }"><input @click.stop type="checkbox" name="abilities[]" value="brand:update" x-model="abilities['brand:update']" @change="checkPresetMatch()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"></td>
                            <td @click="abilities['brand:delete'] = !abilities['brand:delete']; checkPresetMatch()" class="px-4 py-3 text-center cursor-pointer hover:bg-primary-50 transition-colors" :class="{ 'bg-primary-50': abilities['brand:delete'] }"><input @click.stop type="checkbox" name="abilities[]" value="brand:delete" x-model="abilities['brand:delete']" @change="checkPresetMatch()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"></td>
                        </tr>
                        <!-- Lokasi -->
                        <tr class="hover:bg-secondary-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-secondary-900">Master Lokasi</td>
                            <td @click="abilities['location:read'] = !abilities['location:read']; checkPresetMatch()" class="px-4 py-3 text-center cursor-pointer hover:bg-primary-50 transition-colors" :class="{ 'bg-primary-50': abilities['location:read'] }"><input @click.stop type="checkbox" name="abilities[]" value="location:read" x-model="abilities['location:read']" @change="checkPresetMatch()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"></td>
                            <td @click="abilities['location:create'] = !abilities['location:create']; checkPresetMatch()" class="px-4 py-3 text-center cursor-pointer hover:bg-primary-50 transition-colors" :class="{ 'bg-primary-50': abilities['location:create'] }"><input @click.stop type="checkbox" name="abilities[]" value="location:create" x-model="abilities['location:create']" @change="checkPresetMatch()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"></td>
                            <td @click="abilities['location:update'] = !abilities['location:update']; checkPresetMatch()" class="px-4 py-3 text-center cursor-pointer hover:bg-primary-50 transition-colors" :class="{ 'bg-primary-50': abilities['location:update'] }"><input @click.stop type="checkbox" name="abilities[]" value="location:update" x-model="abilities['location:update']" @change="checkPresetMatch()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"></td>
                            <td @click="abilities['location:delete'] = !abilities['location:delete']; checkPresetMatch()" class="px-4 py-3 text-center cursor-pointer hover:bg-primary-50 transition-colors" :class="{ 'bg-primary-50': abilities['location:delete'] }"><input @click.stop type="checkbox" name="abilities[]" value="location:delete" x-model="abilities['location:delete']" @change="checkPresetMatch()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"></td>
                        </tr>
                        <!-- Manajemen User -->
                        <tr class="hover:bg-secondary-50 transition-colors bg-danger-50/30">
                            <td class="px-4 py-3 font-medium text-danger-900">Manajemen User (Sensitif)</td>
                            <td class="px-4 py-3 text-center"><input type="checkbox" name="abilities[]" value="user:read" x-model="abilities['user:read']" @change="checkPresetMatch()" class="rounded border-danger-300 text-danger-600 focus:ring-danger-500"></td>
                            <td class="px-4 py-3 text-center"><input type="checkbox" name="abilities[]" value="user:create" x-model="abilities['user:create']" @change="checkPresetMatch()" class="rounded border-danger-300 text-danger-600 focus:ring-danger-500"></td>
                            <td class="px-4 py-3 text-center"><input type="checkbox" name="abilities[]" value="user:update" x-model="abilities['user:update']" @change="checkPresetMatch()" class="rounded border-danger-300 text-danger-600 focus:ring-danger-500"></td>
                            <td class="px-4 py-3 text-center"><input type="checkbox" name="abilities[]" value="user:delete" x-model="abilities['user:delete']" @change="checkPresetMatch()" class="rounded border-danger-300 text-danger-600 focus:ring-danger-500"></td>
                        </tr>
                        <!-- Log Sistem -->
                        <tr class="hover:bg-secondary-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-secondary-900">Log Aktivitas & Stats</td>
                            <td @click="abilities['log:read'] = !abilities['log:read']; checkPresetMatch()" class="px-4 py-3 text-center cursor-pointer hover:bg-primary-50 transition-colors" :class="{ 'bg-primary-50': abilities['log:read'] }"><input @click.stop type="checkbox" name="abilities[]" value="log:read" x-model="abilities['log:read']" @change="checkPresetMatch()" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"></td>
                            <td class="px-4 py-3 text-center"><span class="text-secondary-300 text-xs italic">-</span></td>
                            <td class="px-4 py-3 text-center"><span class="text-secondary-300 text-xs italic">-</span></td>
                            <td class="px-4 py-3 text-center"><span class="text-secondary-300 text-xs italic">-</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <x-input-error class="m-3" :messages="$errors->get('abilities')" />
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-secondary-100">
            <button type="submit" class="btn btn-primary flex items-center gap-2" :disabled="Object.values(abilities).filter(Boolean).length === 0 || isSubmitting" :class="{ 'opacity-50 cursor-not-allowed': Object.values(abilities).filter(Boolean).length === 0 || isSubmitting }">
                <span x-show="!isSubmitting">{{ __('Generate Token') }}</span>
                <span x-show="isSubmitting" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Memproses...
                </span>
            </button>
            <button type="button" @click="isEditing = false" class="btn btn-ghost text-secondary-600">
                {{ __('ui.cancel') }}
            </button>
        </div>
    </form>

    @php
        $tokensList = (isset($allTokens) && count($allTokens) > 0) ? $allTokens : $user->tokens;
    @endphp

    @if ($tokensList->isNotEmpty())
        <div class="mt-4 pt-4 border-t border-secondary-200">
            <header class="mb-4">
                <h2 class="text-lg font-medium text-secondary-900">
                    {{ (isset($allTokens) && count($allTokens) > 0) ? 'Daftar Semua Token Aktif di Sistem' : 'Daftar Token Aktif' }}
                </h2>
                <p class="mt-1 text-sm text-secondary-600">
                    {{ __('Token di bawah ini sedang memiliki izin akses ke sistem API. Jika ada integrasi yang sudah tidak dipakai, segera cabut aksesnya.') }}
                </p>
            </header>

            <div class="space-y-3">
                @foreach ($tokensList as $token)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 bg-white rounded-lg border border-secondary-200 shadow-sm hover:border-primary-300 transition-colors">
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-secondary-900">{{ $token->name }}</p>
                                @if($token->expires_at && \Carbon\Carbon::parse($token->expires_at)->isPast())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-danger-100 text-danger-800">
                                        Kedaluwarsa
                                    </span>
                                @elseif($token->last_used_at && \Carbon\Carbon::parse($token->last_used_at)->diffInDays(now()) < 7)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-100 text-primary-800">
                                        Aktif
                                    </span>
                                @elseif(!$token->last_used_at)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-success-100 text-success-800">
                                        Baru
                                    </span>
                                @endif
                            </div>
                            @if(isset($allTokens) && count($allTokens) > 0)
                                <p class="text-xs text-secondary-700 mt-1 font-medium">Pemilik: {{ $token->tokenable->name ?? 'Sistem' }}</p>
                            @endif
                            <div x-data="{ expandedAbilities: false }" class="mt-1.5 flex flex-wrap gap-1">
                                @php
                                    $abilities = $token->abilities ?? [];
                                    $displayAbilities = array_slice($abilities, 0, 4);
                                    $remainingAbilities = array_slice($abilities, 4);
                                    $remainingCount = count($remainingAbilities);
                                    
                                    $abilityNames = [
                                        'inventory:read' => 'Lihat Inventaris', 'inventory:create' => 'Buat Inventaris', 'inventory:update' => 'Edit Inventaris', 'inventory:delete' => 'Hapus Inventaris',
                                        'borrowing:read' => 'Lihat Pinjam', 'borrowing:create' => 'Buat Pinjam', 'borrowing:update' => 'Edit Pinjam',
                                        'category:read' => 'Lihat Kategori', 'category:create' => 'Buat Kategori', 'category:update' => 'Edit Kategori', 'category:delete' => 'Hapus Kategori',
                                        'brand:read' => 'Lihat Merk', 'brand:create' => 'Buat Merk', 'brand:update' => 'Edit Merk', 'brand:delete' => 'Hapus Merk',
                                        'location:read' => 'Lihat Lokasi', 'location:create' => 'Buat Lokasi', 'location:update' => 'Edit Lokasi', 'location:delete' => 'Hapus Lokasi',
                                        'user:read' => 'Lihat User', 'user:create' => 'Buat User', 'user:update' => 'Edit User', 'user:delete' => 'Hapus User',
                                        'log:read' => 'Lihat Log'
                                    ];
                                @endphp
                                @foreach($displayAbilities as $ability)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-secondary-100 text-secondary-700 border border-secondary-200" title="{{ $ability }}">
                                        {{ $abilityNames[$ability] ?? $ability }}
                                    </span>
                                @endforeach

                                @if($remainingCount > 0)
                                    <span x-show="!expandedAbilities" @click="expandedAbilities = true" class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-secondary-100 text-secondary-700 border border-secondary-200 cursor-pointer hover:bg-secondary-200 transition-colors">
                                        +{{ $remainingCount }} lainnya
                                    </span>

                                    <template x-if="expandedAbilities">
                                        <div class="contents">
                                            @foreach($remainingAbilities as $ability)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-secondary-100 text-secondary-700 border border-secondary-200" title="{{ $ability }}">
                                                    {{ $abilityNames[$ability] ?? $ability }}
                                                </span>
                                            @endforeach
                                            <span @click="expandedAbilities = false" class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-secondary-100 text-secondary-700 border border-secondary-200 cursor-pointer hover:bg-secondary-200 transition-colors">
                                                Sembunyikan
                                            </span>
                                        </div>
                                    </template>
                                @endif
                            </div>
                            <p class="text-xs text-secondary-500 mt-2">
                                Dibuat: {{ $token->created_at->format('d M Y, H:i') }}
                                @if($token->expires_at)
                                    &bull; Kedaluwarsa: {{ \Carbon\Carbon::parse($token->expires_at)->format('d M Y') }}
                                @endif
                                <br>
                                @if($token->last_used_at)
                                    Status: Terakhir digunakan {{ \Carbon\Carbon::parse($token->last_used_at)->diffForHumans() }}
                                @else
                                    Status: Belum pernah digunakan
                                @endif
                            </p>
                        </div>
                        <div class="shrink-0 flex gap-2">
                            <button type="button" 
                                    @click="$dispatch('open-edit-modal', { id: {{ $token->id }}, name: '{{ addslashes($token->name) }}', abilities: {{ json_encode($token->abilities ?? []) }} })"
                                    class="btn btn-secondary py-1.5 px-3 text-sm flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                Edit Akses
                            </button>
                            <button type="button" 
                                    @click="tokenIdToDelete = {{ $token->id }}; tokenNameToDelete = '{{ addslashes($token->name) }}'; tokenOwnerToDelete = '{{ addslashes($token->tokenable->name ?? 'Sistem') }}'; $dispatch('open-modal', 'confirm-token-revocation')"
                                    class="btn btn-danger py-1.5 px-3 text-sm flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Cabut Akses
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="mt-4 pt-4 border-t border-secondary-200">
            <header class="mb-4">
                <h2 class="text-lg font-medium text-secondary-900">
                    {{ (isset($allTokens) && count($allTokens) > 0) ? 'Daftar Semua Token Aktif di Sistem' : 'Daftar Token Aktif' }}
                </h2>
            </header>
            <div class="flex flex-col items-center justify-center p-8 text-center bg-secondary-50 border border-secondary-200 border-dashed rounded-lg">
                <div class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                </div>
                <h3 class="text-sm font-bold text-secondary-900">Belum Ada Kunci API Aktif</h3>
                <p class="text-xs text-secondary-500 mt-1 max-w-sm">Anda belum memiliki token yang aktif. Klik "Buat Token Baru" di atas untuk mulai mengintegrasikan sistem luar.</p>
            </div>
        </div>
    @endif

    <!-- Panduan Integrasi API -->
    <div class="mt-4 pt-4 border-t border-secondary-200">
        <details class="group bg-secondary-50 border border-secondary-200 rounded-lg overflow-hidden transition-all duration-300">
            <summary class="flex items-center justify-between p-4 cursor-pointer select-none bg-white hover:bg-secondary-50 group-open:bg-secondary-50 transition-colors">
                <div>
                    <h2 class="text-lg font-medium text-secondary-900">
                        {{ __('Dokumentasi Integrasi API') }}
                    </h2>
                    <p class="mt-1 text-sm text-secondary-600">
                        Gunakan panduan singkat ini untuk menyambungkan web e-commerce atau layanan lain ke sistem Azventory Anda.
                    </p>
                </div>
                <!-- Icon Dropdown -->
                <div class="shrink-0 ml-4 text-secondary-400 group-open:rotate-180 transition-transform duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </summary>
            
            <div class="p-6 bg-white border-t border-secondary-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-base font-bold text-secondary-900 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                            Base URL & Autentikasi
                        </h3>
                        <p class="text-sm text-secondary-600 mb-3">
                            Semua permintaan API (kecuali *login*) wajib menyertakan Token Anda pada bagian <code>Header</code> HTTP.
                        </p>
                        <div class="bg-secondary-50 p-3 rounded border border-secondary-200 text-xs font-mono text-secondary-800 break-all mb-4 relative group">
                            <span class="text-secondary-500 block mb-1 text-[10px] font-sans font-bold uppercase tracking-wider">Base URL</span>
                            <div class="flex items-center justify-between gap-2">
                                <code>{{ url('/api/v1') }}</code>
                            </div>
                        </div>
                        <div class="bg-secondary-50 p-3 rounded border border-secondary-200 text-xs font-mono text-secondary-800 break-all relative group">
                            <span class="text-secondary-500 block mb-1 text-[10px] font-sans font-bold uppercase tracking-wider">Header Wajib</span>
                            <div class="leading-relaxed">
                                <code>Authorization: Bearer &lt;Token_Anda&gt;</code><br>
                                <code>Accept: application/json</code>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-base font-bold text-secondary-900 mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Contoh Request (cURL)
                        </h3>
                        <p class="text-sm text-secondary-600 mb-3">
                            Berikut adalah contoh cara memanggil API untuk mendapatkan profil Anda menggunakan cURL:
                        </p>
                        <div class="bg-gray-900 rounded-lg overflow-hidden border border-gray-800">
                            <div class="flex items-center px-4 py-2 bg-gray-800">
                                <div class="flex gap-1.5">
                                    <div class="w-3 h-3 rounded-full bg-danger-500"></div>
                                    <div class="w-3 h-3 rounded-full bg-warning-500"></div>
                                    <div class="w-3 h-3 rounded-full bg-success-500"></div>
                                </div>
                                <span class="ml-3 text-xs font-mono text-gray-400">Terminal</span>
                            </div>
                            <div class="p-4 overflow-x-auto">
<pre class="text-xs text-gray-300 font-mono leading-relaxed">curl -X GET "{{ url('/api/v1/me') }}" \
  -H "Authorization: Bearer <span class="text-warning-400">YOUR_TOKEN_HERE</span>" \
  -H "Accept: application/json"</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cheat Sheet Rute Populer -->
                <div class="mt-6 pt-5 border-t border-secondary-100">
                    <h3 class="text-base font-bold text-secondary-900 mb-1 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        Rute API Populer (Cheat Sheet)
                    </h3>
                    <p class="text-sm text-secondary-600 mb-4">
                        Cukup gabungkan <strong>Base URL</strong> dengan rute di bawah ini (misal: <code class="font-mono text-xs bg-secondary-100 px-1 rounded">{{ url('/api/v1') }}/inventory</code>). Klik rute untuk menyalin ke <em>clipboard</em>.
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @php
                            $routes = [
                                ['method' => 'GET', 'color' => 'success', 'path' => '/inventory'],
                                ['method' => 'POST', 'color' => 'primary', 'path' => '/inventory'],
                                ['method' => 'GET', 'color' => 'success', 'path' => '/inventory/{id}'],
                                ['method' => 'GET', 'color' => 'success', 'path' => '/borrowing'],
                                ['method' => 'POST', 'color' => 'primary', 'path' => '/borrowing'],
                                ['method' => 'PUT', 'color' => 'warning', 'path' => '/borrowing/{id}/return'],
                                ['method' => 'GET', 'color' => 'success', 'path' => '/category'],
                                ['method' => 'GET', 'color' => 'success', 'path' => '/brand'],
                                ['method' => 'GET', 'color' => 'success', 'path' => '/location'],
                            ];
                        @endphp
                        @foreach($routes as $route)
                            <div x-data="{ copied: false }" 
                                 @click="navigator.clipboard.writeText('{{ $route['path'] }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                 class="group bg-white border border-secondary-200 p-2.5 rounded-lg shadow-sm flex items-center justify-between gap-2 cursor-pointer hover:border-primary-300 hover:bg-primary-50 transition-all">
                                <div class="flex items-center gap-2 truncate">
                                    <span class="font-bold text-{{ $route['color'] }}-700 bg-{{ $route['color'] }}-50 border border-{{ $route['color'] }}-200 px-1.5 py-0.5 rounded text-[10px] w-10 text-center shrink-0">{{ $route['method'] }}</span>
                                    <code class="text-xs font-mono text-secondary-700 truncate group-hover:text-primary-700 transition-colors">{{ $route['path'] }}</code>
                                </div>
                                <svg x-show="!copied" class="w-4 h-4 text-secondary-400 group-hover:text-primary-500 opacity-0 group-hover:opacity-100 transition-opacity shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <svg x-show="copied" x-cloak class="w-4 h-4 text-success-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-secondary-200 text-center flex flex-col items-center">
                    <p class="text-sm text-secondary-600 mb-4 max-w-xl mx-auto">
                        Butuh daftar rute lengkap? Atau ingin tahu detail parameter untuk <strong>GET, POST, PUT, DELETE</strong>? Anda bahkan bisa mencoba API-nya langsung dari <em>browser</em>. Buka halaman dokumentasi interaktif berikut.
                    </p>
                    <a href="{{ url('/docs') }}" target="_blank" class="btn btn-primary shadow-sm hover:shadow-md transition-shadow flex items-center gap-2">
                        Buka Dokumentasi API Interaktif
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                </div>
            </div>
        </details>
    </div>
    <!-- Modal Konfirmasi Cabut Akses -->
    <x-modal name="confirm-token-revocation" focusable>
        <form x-data="{ isSubmitting: false }" @submit="isSubmitting = true" method="post" :action="'{{ url('/profile/api-tokens') }}/' + tokenIdToDelete" class="p-6" novalidate>
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-secondary-900">
                {{ __('Cabut Akses Token?') }}
            </h2>

            <p class="mt-2 text-sm text-secondary-600">
                Apakah Anda yakin ingin mencabut token <span class="font-bold text-secondary-900" x-text="tokenNameToDelete"></span> <span x-show="tokenOwnerToDelete" x-text="'(milik ' + tokenOwnerToDelete + ')'"></span>? 
                Aplikasi atau sistem yang menggunakan token ini akan langsung kehilangan akses secara permanen.
            </p>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="btn btn-secondary">
                    {{ __('ui.cancel') }}
                </button>

                <button type="submit" class="btn btn-danger" :disabled="isSubmitting" :class="{ 'opacity-75 cursor-not-allowed': isSubmitting }">
                    <span x-show="!isSubmitting">{{ __('Cabut Akses') }}</span>
                    <span x-show="isSubmitting" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Mencabut...
                    </span>
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Modal Edit Hak Akses -->
    <x-modal name="edit-token-abilities" focusable>
        <div x-data="{
            isSubmitting: false,
            tokenIdToEdit: null,
            tokenNameToEdit: '',
            presetEdit: 'custom',
            editAbilities: {},
            abilityLabels: {
                'inventory:read': 'Lihat Inventaris', 'inventory:create': 'Buat Inventaris', 'inventory:update': 'Edit Inventaris', 'inventory:delete': 'Hapus Inventaris',
                'borrowing:read': 'Lihat Pinjam', 'borrowing:create': 'Buat Pinjam', 'borrowing:update': 'Edit Pinjam',
                'category:read': 'Lihat Kategori', 'category:create': 'Buat Kategori', 'category:update': 'Edit Kategori', 'category:delete': 'Hapus Kategori',
                'brand:read': 'Lihat Merk', 'brand:create': 'Buat Merk', 'brand:update': 'Edit Merk', 'brand:delete': 'Hapus Merk',
                'location:read': 'Lihat Lokasi', 'location:create': 'Buat Lokasi', 'location:update': 'Edit Lokasi', 'location:delete': 'Hapus Lokasi',
                'user:read': 'Lihat User', 'user:create': 'Buat User', 'user:update': 'Edit User', 'user:delete': 'Hapus User',
                'log:read': 'Lihat Log'
            },
            initFromEvent(detail) {
                this.tokenIdToEdit = detail.id;
                this.tokenNameToEdit = detail.name;
                let current = detail.abilities || [];
                let isWildcard = current.includes('*');
                
                Object.keys(this.abilityLabels).forEach(a => {
                    this.editAbilities[a] = isWildcard ? true : current.includes(a);
                });
                
                this.checkPresetMatchEdit();
            },
            setPresetEdit() {
                if (this.presetEdit === 'superadmin') {
                    Object.keys(this.editAbilities).forEach(a => this.editAbilities[a] = true);
                } else if (this.presetEdit === 'admin') {
                    Object.keys(this.editAbilities).forEach(a => this.editAbilities[a] = false);
                    ['inventory:read', 'inventory:create', 'inventory:update', 'borrowing:read', 'borrowing:create', 'borrowing:update', 'category:read', 'category:create', 'brand:read', 'brand:create', 'location:read', 'location:create', 'log:read'].forEach(k => this.editAbilities[k] = true);
                } else if (this.presetEdit === 'operator') {
                    Object.keys(this.editAbilities).forEach(a => this.editAbilities[a] = false);
                    ['inventory:read', 'borrowing:read', 'borrowing:create', 'category:read', 'brand:read', 'location:read'].forEach(k => this.editAbilities[k] = true);
                }
            },
            checkPresetMatchEdit() {
                const adminAbilities = ['inventory:read', 'inventory:create', 'inventory:update', 'borrowing:read', 'borrowing:create', 'borrowing:update', 'category:read', 'category:create', 'brand:read', 'brand:create', 'location:read', 'location:create', 'log:read'];
                const operatorAbilities = ['inventory:read', 'borrowing:read', 'borrowing:create', 'category:read', 'brand:read', 'location:read'];

                let isSuperadmin = true;
                let isAdmin = true;
                let isOperator = true;

                for (const key in this.editAbilities) {
                    if (!this.editAbilities[key]) isSuperadmin = false;
                    
                    if (adminAbilities.includes(key)) {
                        if (!this.editAbilities[key]) isAdmin = false;
                    } else {
                        if (this.editAbilities[key]) isAdmin = false;
                    }
                    
                    if (operatorAbilities.includes(key)) {
                        if (!this.editAbilities[key]) isOperator = false;
                    } else {
                        if (this.editAbilities[key]) isOperator = false;
                    }
                }

                let matchedPreset = 'custom';
                if (isSuperadmin) matchedPreset = 'superadmin';
                else if (isAdmin) matchedPreset = 'admin';
                else if (isOperator) matchedPreset = 'operator';

                this.presetEdit = matchedPreset;
                
                // Disksinkronisasi dengan select custom component
                this.$dispatch('set-selected', {name: 'preset_edit_select', value: matchedPreset});
            },
            selectAll() {
                Object.keys(this.editAbilities).forEach(a => this.editAbilities[a] = true);
                this.checkPresetMatchEdit();
            },
            clearAll() {
                Object.keys(this.editAbilities).forEach(a => this.editAbilities[a] = false);
                this.checkPresetMatchEdit();
            }
        }" @open-edit-modal.window="initFromEvent($event.detail); $dispatch('open-modal', 'edit-token-abilities')">
            
            <form method="post" :action="'{{ url('/profile/api-tokens') }}/' + tokenIdToEdit" class="p-6" @submit="isSubmitting = true" novalidate>
                @csrf
                @method('put')
                <h2 class="text-lg font-bold text-secondary-900 mb-1">Edit Hak Akses Token</h2>
                <div class="mb-5">
                    <x-input-label for="edit_token_name" :value="__('Nama Perangkat / Integrasi')" />
                    <x-text-input id="edit_token_name" name="token_name" type="text" x-model="tokenNameToEdit" required
                                  class="mt-1 block w-full sm:w-2/3" 
                                  placeholder="Contoh: Web Ecommerce Utama" />
                    <x-input-error class="mt-2" :messages="$errors->get('token_name')" />
                </div>
                
                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-3">
                    <span class="text-sm font-medium text-secondary-700">Pilih modul yang diizinkan:</span>
                    <div class="flex items-center gap-2">
                        <div x-on:selected="if($event.detail) { presetEdit = $event.detail; setPresetEdit() }" class="mr-2">
                            <x-select name="preset_edit_select" 
                                      :options="['custom' => '-- Preset Cepat (Role) --', 'superadmin' => 'Setara Superadmin', 'admin' => 'Setara Admin', 'operator' => 'Setara Operator']" 
                                      selected="custom" 
                                      width="w-full sm:w-auto" 
                                      :allowClear="false" />
                        </div>
                        <button type="button" @click="selectAll()" class="text-xs text-primary-600 hover:underline font-bold whitespace-nowrap">Pilih Semua</button>
                        <button type="button" @click="clearAll()" class="text-xs text-danger-600 hover:underline font-bold whitespace-nowrap">Bersihkan</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 max-h-[50vh] overflow-y-auto pr-2 pb-4">
                    <template x-for="ability in Object.keys(abilityLabels)" :key="ability">
                        <label class="flex items-center gap-2 cursor-pointer p-2 hover:bg-secondary-50 rounded border border-secondary-200 transition-colors" :class="{ 'bg-primary-50 border-primary-300': editAbilities[ability] }">
                            <input @change="checkPresetMatchEdit()" type="checkbox" name="abilities[]" :value="ability" x-model="editAbilities[ability]" class="rounded border-secondary-300 text-primary-600 focus:ring-primary-500 cursor-pointer">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-secondary-800" x-text="abilityLabels[ability]"></span>
                                <span class="text-[10px] font-mono text-secondary-500" x-text="ability"></span>
                            </div>
                        </label>
                    </template>
                </div>

                <x-input-error class="mt-2" :messages="$errors->get('abilities')" />

                <div class="mt-4 flex justify-end gap-3 pt-4 border-t border-secondary-200">
                    <button type="button" @click="$dispatch('close')" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary flex items-center gap-2" :disabled="Object.values(editAbilities).filter(Boolean).length === 0 || isSubmitting" :class="{ 'opacity-50 cursor-not-allowed': Object.values(editAbilities).filter(Boolean).length === 0 || isSubmitting }">
                        <span x-show="!isSubmitting">{{ __('ui.profile_btn_save') }}</span>
                        <span x-show="isSubmitting" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</section>


