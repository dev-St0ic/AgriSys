<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 8.5px;
            color: #1a1a1a;
            padding: 12px 14px;
        }

        .report-header {
            border-bottom: 2px solid #1B6B3A;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #1B6B3A;
        }

        .report-meta {
            font-size: 7.5px;
            color: #6b7280;
            margin-top: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        thead tr {
            background-color: #1B6B3A;
            color: #ffffff;
        }

        thead th {
            padding: 5px 6px;
            text-align: left;
            font-size: 7.5px;
            font-weight: bold;
            white-space: nowrap;
            border: 1px solid #145c30;
        }

        tbody tr:nth-child(even) {
            background-color: #f0f7f3;
        }

        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        tbody td {
            padding: 4px 6px;
            border: 1px solid #d1d5db;
            font-size: 7.5px;
            vertical-align: top;
            word-break: break-word;
        }

        .footer {
            margin-top: 10px;
            font-size: 7px;
            color: #9ca3af;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="report-header">
        <div class="report-title">{{ $title }}</div>
        <div class="report-meta">
            Generated: {{ now()->format('F d, Y \a\t h:i A') }}
            &nbsp;&bull;&nbsp;
            Total Records: {{ number_format(count($rows)) }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                @foreach ($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    @foreach ($row as $cell)
                        <td>{{ $cell ?? '—' }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" style="text-align:center; color:#9ca3af; padding:12px;">
                        No records found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">AgriSys &mdash; {{ config('app.name', 'Agriculture System') }}</div>
</body>

</html>
