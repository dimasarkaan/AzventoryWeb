<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    @include('reports.partials.pdf_style')
    <style>
        /* Low-stock specific styles - Emergency Red Injection */
        .critical { color: #dc2626; font-weight: bold; }
        .pdf-company-name { color: #991b1b !important; border-bottom-color: #991b1b !important; }
        th { background-color: #991b1b !important; border-color: #7f1d1d !important; }
        .pdf-company-header { border-bottom-color: #991b1b !important; }
    </style>
</head>
<body>
    @include('reports.partials.pdf_header')

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">{{ __('ui.no_column') }}</th>
                <th style="width: 15%;">{{ __('ui.part_code_column') }}</th>
                <th style="width: 30%;">{{ __('ui.item_name_column') }}</th>
                <th style="width: 20%;">{{ __('ui.location_column') }}</th>
                <th style="width: 10%;">{{ __('ui.remaining_stock_column') }}</th>
                <th style="width: 10%;">{{ __('ui.min_stock_column') }}</th>
                <th style="width: 10%;">{{ __('ui.status_column') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td style="text-align:center;">{{ $index + 1 }}</td>
                <td>{{ $row->part_number }}</td>
                <td>{{ $row->name }}</td>
                <td>{{ $row->location?->name ?? '-' }}</td>
                <td class="critical" style="text-align: center; font-size: 11pt;">{{ $row->stock }}</td>
                <td style="text-align: center;">{{ $row->minimum_stock }}</td>
                <td>
                    @if($row->stock <= $row->minimum_stock)
                        <span class="critical" style="color: #dc2626; font-weight: bold;">{{ strtoupper(__('ui.status_critical')) }}</span>
                    @else
                        <span style="color: #ea580c; font-weight: bold;">{{ strtoupper(__('ui.approaching_stock')) }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
