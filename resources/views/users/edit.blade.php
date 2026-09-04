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
                    <div class="bg-white rounded-xl border border-secondary-200 shadow-card p-6 overflow-visible" x-data="{ isSubmitting: false }">
                        <form action="{{ route('users.update', $user) }}" method="POST" @submit="isSubmitting = true" novalidate>
                            @csrf
                            @method('PUT')
        
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                                <!-- Area Informasi Akun -->
                                <div class="md:col-span-2 border-b border-secondary-100 pb-2 mb-2">
                                    <h3 class="text-lg font-bold text-secondary-900">{{ __('ui.account_info') }}</h3>
                                </div>

    @include('users.partials.form')

                            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-secondary-100">
                                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                    {{ __('ui.cancel') }}
                                </a>
                                <button type="submit" id="submit-btn" data-testid="btn-submit-user" class="btn btn-primary" :disabled="isSubmitting" :class="{ 'opacity-75 cursor-not-allowed': isSubmitting }">
                                    <span x-show="!isSubmitting">{{ __('ui.save_changes') }}</span>
                                    <span x-show="isSubmitting" class="flex items-center gap-2">
                                        <svg id="btn-spinner" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span>{{ __('ui.save_changes') }}</span>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Side Actions (Danger Zone) -->
                <div class="space-y-4" x-data="{ openDanger: false, isResetting: false, isDeleting: false }">
                    <button @click="openDanger = !openDanger" type="button" class="w-full flex justify-between items-center bg-white p-4 rounded-xl border border-danger-200 shadow-sm text-danger-700 font-bold hover:bg-danger-50 transition-colors">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Advanced / Danger Settings
                        </span>
                        <svg class="w-5 h-5 transform transition-transform duration-200" :class="{'rotate-180': openDanger}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="openDanger" x-collapse x-cloak class="space-y-4">
                        <div class="card p-6 border-l-4 border-warning-500">
                            <h3 class="text-lg font-bold text-secondary-900 mb-2">{{ __('ui.reset_password') }}</h3>
                            <p class="text-sm text-secondary-500 mb-4">
                                {{ __('ui.reset_password_desc') }} <code>password123</code>.
                            </p>
                            <form action="{{ route('users.reset-password', $user) }}" method="POST" novalidate id="form-reset">
                                @csrf
                                @method('PATCH')
                                <button type="button" @click="confirmResetWithState($event, () => isResetting = true)" data-testid="btn-reset-password" class="btn btn-warning w-full justify-center" :disabled="isResetting">
                                    <svg x-show="!isResetting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    <svg x-show="isResetting" x-cloak class="animate-spin w-5 h-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span x-show="!isResetting">{{ __('ui.reset_password') }}</span>
                                    <span x-show="isResetting" x-cloak>Memproses...</span>
                                </button>
                            </form>
                        </div>

                        <div class="card p-6 border-l-4 border-danger-500">
                            <h3 class="text-lg font-bold text-secondary-900 mb-2">{{ __('ui.delete_user') }}</h3>
                            <p class="text-sm text-secondary-500 mb-4">
                                {{ __('ui.delete_user_warning') }}
                            </p>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" novalidate id="form-delete">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="confirmDeleteWithState($event, () => isDeleting = true)" data-testid="btn-delete-user" class="btn btn-danger w-full justify-center" :disabled="isDeleting">
                                    <svg x-show="!isDeleting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <svg x-show="isDeleting" x-cloak class="animate-spin w-5 h-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span x-show="!isDeleting">{{ __('ui.delete') }}</span>
                                    <span x-show="isDeleting" x-cloak>Menghapus...</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    @push('scripts')
    <script>
        // Existing global confirmReset just in case (fallback)
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
            });
        }

        // Alpine Specific Handlers
        function confirmResetWithState(event, setLoadingState) {
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
                    setLoadingState();
                    form.submit();
                }
            });
        }

        function confirmDeleteWithState(event, setLoadingState) {
            event.preventDefault();
            const form = event.target.closest('form');
            Swal.fire({
                title: '{{ __('ui.delete_confirm_title') }}',
                text: "{{ __('ui.delete_confirm_desc') }}",
                icon: 'error',
                showCancelButton: true,
                confirmButtonText: '{{ __('ui.yes_delete') }}',
                cancelButtonText: '{{ __('ui.cancel') }}',
                customClass: {
                    popup: '!rounded-2xl !font-sans',
                    title: '!text-secondary-900 !text-xl !font-bold',
                    htmlContainer: '!text-secondary-500 !text-sm',
                    confirmButton: 'btn btn-danger px-6 py-2.5 rounded-lg ml-3 shadow-md transform hover:scale-105 transition-transform duration-200',
                    cancelButton: 'btn btn-secondary px-6 py-2.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                },
                buttonsStyling: false,
                reverseButtons: true,
                iconColor: '#ef4444',
                padding: '2em',
                backdrop: `rgba(0,0,0,0.4)`
            }).then((result) => {
                if (result.isConfirmed) {
                    setLoadingState();
                    form.submit();
                }
            });
        }
    </script>
    @endpush
    <x-unsaved-changes-warning />
</x-app-layout>

