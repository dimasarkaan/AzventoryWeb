<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-4 flex items-center justify-between">
                <div>
                     <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('Detail Pengguna') }}
                    </h2>
                    <p class="mt-1 text-sm text-secondary-500">
                        Informasi lengkap akun dan profil pengguna.
                    </p>
                </div>
                <div class="flex gap-3">
                     <a href="{{ route('superadmin.users.index') }}" class="btn btn-secondary flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        {{ __('Kembali') }}
                    </a>
                    <a href="{{ route('superadmin.users.edit', $user) }}" class="btn btn-warning flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        {{ __('Edit Akun') }}
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Left Column: Profile Card -->
                <div class="space-y-4">
                    <div class="card p-6 flex flex-col items-center text-center">
                        <div class="relative mb-4">
                            <div class="h-32 w-32 rounded-full overflow-hidden border-4 border-white shadow-lg">
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                            </div>
                            <div class="absolute bottom-0 right-0 p-1.5 bg-white rounded-full shadow-sm">
                                @if($user->status === 'active')
                                    <div class="h-5 w-5 bg-success-500 rounded-full border-2 border-white" title="Aktif"></div>
                                @else
                                    <div class="h-5 w-5 bg-danger-500 rounded-full border-2 border-white" title="Nonaktif"></div>
                                @endif
                            </div>
                        </div>
                        
                        <h3 class="text-xl font-bold text-secondary-900">{{ $user->name }}</h3>
                        <p class="text-sm text-secondary-500 font-mono mb-4">@ {{ $user->username }}</p>

                        <div class="flex items-center gap-2 mb-6">
                            @php
                                $roleColors = [
                                    'superadmin' => 'bg-purple-100 text-purple-800',
                                    'admin' => 'bg-blue-100 text-blue-800',
                                    'operator' => 'bg-secondary-100 text-secondary-800'
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>

                        <div class="w-full border-t border-secondary-100 pt-4 text-left space-y-3">
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold block">Jabatan</span>
                                <span class="text-secondary-800 font-medium">{{ $user->jabatan ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold block">Bergabung Sejak</span>
                                <span class="text-secondary-800 font-medium">{{ $user->created_at->isoFormat('D MMMM Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Details -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="card p-6">
                        <h3 class="text-lg font-bold text-secondary-900 border-b border-secondary-100 pb-2 mb-4">Informasi Kontak & Detail</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold block mb-1">Email Address</span>
                                <div class="flex items-center gap-2 text-secondary-900">
                                    <svg class="w-5 h-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    {{ $user->email }}
                                </div>
                            </div>

                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold block mb-1">Nomor Telepon (WhatsApp)</span>
                                @if($user->phone)
                                    <div class="flex items-center gap-2 text-secondary-900">
                                        <svg class="w-5 h-5 text-success-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                        <span>{{ $user->phone }}</span>
                                    </div>
                                @else
                                    <span class="text-secondary-400 italic">Belum diisi</span>
                                @endif
                            </div>

                            <div class="md:col-span-2">
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold block mb-1">Alamat</span>
                                <p class="text-secondary-900">{{ $user->address ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
