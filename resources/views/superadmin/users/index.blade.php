<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('Manajemen Pengguna') }}
                    </h2>
                    <p class="mt-1 text-sm text-secondary-500">Kelola akun, role, dan hak akses pengguna sistem.</p>
                </div>
                <div>
                    <a href="{{ route('superadmin.users.create') }}" class="btn btn-primary flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        {{ __('Tambah Pengguna') }}
                    </a>
                </div>
            </div>

            <!-- Success Message (if any) -->
            @if(session('success'))
                <div class="mb-4 p-4 rounded-lg bg-success-50 text-success-700 border border-success-100 flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Search & Filters -->
             <div class="mb-4 card p-4">
                <form method="GET" action="{{ route('superadmin.users.index') }}" class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="relative w-full md:w-96">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" class="input-field pl-10 w-full" placeholder="Cari nama, email, atau username..." onchange="this.form.submit()">
                    </div>
                    <div>
                        <select name="role" class="input-field w-full md:w-auto" onchange="this.form.submit()">
                            <option value="Semua Role">Semua Role</option>
                            <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="operator" {{ request('role') == 'operator' ? 'selected' : '' }}>Operator</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Desktop Table View (Hidden on Mobile) -->
            <div class="hidden md:block card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th>Profil</th>
                                <th>Email / Kontak</th>
                                <th>Jabatan</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr class="group hover:bg-secondary-50 transition-colors">
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-full bg-secondary-100 flex-shrink-0 overflow-hidden">
                                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                                            </div>
                                            <div>
                                                <div class="font-medium text-secondary-900">{{ $user->name }}</div>
                                                <div class="text-xs text-secondary-500 font-mono">@ {{ $user->username }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm text-secondary-900">{{ $user->email }}</div>
                                        @if($user->phone)
                                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $user->phone)) }}" target="_blank" class="text-xs text-success-600 hover:text-success-700 flex items-center gap-1 mt-0.5">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                                {{ $user->phone }}
                                            </a>
                                        @endif
                                    </td>
                                    <td class="text-sm text-secondary-600">{{ $user->jabatan ?? '-' }}</td>
                                    <td>
                                        @php
                                            $roleColors = [
                                                'superadmin' => 'bg-purple-100 text-purple-800',
                                                'admin' => 'bg-blue-100 text-blue-800',
                                                'operator' => 'bg-secondary-100 text-secondary-800'
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($user->status === 'active')
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('superadmin.users.show', $user) }}" class="btn btn-ghost p-2 text-secondary-500 hover:text-primary-600" title="Detail">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                            <a href="{{ route('superadmin.users.edit', $user) }}" class="btn btn-ghost p-2 text-secondary-500 hover:text-warning-600" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            <form action="{{ route('superadmin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus user ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-ghost p-2 text-secondary-500 hover:text-danger-600" title="Hapus (Nonaktifkan lebih disarankan)" onclick="if(confirm('Apakah Anda yakin?')) this.closest('form').submit()">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-secondary-500">
                                        Data pengguna kosong.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View (Visible on Mobile) -->
            <div class="md:hidden space-y-4">
                @forelse($users as $user)
                    <div class="card p-4 flex flex-col gap-3">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 rounded-full bg-secondary-100 flex-shrink-0 overflow-hidden">
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                                </div>
                                <div>
                                    <div class="font-bold text-secondary-900 line-clamp-1">{{ $user->name }}</div>
                                    <div class="text-xs text-secondary-500 font-mono">@ {{ $user->username }}</div>
                                </div>
                            </div>
                            @if($user->status === 'active')
                                <span class="badge badge-success text-[10px]">Aktif</span>
                            @else
                                <span class="badge badge-danger text-[10px]">Nonaktif</span>
                            @endif
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2 text-sm border-t border-b border-secondary-50 py-3">
                            <div>
                                <span class="text-xs text-secondary-400 block">Role</span>
                                @php
                                    $roleColors = [
                                        'superadmin' => 'text-purple-700 bg-purple-50',
                                        'admin' => 'text-blue-700 bg-blue-50',
                                        'operator' => 'text-secondary-700 bg-secondary-50'
                                    ];
                                @endphp
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $roleColors[$user->role] ?? 'text-gray-700 bg-gray-50' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-secondary-400 block">Jabatan</span>
                                <span class="font-medium text-secondary-700">{{ $user->jabatan ?? '-' }}</span>
                            </div>
                            <div class="col-span-2 mt-1">
                                <span class="text-xs text-secondary-400 block">Email</span>
                                <span class="text-secondary-700">{{ $user->email }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-1">
                             <a href="{{ route('superadmin.users.show', $user) }}" class="btn btn-ghost text-xs p-2 h-auto text-secondary-600 font-medium">Detail</a>
                             <a href="{{ route('superadmin.users.edit', $user) }}" class="btn btn-secondary text-xs p-2 h-auto">Edit</a>
                             <form action="{{ route('superadmin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus user ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger text-xs p-2 h-auto">Hapus</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="card p-8 text-center text-secondary-500">
                        <p class="text-sm">Data pengguna kosong.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-6">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
