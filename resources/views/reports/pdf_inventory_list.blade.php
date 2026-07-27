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
                <th style="width: 5%;">{{ __('ui.no_column') }}</th>
                <th style="width: 26%;">{{ __('ui.item_name_column') }}</th>
                <th style="width: 13%;">{{ __('ui.category_column') }}</th>
                <th style="width: 13%;">{{ __('ui.brand_column') }}</th>
                <th style="width: 15%;">{{ __('ui.location_column') }}</th>
                <th style="width: 10%;">{{ __('ui.stock_column') }}</th>
                <th style="width: 18%;">{{ __('ui.status_column') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td style="text-align:center;">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $item->name }}</strong><br>
                    <small style="color: #666;">PN: {{ $item->part_number }}</small>
                </td>
                <td>{{ $item->category?->name ?? '-' }}</td>
                <td>{{ $item->brand?->name ?? '-' }}</td>
                <td>{{ $item->location?->name ?? '-' }}</td>
                <td style="text-align: center;">{{ $item->stock }} {{ $item->unit }}</td>
                <td>
                    @if(strtolower($item->condition) === 'rusak')
                        <span class="badge badge-danger">Rusak</span>
                    @elseif(strtolower($item->condition) === 'hilang')
                        <span class="badge badge-secondary" style="background:#f1f5f9;color:#475569;border-color:#cbd5e1;">Hilang</span>
                    @elseif($item->stock == 0)
                        <span class="badge badge-danger">{{ __('ui.status_out_of_stock') }}</span>
                    @elseif($item->minimum_stock > 0)
                        @if($item->stock <= $item->minimum_stock)
                            <span class="badge badge-warning">{{ __('ui.stock_low') }}</span>
                        @elseif($item->stock <= ($item->minimum_stock + 5))
                            <span class="badge badge-warning" style="background:#fff7ed;color:#92400e;border-color:#fbbf24;">{{ __('ui.approaching_stock') }}</span>
                        @else
                            <span class="badge badge-success">{{ __('ui.stock_safe') }}</span>
                        @endif
                    @else
                        <span class="badge badge-success">{{ __('ui.stock_safe') }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
