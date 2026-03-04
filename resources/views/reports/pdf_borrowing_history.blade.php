<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    @include('reports.partials.pdf_style')
    <style>
        /* Borrowing-specific styles (supplement pdf_header partial) */
        .borrowed { background-color: #3b82f6 !important; color: white !important; }
        .returned { background-color: #10b981 !important; color: white !important; }
        .overdue  { background-color: #dc2626 !important; color: white !important; }
        .late { color: #dc2626; font-weight: bold; font-size: 8.5pt; display: block; margin-top: 2px; }
        tr:nth-child(even) { background-color: #eff6ff; }
    </style>
</head>
<body>
    @include('reports.partials.pdf_header')

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">{{ __('ui.no_column') }}</th>
                <th style="width: 18%;">{{ __('ui.borrower_column') }}</th>
                <th style="width: 21%;">{{ __('ui.item_column') }}</th>
                <th style="width: 12%;">{{ __('ui.borrow_date_column') }}</th>
                <th style="width: 12%;">{{ __('ui.due_date_column') }}</th>
                <th style="width: 12%;">{{ __('ui.return_date_column') }}</th>
                <th style="width: 10%;">{{ __('ui.status_column') }}</th>
                <th style="width: 10%;">{{ __('ui.condition_column') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row->user->name ?? $row->borrower_name }}</td>
                <td>
                    <b>{{ $row->sparepart->name ?? 'Deleted Item' }}</b>
                    <br><span style="color: #666; font-size: 8pt;">Jml: {{ $row->quantity }}</span>
                </td>
                <td>{{ $row->borrowed_at->translatedFormat('d F Y') }}</td>
                <td>
                    {{ $row->expected_return_at ? $row->expected_return_at->translatedFormat('d F Y') : '-' }}
                    @if($row->status == 'borrowed' && $row->expected_return_at && $row->expected_return_at < now())
                        <span class="late">{{ __('ui.late_days', ['days' => (int) $row->expected_return_at->diffInDays(now())]) }}</span>
                    @endif
                </td>
                <td>{{ $row->returned_at ? $row->returned_at->translatedFormat('d F Y H:i') : '-' }}</td>
                <td>
                    @php
                        $statusLabels = [
                            'borrowed' => __('ui.status_borrowed'),
                            'returned' => __('ui.status_returned'),
                            'lost' => __('ui.status_lost'),
                        ];
                        $statusLabel = $statusLabels[$row->status] ?? ucfirst($row->status);
                    @endphp
                    <span class="badge {{ $row->status }}">{{ $statusLabel }}</span>
                </td>
                <td>
                    @php
                        $conditionLabels = [
                            'good' => __('ui.condition_good'),
                            'broken' => __('ui.condition_broken'),
                            'lost' => __('ui.condition_lost'),
                        ];
                        $conditionLabel = $row->return_condition ? ($conditionLabels[$row->return_condition] ?? ucfirst($row->return_condition)) : '-';
                    @endphp
                    {{ $conditionLabel }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
