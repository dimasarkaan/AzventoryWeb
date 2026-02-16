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
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- Account Details -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-bold text-secondary-900 border-b border-secondary-100 pb-2">{{ __('ui.account_info') }}</h3>
                            
                            <!-- Email -->
                            <div>
                                <label for="email" class="input-label">{{ __('ui.email_address') }} <span class="text-danger-500">*</span></label>
                                <input id="email" class="input-field" type="email" name="email" value="{{ old('email') }}" placeholder="contoh@gmail.com" autofocus />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                             <!-- Status -->
                             <div>
                                <label for="status" class="input-label">{{ __('ui.account_status') }}</label>
                                @php
                                    $statusOptions = [
                                        'active' => __('ui.active'),
                                        'inactive' => __('ui.inactive'),
                                    ];
                                @endphp
                                <x-select name="status" :options="$statusOptions" :selected="old('status', 'active')" placeholder="{{ __('ui.select_status') }}" width="w-full" />
                            </div>
                        </div>

                        <!-- Role & Job -->
                        <div class="space-y-4">
                             <h3 class="text-lg font-bold text-secondary-900 border-b border-secondary-100 pb-2">{{ __('ui.access_job') }}</h3>

                            <!-- Role -->
                            <div>
                                <label for="role" class="input-label">{{ __('ui.access_role') }} <span class="text-danger-500">*</span></label>
                                @php
                                    $roleOptions = [
                                        \App\Enums\UserRole::OPERATOR->value => __('ui.role_operator_desc'),
                                        \App\Enums\UserRole::ADMIN->value => __('ui.role_admin_desc'),
                                        \App\Enums\UserRole::SUPERADMIN->value => __('ui.role_superadmin_desc'),
                                    ];
                                @endphp
                                <x-select name="role" :options="$roleOptions" :selected="old('role')" placeholder="{{ __('ui.select_role') }}" width="w-full" />
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>

                            <!-- Jabatan -->
                            <div>
                                <label for="jabatan" class="input-label">{{ __('ui.job_position') }} <span class="text-danger-500">*</span></label>
                                <input id="jabatan" class="input-field" type="text" name="jabatan" value="{{ old('jabatan') }}" placeholder="{{ __('ui.placeholder_job') }}" />
                                <x-input-error :messages="$errors->get('jabatan')" class="mt-2" />
                            </div>

                             <div class="bg-primary-50 border border-primary-100 rounded-lg p-4 flex items-start gap-3 mt-8">
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
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-secondary-100">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            {{ __('ui.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            {{ __('ui.save_user') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
