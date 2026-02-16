<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18pt; color: #1e40af; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; vertical-align: top; }
        th { background-color: #4b5563; color: #ffffff; font-weight: bold; text-transform: uppercase; font-size: 9pt; text-align: center; }
        tr:nth-child(even) { background-color: #f3f4f6; }
        .text-success { color: #059669; font-weight: bold; }
        .text-danger { color: #dc2626; font-weight: bold; }
        .footer { text-align: right; margin-top: 30px; font-size: 8pt; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="text-transform: uppercase;">{{ $title }}</h1>
        <p style="font-size: 10pt; margin-top: 5px;">
            @if($startDate && $endDate)
                {{ __('ui.period_label') }} {{ $startDate->translatedFormat('d F Y') }} - {{ $endDate->translatedFormat('d F Y') }}
            @else
                {{ __('ui.period_label') }} {{ __('ui.all_history') }}
            @endif
            &nbsp; | &nbsp;
            {{ __('ui.location_label') }} {{ $location == 'all' ? __('ui.all_locations') : $location }}
        </p>
    </div>

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
                        <span style="color: green;">{{ __('ui.type_in') }}</span>
                    @else
                        <span style="color: #dc2626;">{{ __('ui.type_out') }}</span>
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

    <div class="footer">
        <p>Azventory &bull; {{ __('ui.report_footer_printed_by', ['name' => auth()->user()->name, 'date' => now()->translatedFormat('d F Y H:i')]) }}</p>
    </div>
</body>
</html>
