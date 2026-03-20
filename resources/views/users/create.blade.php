<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                     <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('ui.add_new_user') }}
                    </h2>
                    <p class="mt-1 text-sm text-secondary-500">{{ __('ui.add_user_desc') }}</p>
                </div>
                 <a href="{{ route('users.index') }}" class="btn btn-secondary flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    {{ __('ui.back') }}
                </a>
            </div>

            <div class="bg-white rounded-xl border border-secondary-200 shadow-card p-8 overflow-visible">
                <form action="{{ route('users.store') }}" method="POST" novalidate>
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-6">
                        <!-- Section Headers -->
                        <div class="lg:col-span-2 grid grid-cols-1 lg:grid-cols-2 gap-x-12 border-b border-secondary-100 pb-2 mb-2">
                            <div>
                                <h3 class="text-lg font-bold text-secondary-900">{{ __('ui.account_info') }}</h3>
                            </div>
                            <div class="hidden lg:block">
                                <h3 class="text-lg font-bold text-secondary-900">{{ __('ui.access_job') }}</h3>
                            </div>
                        </div>
                        
                        <!-- Nama Lengkap -->
                        <div class="space-y-2">
                            <label for="name" class="input-label">{{ __('ui.full_name') }} <span class="text-danger-500">*</span></label>
                            <input id="name" class="input-field" type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('ui.placeholder_full_name') }}" autocomplete="name" autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
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
                            <x-select name="role" id="role" :options="$roleOptions" :selected="old('role')" placeholder="{{ __('ui.select_role') }}" width="w-full" />
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <label for="email" class="input-label">{{ __('ui.email_address') }} <span class="text-danger-500">*</span></label>
                            <input id="email" class="input-field" type="email" name="email" value="{{ old('email') }}" placeholder="contoh@gmail.com" autocomplete="email" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Jabatan -->
                        <div class="space-y-2">
                            <label for="jabatan" class="input-label">{{ __('ui.job_position') }} <span class="text-danger-500">*</span></label>
                            <input id="jabatan" class="input-field" type="text" name="jabatan" value="{{ old('jabatan') }}" placeholder="{{ __('ui.placeholder_job') }}" autocomplete="organization-title" />
                            <x-input-error :messages="$errors->get('jabatan')" class="mt-2" />
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
                            <x-select name="status" id="status" :options="$statusOptions" :selected="old('status', 'aktif')" placeholder="{{ __('ui.select_status') }}" width="w-full" />
                        </div>

                        <!-- Info Box -->
                        <div class="bg-primary-50 border border-primary-100 rounded-lg p-4 flex items-start gap-3 lg:mt-0">
                            <svg class="w-5 h-5 text-primary-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div class="text-sm text-primary-700">
                                <p class="font-semibold">{{ __('ui.default_system_info') }}</p>
                                <ul class="list-disc list-inside mt-1 space-y-1 text-primary-600">
                                    <li><strong>{{ __('ui.default_username_info') }}</strong></li>
                                    <li><strong>{{ __('ui.default_password_info') }}</strong> <code>password123</code></li>
                                    <li>{{ __('ui.user_can_set_username') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-secondary-100">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            {{ __('ui.cancel') }}
                        </a>
                        <button type="submit" id="submit-btn" class="btn btn-primary">
                            <span class="inline-flex items-center gap-2">
                                <svg id="btn-spinner" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ __('ui.save_user') }}</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const btn = document.getElementById('submit-btn');
            const spinner = document.getElementById('btn-spinner');
            
            if (btn.disabled) return;
            
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            spinner.classList.remove('hidden');
        });
    </script>
    @endpush
</x-app-layout>
