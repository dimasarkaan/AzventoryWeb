<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Aktivitas Sistem</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18pt; color: #1e40af; }
        .header p { margin: 5px 0; color: #666; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; vertical-align: top; font-size: 9pt; }
        th { background-color: #4b5563; color: #ffffff; font-weight: bold; text-transform: uppercase; text-align: center; }
        tr:nth-child(even) { background-color: #f3f4f6; }
        .badges { font-size: 8pt; padding: 2px 5px; border-radius: 4px; border: 1px solid #ccc; display: inline-block; }
        .badge-info { background: #e0f2fe; color: #0369a1; border-color: #bae6fd; }
        .badge-warning { background: #fef3c7; color: #92400e; border-color: #fde68a; }
        .badge-danger { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }
        .badge-success { background: #d1fae5; color: #065f46; border-color: #a7f3d0; }
        .footer { text-align: right; margin-top: 30px; font-size: 8pt; color: #999; }
        .meta { font-size: 9pt; margin-bottom: 15px; color: #444; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="text-transform: uppercase;">Laporan Aktivitas Sistem</h1>
        <p>
            Periode: 
            @if(request('start_date') && request('end_date'))
                {{ \Carbon\Carbon::parse(request('start_date'))->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse(request('end_date'))->translatedFormat('d F Y') }}
            @elseif(request('start_date'))
                Sejak {{ \Carbon\Carbon::parse(request('start_date'))->translatedFormat('d F Y') }}
            @elseif(request('end_date'))
                Hingga {{ \Carbon\Carbon::parse(request('end_date'))->translatedFormat('d F Y') }}
            @else
                Semua Riwayat
            @endif
        </p>
    </div>

    <div class="meta">
        @if(request('role') && request('role') !== 'Semua Role')
            <strong>Role:</strong> {{ ucfirst(request('role')) }} &nbsp; | &nbsp;
        @endif
        @if(request('user_id'))
            <strong>User ID:</strong> {{ request('user_id') }} &nbsp; | &nbsp;
        @endif
        @if(request('action'))
            <strong>Jenis Aksi:</strong> {{ request('action') }} &nbsp; | &nbsp;
        @endif
        @if(request('search'))
            <strong>Kata Kunci:</strong> "{{ request('search') }}"
        @endif
    </div>

    @php
        $isPdf = $isPdf ?? true;
    @endphp

    <table style="width: {{ $isPdf ? '100%' : 'auto' }};">
        <thead>
            <tr>
                <th style="{{ $isPdf ? 'width: 15%' : 'width: 120px' }}">Waktu</th>
                <th style="{{ $isPdf ? 'width: 20%' : 'width: 150px' }}">Pengguna</th>
                <th style="{{ $isPdf ? 'width: 10%' : 'width: 100px' }}">Role</th>
                <th style="{{ $isPdf ? 'width: 20%' : 'width: 180px' }}">Aksi</th>
                <th style="{{ $isPdf ? 'width: 35%' : 'width: 400px' }}">Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $log->user->name ?? 'System' }}</td>
                    <td style="text-align: center;">
                        @php
                            $role = $log->user->role ?? '-';
                            $badgeClass = match($role) {
                                'superadmin' => 'badge-danger',
                                'admin' => 'badge-warning',
                                'operator' => 'badge-info',
                                default => 'badges'
                            };
                        @endphp
                        <span class="badges {{ $badgeClass }}">{{ ucfirst($role) }}</span>
                    </td>
                    <td>
                        <span style="font-weight: bold; color: #4b5563;">{{ $log->action }}</span>
                    </td>
                    <td>
                        {{ $log->description }}
                        @if($log->properties && is_array($log->properties))
                            <div style="margin-top: 5px; font-size: 8.5pt; color: #555; background: #fff; padding: 4px; border: 1px dashed #ccc;">
                                <strong>Detail Perubahan:</strong><br>
                                @foreach($log->properties as $key => $change)
                                    @if(is_array($change) && isset($change['old'], $change['new']))
                                        &bull; {{ ucfirst($key) }}: <span style="text-decoration: line-through; color: #ef4444;">{{ $change['old'] }}</span> &rarr; <span style="color: #10b981; font-weight: bold;">{{ $change['new'] }}</span><br>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">Tidak ada data aktivitas yang sesuai filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Azventory &bull; Dicetak oleh {{ auth()->user()->name }} pada {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>
</body>
</html>
