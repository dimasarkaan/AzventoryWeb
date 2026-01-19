<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                     <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('Tambah Pengguna Baru') }}
                    </h2>
                    <p class="mt-1 text-sm text-secondary-500">Buat akun untuk staff atau admin baru.</p>
                </div>
                 <a href="{{ route('superadmin.users.index') }}" class="btn btn-secondary flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    {{ __('Kembali') }}
                </a>
            </div>

            <div class="card p-8">
                <form action="{{ route('superadmin.users.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- Account Details -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-bold text-secondary-900 border-b border-secondary-100 pb-2">Informasi Akun</h3>
                            
                            <!-- Email -->
                            <div>
                                <label for="email" class="input-label">Email Address <span class="text-danger-500">*</span></label>
                                <input id="email" class="input-field" type="email" name="email" value="{{ old('email') }}" placeholder="contoh@gmail.com" autofocus />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                             <!-- Status -->
                             <div>
                                <label for="status" class="input-label">Status Akun</label>
                                <select id="status" name="status" class="input-field">
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <!-- Role & Job -->
                        <div class="space-y-4">
                             <h3 class="text-lg font-bold text-secondary-900 border-b border-secondary-100 pb-2">Akses & Jabatan</h3>

                            <!-- Role -->
                            <div>
                                <label for="role" class="input-label">Role Akses <span class="text-danger-500">*</span></label>
                                <select id="role" name="role" class="input-field">
                                    <option value="operator" {{ old('role') == 'operator' ? 'selected' : '' }}>Operator (Gudang)</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (Manajemen)</option>
                                    <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}>Super Admin (Full Akses)</option>
                                </select>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>

                            <!-- Jabatan -->
                            <div>
                                <label for="jabatan" class="input-label">Jabatan / Posisi <span class="text-danger-500">*</span></label>
                                <input id="jabatan" class="input-field" type="text" name="jabatan" value="{{ old('jabatan') }}" placeholder="Contoh: Staff Gudang, Kepala Logistik" />
                                <x-input-error :messages="$errors->get('jabatan')" class="mt-2" />
                            </div>

                             <div class="bg-primary-50 border border-primary-100 rounded-lg p-4 flex items-start gap-3 mt-8">
                                <svg class="w-5 h-5 text-primary-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="text-sm text-primary-700">
                                    <p class="font-semibold">Info Default System</p>
                                    <ul class="list-disc list-inside mt-1 space-y-1 text-primary-600">
                                        <li><strong>Username Default:</strong> Dibuat otomatis sementara (berdasarkan email).</li>
                                        <li><strong>Password Default:</strong> <code>password123</code></li>
                                        <li>User dapat mengatur username sendiri setelah login.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-secondary-100">
                        <a href="{{ route('superadmin.users.index') }}" class="btn btn-secondary">
                            {{ __('Batal') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            {{ __('Simpan Pengguna') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
