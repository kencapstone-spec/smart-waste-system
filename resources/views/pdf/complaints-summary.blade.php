<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complaints Summary Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #16a34a; padding-bottom: 12px; }
        .header h1 { font-size: 18px; color: #16a34a; margin: 0 0 4px; }
        .header p { margin: 2px 0; color: #555; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead { background-color: #16a34a; color: white; }
        thead th { padding: 7px 10px; text-align: left; font-size: 10px; }
        tbody tr:nth-child(even) { background-color: #f0fdf4; }
        tbody td { padding: 6px 10px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; }
        .badge-pending { background: #fef9c3; color: #854d0e; }
        .badge-reviewed { background: #dbeafe; color: #1e40af; }
        .badge-resolved { background: #dcfce7; color: #166534; }
        .badge-missed_collection { background: #ffedd5; color: #9a3412; }
        .badge-illegal_dumping { background: #fee2e2; color: #991b1b; }
        .footer { margin-top: 24px; font-size: 9px; color: #888; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 10px; }
        .desc { max-width: 200px; overflow: hidden; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Smart Waste Collection System</h1>
        <p>Barangay San Isidro, Talibon, Bohol</p>
        <p style="font-size:13px; font-weight:bold; margin-top:8px;">Complaints & Reports Summary</p>
        @if($from || $to)
            <p>Period: {{ $from ? \Carbon\Carbon::parse($from)->format('M d, Y') : 'All time' }} &mdash; {{ $to ? \Carbon\Carbon::parse($to)->format('M d, Y') : 'Present' }}</p>
        @endif
    </div>

    <table style="width:auto; margin-bottom:12px;">
        <tr>
            <td style="padding:6px 16px; background:#ffedd5; border-radius:4px; color:#9a3412; font-weight:bold;">Missed Collection: {{ $reports->where('type','missed_collection')->count() }}</td>
            <td style="padding:6px 16px; background:#fee2e2; border-radius:4px; color:#991b1b; font-weight:bold;">Illegal Dumping: {{ $reports->where('type','illegal_dumping')->count() }}</td>
            <td style="padding:6px 16px; background:#f3f4f6; border-radius:4px; font-weight:bold;">Total: {{ $reports->count() }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Resident</th>
                <th>Type</th>
                <th>Description</th>
                <th>Status</th>
                <th>Responded By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $i => $report)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $report->created_at->format('M d, Y') }}</td>
                <td>{{ $report->resident->name ?? '—' }}</td>
                <td><span class="badge badge-{{ $report->type }}">{{ $report->type === 'missed_collection' ? 'Missed Collection' : 'Illegal Dumping' }}</span></td>
                <td class="desc">{{ \Str::limit($report->description, 60) }}</td>
                <td><span class="badge badge-{{ $report->status }}">{{ ucfirst($report->status) }}</span></td>
                <td>{{ $report->respondedBy->name ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center; color:#888;">No reports found for the selected period.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('F d, Y h:i A') }} &bull; Smart Waste Collection Monitoring System
    </div>
</body>
</html>
