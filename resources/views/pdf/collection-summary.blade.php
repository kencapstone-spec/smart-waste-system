<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Collection Summary Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #16a34a; padding-bottom: 12px; }
        .header h1 { font-size: 18px; color: #16a34a; margin: 0 0 4px; }
        .header p { margin: 2px 0; color: #555; font-size: 10px; }
        .meta { margin-bottom: 16px; font-size: 10px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead { background-color: #16a34a; color: white; }
        thead th { padding: 7px 10px; text-align: left; font-size: 10px; }
        tbody tr:nth-child(even) { background-color: #f0fdf4; }
        tbody td { padding: 6px 10px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; }
        .badge-completed { background: #dcfce7; color: #166534; }
        .badge-missed { background: #fee2e2; color: #991b1b; }
        .badge-pending { background: #fef9c3; color: #854d0e; }
        .footer { margin-top: 24px; font-size: 9px; color: #888; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 10px; }
        .summary-box { display: inline-block; padding: 8px 16px; margin: 4px; border-radius: 6px; font-size: 11px; }
        .summary-section { margin-bottom: 16px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Smart Waste Collection System</h1>
        <p>Barangay San Isidro, Talibon, Bohol</p>
        <p style="font-size:13px; font-weight:bold; margin-top:8px;">Collection Activity Summary Report</p>
        @if($from || $to)
            <p>Period: {{ $from ? \Carbon\Carbon::parse($from)->format('M d, Y') : 'All time' }} &mdash; {{ $to ? \Carbon\Carbon::parse($to)->format('M d, Y') : 'Present' }}</p>
        @endif
    </div>

    <div class="summary-section">
        <table style="width:auto; margin-bottom:12px;">
            <tr>
                <td style="padding:6px 16px; background:#dcfce7; border-radius:4px; color:#166534; font-weight:bold;">Completed: {{ $tasks->where('status','completed')->count() }}</td>
                <td style="padding:6px 16px; background:#fee2e2; border-radius:4px; color:#991b1b; font-weight:bold; margin-left:8px;">Missed: {{ $tasks->where('status','missed')->count() }}</td>
                <td style="padding:6px 16px; background:#fef9c3; border-radius:4px; color:#854d0e; font-weight:bold;">Pending: {{ $tasks->where('status','pending')->count() }}</td>
                <td style="padding:6px 16px; background:#f3f4f6; border-radius:4px; font-weight:bold;">Total: {{ $tasks->count() }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Street</th>
                <th>Zone</th>
                <th>Personnel</th>
                <th>Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $i => $task)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($task->collection_date)->format('M d, Y') }}</td>
                <td>{{ $task->schedule->street->name ?? '—' }}</td>
                <td>{{ $task->schedule->street->zone->name ?? '—' }}</td>
                <td>{{ $task->personnel->name ?? '—' }}</td>
                <td><span class="badge badge-{{ $task->status }}">{{ ucfirst($task->status) }}</span></td>
                <td>{{ $task->remarks ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center; color:#888;">No collection tasks found for the selected period.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('F d, Y h:i A') }} &bull; Smart Waste Collection Monitoring System
    </div>
</body>
</html>
