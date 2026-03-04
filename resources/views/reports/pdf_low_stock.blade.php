<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    @include('reports.partials.pdf_style')
    <style>
        /* Low-stock specific overrides (supplement pdf_header partial) */
        .pdf-company-header { border-bottom-color: #dc2626 !important; }
        .pdf-report-title h1 { color: #dc2626 !important; }
        th { background-color: #991b1b !important; } /* Dark Red header */
        tr:nth-child(even) { background-color: #fef2f2; }
        .critical { color: #dc2626; font-weight: bold; }
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
                <td>{{ $row->location }}</td>
                <td class="critical" style="text-align: center; font-size: 11pt;">{{ $row->stock }}</td>
                <td style="text-align: center;">{{ $row->minimum_stock }}</td>
                <td class="critical">{{ strtoupper(__('ui.status_critical')) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
