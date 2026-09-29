<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                     <h1 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('ui.add_new_user') }}
                    </h1>
                    <p class="mt-1 text-sm text-secondary-500">{{ __('ui.add_user_desc') }}</p>
                </div>
                 <a href="{{ route('users.index') }}" class="btn btn-secondary flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    {{ __('ui.back') }}
                </a>
            </div>

            <div class="bg-white rounded-xl border border-secondary-200 shadow-card p-8 overflow-visible" x-data="{ isSubmitting: false, email: '{{ old('email', '') }}' }">
                <form action="{{ route('users.store') }}" method="POST" @submit="isSubmitting = true" novalidate>
                    @csrf
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-6">
                        <!-- Form Fields will flow into 2 columns automatically -->
    @include('users.partials.form')
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

