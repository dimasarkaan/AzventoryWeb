<section x-data="{ sessionIdToLogout: null }">
    <div class="text-sm text-secondary-600 mb-4 w-full">
        {{ __('ui.active_sessions_info') }}
    </div>

    @if (count($sessions) > 0)
        <div class="mt-5 space-y-4">
            @foreach ($sessions as $session)
                <div class="flex items-center">
                    <div>
                        @if ($session->agent->is_desktop)
                            <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        @else
                            <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        @endif
                    </div>

                    <div class="ml-3">
                        <div class="text-sm text-secondary-900 font-medium">
                            {{ $session->agent->browser ?: 'Unknown' }} pada {{ $session->agent->os ?: 'Unknown' }}
                        </div>

                        <div>
                            <div class="text-xs text-secondary-500">
                                {{ $session->ip_address }},

                                @if ($session->is_current_device)
                                    <span class="text-success-500 font-semibold">{{ __('ui.this_device') }}</span>
                                @else
                                    {{ __('ui.active_status') }} {{ $session->last_active }}
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @if (!$session->is_current_device)
                    <div class="ml-auto">
                        <button type="button" 
                                x-on:click.prevent="sessionIdToLogout = '{{ $session->id }}'; $dispatch('open-modal', 'confirm-logout-other-browser-sessions')"
                                data-testid="btn-logout-session-{{ $session->id }}"
                                class="text-xs font-bold text-danger-600 hover:text-danger-800 hover:underline px-2 py-1 rounded transition-colors">
                            {{ __('ui.revoke_access') }}
                        </button>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if (count($sessions) > 1)
    <div class="flex items-center mt-6">
        <button 
            type="button"
            x-on:click.prevent="sessionIdToLogout = null; $dispatch('open-modal', 'confirm-logout-other-browser-sessions')"
            data-testid="btn-logout-all-sessions"
            class="btn btn-secondary flex items-center gap-2"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            {{ __('ui.logout_all_other_devices') }}
        </button>
    </div>
    @else
    <div class="mt-6 p-4 bg-secondary-50 border border-secondary-200 rounded-lg flex items-start gap-3">
        <svg class="w-5 h-5 text-secondary-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div>
            <h4 class="text-sm font-bold text-secondary-900">{{ __('ui.single_session') }}</h4>
            <p class="text-xs text-secondary-600 mt-1">{{ __('ui.single_session_desc') }}</p>
        </div>
    </div>
    @endif


    <!-- Modal Konfirmasi Logout -->
    <x-modal name="confirm-logout-other-browser-sessions" :show="$errors->sessionDeletion->isNotEmpty()" focusable>
        <form x-data="{ submitting: false }" @submit="submitting = true" method="post" :action="sessionIdToLogout ? '{{ url('/profile/sessions') }}/' + sessionIdToLogout : '{{ route('profile.sessions.destroy') }}'" class="p-6" novalidate>
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-secondary-900" x-text="sessionIdToLogout ? '{{ __('ui.revoke_this_device') }}' : '{{ __('ui.revoke_all_other_devices') }}'">
            </h2>

            <p class="mt-2 text-sm text-secondary-600" x-text="sessionIdToLogout ? '{{ __('ui.revoke_this_device_desc') }}' : '{{ __('ui.revoke_all_other_devices_desc') }}'">
            </p>

            <div class="mt-6 w-full">
                <label for="password_session" class="sr-only">{{ __('ui.auth_label_password') }}</label>
                <x-password-input id="password_session" name="password" data-testid="input-session-password"
                    class="input-field w-full {{ $errors->sessionDeletion->has('password') ? '!border-red-500' : '' }}" 
                    placeholder="{{ __('ui.profile_placeholder_password') }}" 
                    autocomplete="current-password" />
                <x-input-error :messages="$errors->sessionDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="btn btn-secondary">
                    {{ __('ui.cancel') }}
                </button>

                <button type="submit" data-testid="btn-confirm-logout-session" class="btn btn-primary flex items-center gap-2" :class="{ 'opacity-75 cursor-not-allowed': submitting }" :disabled="submitting">
                    <svg x-show="submitting" x-cloak class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span x-text="submitting ? '{{ __('ui.saving') }}...' : '{{ __('ui.logout_other_devices_btn') }}'"></span>
                </button>
            </div>
        </form>
    </x-modal>
</section>
