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

            <div class="bg-white rounded-xl border border-secondary-200 shadow-card p-8 overflow-visible" x-data="{ isSubmitting: false }">
                <form action="{{ route('users.store') }}" method="POST" @submit="isSubmitting = true" novalidate>
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
                        
    @include('users.partials.form')

                        <!-- Info Box (Security Warning) -->
                        <div class="bg-warning-50 border border-warning-200 rounded-lg p-4 flex items-start gap-3 lg:mt-0">
                            <svg class="w-5 h-5 text-warning-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <div class="text-sm text-warning-800">
                                <p class="font-bold">{{ __('ui.default_system_info') }} (Perhatian Keamanan)</p>
                                <ul class="list-disc list-inside mt-2 space-y-1 text-warning-700">
                                    <li><strong>{{ __('ui.default_username_info') }}</strong></li>
                                    <li><strong>{{ __('ui.default_password_info') }}</strong> <code class="bg-warning-100 px-1.5 py-0.5 rounded text-warning-900 font-bold border border-warning-200">password123</code></li>
                                    <li class="mt-2 text-warning-900 text-xs font-semibold bg-warning-100 p-2 rounded-md border border-warning-200 block">⚠️ Pastikan untuk menginstruksikan pengguna mengganti password default ini setelah login pertama kali.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-secondary-100">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            {{ __('ui.cancel') }}
                        </a>
                        <button type="submit" id="submit-btn" data-testid="btn-submit-user" class="btn btn-primary" :disabled="isSubmitting" :class="{ 'opacity-75 cursor-not-allowed': isSubmitting }">
                            <span x-show="!isSubmitting">{{ __('ui.save_user') }}</span>
                            <span x-show="isSubmitting" class="flex items-center gap-2">
                                <svg id="btn-spinner" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
    @endpush
    <x-unsaved-changes-warning />
</x-app-layout>

