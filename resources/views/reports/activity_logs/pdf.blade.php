<!DOCTYPE html>
<html>
<head>
    <title>Laporan Aktivitas Sistem</title>
    @include('reports.partials.pdf_style')
    <style>
        .badges { font-size: 8pt; padding: 2px 5px; border-radius: 4px; border: 1px solid #ccc; display: inline-block; }
        .badge-info { background: #e0f2fe; color: #0369a1; border-color: #bae6fd; }
        .badge-warning { background: #fef3c7; color: #92400e; border-color: #fde68a; }
        .badge-danger { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }
        .badge-success { background: #d1fae5; color: #065f46; border-color: #a7f3d0; }
        .meta { font-size: 9pt; margin-bottom: 15px; color: #444; }
    </style>
</head>
<body>
    @php
        // Construct Title and Period Strings for the Header Partial
        $title = mb_strtoupper(__('ui.report_activity_title'));
        
        $request = $request ?? request(); // Ensure request is available from Job or live View
        
        if($request->get('start_date') && $request->get('end_date')) {
            $period = \Carbon\Carbon::parse($request->get('start_date'))->translatedFormat('d F Y') . ' - ' . \Carbon\Carbon::parse($request->get('end_date'))->translatedFormat('d F Y');
            $startDate = \Carbon\Carbon::parse($request->get('start_date'));
            $endDate = \Carbon\Carbon::parse($request->get('end_date'));
        } elseif($request->get('start_date')) {
            $period = __('ui.since_date', ['date' => \Carbon\Carbon::parse($request->get('start_date'))->translatedFormat('d F Y')]);
            $startDate = \Carbon\Carbon::parse($request->get('start_date'));
            $endDate = null;
        } elseif($request->get('end_date')) {
            $period = __('ui.until_date', ['date' => \Carbon\Carbon::parse($request->get('end_date'))->translatedFormat('d F Y')]);
            $startDate = null;
            $endDate = \Carbon\Carbon::parse($request->get('end_date'));
        } else {
            $period = __('ui.all_history');
            $startDate = null;
            $endDate = null;
        }
        
        $type = 'activity_log';
    @endphp

    @include('reports.partials.pdf_header')

    <div class="meta">
        @if($request->get('role') && $request->get('role') !== 'Semua Role')
            <strong>{{ __('ui.role_filter') }}:</strong> {{ ucfirst($request->get('role')) }} &nbsp; | &nbsp;
        @endif
        @if($request->get('user_id'))
            <strong>User ID:</strong> {{ $request->get('user_id') }} &nbsp; | &nbsp;
        @endif
        @if($request->get('action'))
            <strong>{{ __('ui.action_type') }}:</strong> {{ $request->get('action') }} &nbsp; | &nbsp;
        @endif
        @if($request->get('search'))
            <strong>{{ __('ui.keyword_label') }}</strong> "{{ $request->get('search') }}"
        @endif
    </div>

    @php
        $isPdf = $isPdf ?? true;
    @endphp

    <table style="width: {{ $isPdf ? '100%' : 'auto' }};">
        <thead>
            <tr>
                <th style="width: 15%">{{ __('ui.time_header') }}</th>
                <th style="width: 20%">{{ __('ui.user_header') }}</th>
                <th style="width: 10%">{{ __('ui.role_filter') }}</th>
                <th style="width: 20%">{{ __('ui.action_header') }}</th>
                <th style="width: 35%">{{ __('ui.description_header') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $log->user->name ?? __('ui.system_user') }}</td>
                    <td style="text-align: center;">
                        @php
                            $role = $log->user->role ?? null;
                            $badgeClass = match($role) {
                                \App\Enums\UserRole::SUPERADMIN => 'badge-danger',
                                \App\Enums\UserRole::ADMIN => 'badge-warning',
                                \App\Enums\UserRole::OPERATOR => 'badge-info',
                                default => 'badges'
                            };
                            $roleLabel = $role instanceof \App\Enums\UserRole ? $role->label() : ($role ?? '-');
                        @endphp
                        <span class="badges {{ $badgeClass }}">{{ $roleLabel }}</span>
                    </td>
                    <td>
                        <span style="font-weight: bold; color: #4b5563;">{{ $log->action }}</span>
                    </td>
                    <td>
                        {{ $log->description }}
                        @if($log->properties && is_array($log->properties))
                            <div style="margin-top: 5px; font-size: 8.5pt; color: #555; background: #fff; padding: 4px; border: 1px dashed #ccc;">
                                <strong>{{ __('ui.change_details') }}</strong><br>
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
                    <td colspan="5" style="text-align: center; padding: 20px;">{{ __('ui.no_data_filtered') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
