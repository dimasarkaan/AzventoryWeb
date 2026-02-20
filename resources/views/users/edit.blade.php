<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                     <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('ui.edit_user') }}
                    </h2>
                    <p class="mt-1 text-sm text-secondary-500">
                        {{ __('ui.edit_user_desc') }} <strong>{{ $user->name }}</strong>.
                    </p>
                </div>
                <div>
                     <a href="{{ route('users.index') }}" class="btn btn-secondary flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        {{ __('ui.back') }}
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Main Edit Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl border border-secondary-200 shadow-card p-6 overflow-visible">
                        <form action="{{ route('users.update', $user) }}" method="POST">
                            @csrf
                            @method('PUT')
        
                            <div class="space-y-4">
                                <h3 class="text-lg font-bold text-secondary-900 border-b border-secondary-100 pb-2 mb-4">{{ __('ui.account_info') }}</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                     <!-- Read Only Info -->
                                    <div>
                                        <label class="input-label">{{ __('ui.username') }}</label>
                                        <div class="input-field bg-secondary-50 text-secondary-500 cursor-not-allowed">
                                            {{ $user->username }}
                                        </div>
                                    </div>
            
                                    <div>
                                        <label for="email" class="input-label">{{ __('ui.email_address') }} <span class="text-danger-500">*</span></label>
                                        <input type="email" name="email" id="email" class="input-field w-full" value="{{ old('email', $user->email) }}" required>
                                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                    </div>
                                </div>

                                <!-- Editable Fields -->
                                <div>
                                    <label for="jabatan" class="input-label">{{ __('ui.job_position') }} <span class="text-danger-500">*</span></label>
                                    <input id="jabatan" class="input-field" type="text" name="jabatan" value="{{ old('jabatan', $user->jabatan) }}" required />
                                    <x-input-error :messages="$errors->get('jabatan')" class="mt-2" />
                                </div>
        
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="role" class="input-label">{{ __('ui.access_role') }} <span class="text-danger-500">*</span></label>
                                        @php
                                            $roleOptions = [
                                                \App\Enums\UserRole::OPERATOR->value => __('ui.role_operator_desc'),
                                                \App\Enums\UserRole::ADMIN->value => __('ui.role_admin_desc'),
                                                \App\Enums\UserRole::SUPERADMIN->value => __('ui.role_superadmin_desc'),
                                            ];
                                        @endphp
                                        <x-select name="role" :options="$roleOptions" :selected="old('role', $user->role->value)" placeholder="{{ __('ui.select_role') }}" width="w-full" />
                                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                                    </div>
            
                                    <div>
                                        <label for="status" class="input-label">{{ __('ui.account_status') }}</label>
                                        @php
                                            $statusOptions = [
                                                'active' => __('ui.active'),
                                                'inactive' => __('ui.inactive'),
                                            ];
                                        @endphp
                                        <x-select name="status" :options="$statusOptions" :selected="old('status', $user->status)" placeholder="{{ __('ui.select_status') }}" width="w-full" />
                                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
        
                            <div class="flex items-center justify-end gap-3 mt-8 pt-4 border-t border-secondary-100">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('ui.save_changes') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Side Actions (Danger Zone) -->
                <div class="space-y-4">
                    <div class="card p-6 border-l-4 border-warning-500">
                        <h3 class="text-lg font-bold text-secondary-900 mb-2">{{ __('ui.reset_password') }}</h3>
                        <p class="text-sm text-secondary-500 mb-4">
                            {{ __('ui.reset_password_desc') }} <code>password123</code>.
                        </p>
                        <form action="{{ route('users.reset-password', $user) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning w-full justify-center" onclick="confirmReset(event)">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                {{ __('ui.reset_password') }}
                            </button>
                        </form>
                    </div>

                    <div class="card p-6 border-l-4 border-danger-500">
                        <h3 class="text-lg font-bold text-secondary-900 mb-2">{{ __('ui.delete_user') }}</h3>
                        <p class="text-sm text-secondary-500 mb-4">
                            {{ __('ui.delete_user_warning') }}
                        </p>
                         <form action="{{ route('users.destroy', $user) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-full justify-center" onclick="confirmDelete(event)">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                {{ __('ui.force_delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    @push('scripts')
    <script>
        function confirmReset(event) {
            event.preventDefault();
            const form = event.target.closest('form');
            Swal.fire({
                title: '{{ __('ui.reset_password_title') }}',
                text: "{{ __('ui.reset_password_confirm') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('ui.yes_reset') }}',
                cancelButtonText: '{{ __('ui.cancel') }}',
                customClass: {
                    popup: '!rounded-2xl !font-sans',
                    title: '!text-secondary-900 !text-xl !font-bold',
                    htmlContainer: '!text-secondary-500 !text-sm',
                    confirmButton: 'btn btn-warning px-6 py-2.5 rounded-lg ml-3 shadow-md transform hover:scale-105 transition-transform duration-200',
                    cancelButton: 'btn btn-secondary px-6 py-2.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                },
                buttonsStyling: false,
                reverseButtons: true,
                iconColor: '#f59e0b',
                padding: '2em',
                backdrop: `rgba(0,0,0,0.4)`
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        }
    </script>
    @endpush
</x-app-layout>
