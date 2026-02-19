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
        .badges { font-size: 8pt; padding: 2px 5px; border-radius: 4px; border: 1px solid #ccc; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }
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
                <th style="width: 5%;">{{ __('ui.no_column') }}</th>
                <th style="width: 25%;">{{ __('ui.item_name_column') }}</th>
                <th style="width: 15%;">{{ __('ui.category_column') }}</th>
                <th style="width: 15%;">{{ __('ui.brand_column') }}</th>
                <th style="width: 15%;">{{ __('ui.location_column') }}</th>
                <th style="width: 10%;">{{ __('ui.stock_column') }}</th>
                <th style="width: 15%;">{{ __('ui.status_column') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $item->name }}</strong><br>
                    <small style="color: #666;">PN: {{ $item->part_number }}</small>
                </td>
                <td>{{ $item->category }}</td>
                <td>{{ $item->brand }}</td>
                <td>{{ $item->location }}</td>
                <td style="text-align: center;">{{ $item->stock }} {{ $item->unit }}</td>
                <td>
                    @if($item->stock == 0)
                        <span class="badges badge-danger">{{ __('ui.status_out_of_stock') }}</span>
                    @elseif($item->minimum_stock > 0 && $item->stock <= $item->minimum_stock)
                        <span class="badges badge-warning">{{ __('ui.stock_low') }}</span>
                    @else
                        <span class="badges badge-success">{{ __('ui.stock_safe') }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Azventory &bull; {{ __('ui.report_footer_printed_by', ['name' => auth()->user()->name, 'date' => now()->translatedFormat('d F Y H:i')]) }}</p>
    </div>
</body>
</html>
