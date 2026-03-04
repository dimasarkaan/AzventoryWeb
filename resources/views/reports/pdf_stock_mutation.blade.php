<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    @include('reports.partials.pdf_style')
</head>
<body>
    @include('reports.partials.pdf_header')

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">{{ __('ui.date_column') }}</th>
                <th style="width: 25%;">{{ __('ui.item_column') }}</th>
                <th style="width: 10%;">{{ __('ui.type_column') }}</th>
                <th style="width: 10%; text-align: right;">{{ __('ui.amount_column') }}</th>
                <th style="width: 25%;">{{ __('ui.description_column') }}</th>
                <th style="width: 15%;">{{ __('ui.user_column') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $log)
            <tr>
                <td>{{ $log->created_at->translatedFormat('d F Y H:i') }}</td>
                <td>
                    <strong>{{ $log->sparepart->name ?? __('ui.unknown') }}</strong><br>
                    <small>PN: {{ $log->sparepart->part_number ?? '-' }}</small>
                </td>
                <td>
                    @if($log->type == 'masuk')
                        <span style="color: #059669; font-weight:bold;">{{ __('ui.type_in') }}</span>
                    @else
                        <span style="color: #dc2626; font-weight:bold;">{{ __('ui.type_out') }}</span>
                    @endif
                </td>
                <td style="text-align: right;">
                    @if($log->type == 'masuk')
                        <span class="text-success">+{{ $log->quantity }}</span>
                    @else
                        <span class="text-danger">-{{ $log->quantity }}</span>
                    @endif
                </td>
                <td>{{ $log->reason }}</td>
                <td>{{ $log->user->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
