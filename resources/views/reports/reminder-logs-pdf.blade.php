<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Reminder Log</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1a1a2e;
            background: #fff;
        }

        /* ── Header ── */
        .header {
            background: linear-gradient(135deg, #1E3A5F 0%, #0E7490 100%);
            color: #fff;
            padding: 18px 24px;
            margin-bottom: 16px;
            border-radius: 4px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .header .subtitle {
            font-size: 10px;
            opacity: 0.85;
            margin-top: 3px;
        }
        .header .meta {
            margin-top: 8px;
            font-size: 9px;
            opacity: 0.75;
        }

        /* ── Summary Cards ── */
        .summary {
            display: table;
            width: 100%;
            margin-bottom: 14px;
            border-collapse: separate;
            border-spacing: 6px;
        }
        .summary-card {
            display: table-cell;
            width: 25%;
            background: #F0F4FA;
            border: 1px solid #CBD5E1;
            border-radius: 4px;
            padding: 10px 12px;
            text-align: center;
        }
        .summary-card .label {
            font-size: 8px;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .summary-card .value {
            font-size: 20px;
            font-weight: 700;
            margin-top: 2px;
        }
        .card-total   .value { color: #1E3A5F; }
        .card-sent    .value { color: #16A34A; }
        .card-failed  .value { color: #DC2626; }
        .card-skipped .value { color: #D97706; }

        /* ── Filter Info ── */
        .filter-info {
            background: #FAFAFA;
            border: 1px solid #E2E8F0;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 12px;
            font-size: 9px;
            color: #475569;
        }
        .filter-info strong { color: #1E3A5F; }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead tr {
            background: #1E3A5F;
            color: #fff;
        }
        thead th {
            padding: 7px 8px;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #163254;
        }
        tbody tr:nth-child(even) { background: #F8FAFC; }
        tbody tr:nth-child(odd)  { background: #FFFFFF; }
        tbody td {
            padding: 6px 8px;
            border: 1px solid #E2E8F0;
            vertical-align: middle;
        }

        /* ── Badges ── */
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .badge-sent    { background: #DCFCE7; color: #16A34A; }
        .badge-failed  { background: #FEE2E2; color: #DC2626; }
        .badge-skipped { background: #FEF9C3; color: #A16207; }

        .badge-permit   { background: #FEF9C3; color: #92400E; }
        .badge-contract { background: #DCFCE7; color: #166534; }

        /* ── Footer ── */
        .footer {
            margin-top: 16px;
            padding-top: 8px;
            border-top: 1px solid #E2E8F0;
            font-size: 8px;
            color: #94A3B8;
            text-align: center;
        }

        .no-data {
            text-align: center;
            padding: 24px;
            color: #94A3B8;
            font-style: italic;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <h1>🔔 Laporan Reminder Log</h1>
        <div class="subtitle">PT Ekson Indo — History Pengiriman Notifikasi Reminder</div>
        <div class="meta">
            Dicetak: {{ now()->format('d F Y, H:i') }} WIB
            &nbsp;|&nbsp;
            Total data: {{ $logs->count() }} log
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="summary">
        <div class="summary-card card-total">
            <div class="label">Total</div>
            <div class="value">{{ $logs->count() }}</div>
        </div>
        <div class="summary-card card-sent">
            <div class="label">Sent</div>
            <div class="value">{{ $logs->where('status', 'sent')->count() }}</div>
        </div>
        <div class="summary-card card-failed">
            <div class="label">Failed</div>
            <div class="value">{{ $logs->where('status', 'failed')->count() }}</div>
        </div>
        <div class="summary-card card-skipped">
            <div class="label">Skipped</div>
            <div class="value">{{ $logs->where('status', 'skipped')->count() }}</div>
        </div>
    </div>

    {{-- Filter Info --}}
    @if($filterInfo)
    <div class="filter-info">
        <strong>Filter aktif:</strong> {{ $filterInfo }}
    </div>
    @endif

    {{-- Table --}}
    @if($logs->isEmpty())
        <div class="no-data">Tidak ada data reminder log sesuai filter yang dipilih.</div>
    @else
    <table>
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th>Entity</th>
                <th>Entity ID</th>
                <th>Target Date</th>
                <th>Days Before</th>
                <th>Recipient</th>
                <th>Channel</th>
                <th>Status</th>
                <th>Sent At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $i => $log)
            <tr>
                <td style="text-align:center">{{ $i + 1 }}</td>
                <td>
                    <span class="badge badge-{{ $log->entity }}">{{ ucfirst($log->entity) }}</span>
                </td>
                <td style="text-align:center">{{ $log->entity_id }}</td>
                <td>{{ $log->target_date ? $log->target_date->format('d/m/Y') : '-' }}</td>
                <td style="text-align:center">{{ $log->rule_days }}d</td>
                <td>{{ $log->recipient ?? '-' }}</td>
                <td style="text-align:center">{{ strtoupper($log->channel) }}</td>
                <td>
                    <span class="badge badge-{{ $log->status }}">{{ strtoupper($log->status) }}</span>
                </td>
                <td>{{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Footer --}}
    <div class="footer">
        Laporan ini digenerate otomatis oleh Sistem Reminder PT Ekson Indo &bull; {{ now()->format('Y') }}
    </div>

</body>
</html>
