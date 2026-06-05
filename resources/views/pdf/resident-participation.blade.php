<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resident Participation Report</title>
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
        .points-col { font-weight: bold; color: #16a34a; }
        .footer { margin-top: 24px; font-size: 9px; color: #888; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 10px; }
        .rank-1 { color: #d97706; font-weight: bold; }
        .rank-2 { color: #6b7280; font-weight: bold; }
        .rank-3 { color: #92400e; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Smart Waste Collection System</h1>
        <p>Barangay San Isidro, Talibon, Bohol</p>
        <p style="font-size:13px; font-weight:bold; margin-top:8px;">Resident Participation Report</p>
        <p>Generated: {{ now()->format('F d, Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>Resident Name</th>
                <th>Phone</th>
                <th>Street</th>
                <th>Zone</th>
                <th>Total Points</th>
            </tr>
        </thead>
        <tbody>
            @forelse($residents as $i => $resident)
            <tr>
                <td class="{{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : '')) }}">
                    #{{ $i + 1 }}
                </td>
                <td>{{ $resident->name }}</td>
                <td>{{ $resident->phone }}</td>
                <td>{{ $resident->street->name ?? '—' }}</td>
                <td>{{ $resident->street->zone->name ?? '—' }}</td>
                <td class="points-col">{{ $resident->total_points }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center; color:#888;">No active residents found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('F d, Y h:i A') }} &bull; Smart Waste Collection Monitoring System
    </div>
</body>
</html>
