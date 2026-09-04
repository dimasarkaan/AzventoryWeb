<x-guest-layout>
    <div class="mb-4 text-sm text-secondary-600">
        {{ __('ui.auth_verify_desc') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-success-600">
            {{ __('ui.auth_verify_sent') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}" x-data="{ isSubmitting: false }" @submit="isSubmitting = true" novalidate>
            @csrf

            <div>
                <x-primary-button x-bind:disabled="isSubmitting" class="disabled:opacity-70 disabled:cursor-not-allowed">
                    <span x-show="!isSubmitting">{{ __('ui.auth_btn_resend_verify') }}</span>
                    <span x-show="isSubmitting" class="flex items-center gap-2" x-cloak>
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses...
                    </span>
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}" novalidate>
            @csrf

            <button type="submit" class="underline text-sm text-secondary-600 hover:text-secondary-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                {{ __('ui.auth_btn_logout') }}
            </button>
        </form>
    </div>
</x-guest-layout>


