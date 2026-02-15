@props(['user', 'trash' => false])

<div class="card p-4 flex flex-col gap-3 group border border-secondary-100 hover:border-primary-200 transition-colors">
    <!-- Header -->
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-3">
            <div class="h-12 w-12 rounded-full bg-secondary-100 flex-shrink-0 overflow-hidden border border-secondary-200">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
            </div>
            <div>
                <div class="font-bold text-secondary-900 line-clamp-1 group-hover:text-primary-600 transition-colors">{{ $user->name }}</div>
                <div class="text-xs text-secondary-500 font-mono">@ {{ $user->username }}</div>
            </div>
        </div>
        @if($user->status === 'active')
            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-success-100 text-success-700 tracking-wide uppercase">{{ __('ui.active') }}</span>
        @else
            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-secondary-100 text-secondary-600 tracking-wide uppercase">{{ __('ui.inactive') }}</span>
        @endif
    </div>
    
    <!-- Info Grid -->
    <div class="grid grid-cols-2 gap-2 text-sm border-t border-b border-secondary-50 py-3">
        <div>
            <span class="text-[10px] text-secondary-400 block uppercase tracking-wider mb-0.5">{{ __('ui.role') }}</span>
            @php
                $roleColors = [
                    \App\Enums\UserRole::SUPERADMIN->value => 'text-purple-700 bg-purple-50 border-purple-100',
                    \App\Enums\UserRole::ADMIN->value => 'text-blue-700 bg-blue-50 border-blue-100',
                    \App\Enums\UserRole::OPERATOR->value => 'text-secondary-700 bg-secondary-50 border-secondary-100'
                ];
            @endphp
            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-medium border {{ $roleColors[$user->role->value] ?? 'text-secondary-700 bg-secondary-50 border-secondary-100' }}">
                {{ $user->role->label() }}
            </span>
        </div>
        <div class="text-right">
            <span class="text-[10px] text-secondary-400 block uppercase tracking-wider mb-0.5">{{ __('ui.job_title') }}</span>
            <span class="font-medium text-secondary-700 text-xs">{{ $user->jabatan ?? '-' }}</span>
        </div>
        <div class="col-span-2 mt-1">
            <span class="text-[10px] text-secondary-400 block uppercase tracking-wider mb-0.5">Email</span>
            <div class="flex items-center gap-1.5 text-secondary-700 text-xs">
                <svg class="w-3.5 h-3.5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                <span class="truncate">{{ $user->email }}</span>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end gap-2 pt-1 border-t border-secondary-50">
        @if($trash)
            <form action="{{ route('users.restore', $user->id) }}" method="POST" class="w-full">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-sm btn-success w-full justify-center flex items-center gap-2 h-8 text-xs" onclick="confirmUserRestore(event)">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    {{ __('ui.restore') }}
                </button>
            </form>
        @else
            <a href="{{ route('users.show', $user) }}" class="btn btn-ghost text-xs p-2 h-8 text-secondary-600 font-medium hover:bg-secondary-50 rounded-lg transition-colors">{{ __('ui.detail') }}</a>
            <a href="{{ route('users.edit', $user) }}" class="btn btn-white text-xs p-2 h-8 border border-secondary-200 text-secondary-600 font-medium hover:bg-secondary-50 rounded-lg transition-all">{{ __('ui.edit') }}</a>
            @if(auth()->id() !== $user->id)
                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger text-xs p-2 h-8 bg-danger-50 text-danger-600 hover:bg-danger-600 hover:text-white border-transparent transition-all" onclick="confirmDelete(event)">{{ __('ui.delete') }}</button>
                </form>
            @endif
        @endif
    </div>
</div>
