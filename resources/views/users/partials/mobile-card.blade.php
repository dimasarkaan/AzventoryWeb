                    <div class="card p-4">
                        <!-- Header: Avatar, Name, Role -->
                        <div class="flex items-start gap-4 mb-4">
                            <!-- Avatar -->
                            @if(request('trash'))
                                <div class="flex items-center self-center mr-2">
                                    <input type="checkbox" name="ids[]" value="{{ $user->id }}" class="user-checkbox rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 w-5 h-5">
                                </div>
                            @endif
                            <div class="h-14 w-14 rounded-full bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0 border border-secondary-200 overflow-hidden">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" loading="lazy" class="h-full w-full object-cover">
                                @else
                                    <span class="font-bold text-lg">{{ substr($user->name, 0, 1) }}</span>
                                @endif
                            </div>
                            
                            <!-- Identity -->
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-base font-bold text-secondary-900 line-clamp-1">
                                            {{ $user->name }}
                                        </h3>
                                        <div class="flex items-center gap-2 mt-1">
                                            @php
                                                $roleColor = match($user->role) {
                                                    \App\Enums\UserRole::SUPERADMIN => 'bg-purple-100 text-purple-700 border-purple-200',
                                                    \App\Enums\UserRole::ADMIN => 'bg-blue-100 text-blue-700 border-blue-200',
                                                    \App\Enums\UserRole::OPERATOR => 'bg-gray-100 text-gray-700 border-gray-200',
                                                };
                                            @endphp
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium border {{ $roleColor }}">
                                                {{ $user->role->label() }}
                                            </span>
                                            <span class="w-1 h-1 rounded-full bg-secondary-300"></span>
                                            <span class="text-xs text-secondary-500">{{ $user->created_at->format('M Y') }}</span>
                                        </div>
                                    </div>
                                    
                                    <x-status-badge :status="$user->status" type="pill" class="text-[10px] uppercase tracking-wide" />
                                </div>
                            </div>
                        </div>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-1 gap-y-2 text-sm mb-4 border-t border-b border-secondary-100 py-3">
                             <div class="flex items-center gap-2 text-secondary-600" x-data="{ copied: false }">
                                <svg class="w-4 h-4 text-secondary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <button type="button" @click="navigator.clipboard.writeText('{{ $user->email }}'); copied = true; setTimeout(() => copied = false, 2000)" class="truncate hover:text-primary-600 transition-colors flex items-center gap-1 group/email text-left w-full" title="Salin Email">
                                    <span class="truncate">{{ $user->email }}</span>
                                    <svg x-show="!copied" class="w-3.5 h-3.5 text-secondary-400 group-hover/email:text-primary-500 opacity-0 group-hover/email:opacity-100 transition-all flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <svg x-show="copied" x-cloak class="w-3.5 h-3.5 text-success-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </button>
                            </div>
                            <div class="flex items-center gap-2 text-secondary-600">
                                <svg class="w-4 h-4 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <span class="truncate">{{ $user->job_title ?? '-' }}</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-2">
                            @if(request('trash'))
                                @can('restore', $user)
                                <form action="{{ route('users.restore', $user->uuid) }}" method="POST" class="w-full" novalidate>
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success w-full justify-center flex items-center gap-2" onclick="confirmUserRestore(event)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        {{ __('ui.restore') }}
                                    </button>
                                </form>
                                @endcan
                            @else
                                @can('update', $user)
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-secondary flex-1 justify-center flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        {{ __('ui.edit') }}
                                    </a>
                                @endcan
                                @can('delete', $user)
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="flex-1" novalidate>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger w-full justify-center flex items-center gap-2" onclick="confirmDelete(event)">
                                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            {{ __('ui.delete') }}
                                        </button>
                                    </form>
                                @endcan
                            @endif
                        </div>
                    </div>
