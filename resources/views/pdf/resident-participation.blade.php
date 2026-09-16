<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resident Participation &amp; Points Ranking</title>
    <style>
        @page { margin: 25px 30px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1e293b; margin: 0; padding: 0; line-height: 1.4; }
        
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #0284c7; padding-bottom: 12px; }
        .republic { font-size: 8px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin: 0; }
        .brgy { font-size: 14px; font-weight: 800; color: #0369a1; margin: 2px 0 4px; letter-spacing: 0.5px; }
        .office { font-size: 9px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; }
        .report-title { font-size: 13px; font-weight: 800; color: #0f172a; margin: 10px 0 2px; text-transform: uppercase; }
        .meta-period { font-size: 9px; color: #64748b; margin: 0; }

        .metrics-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .metric-cell { padding: 8px 12px; text-align: center; border: 1px solid #e2e8f0; border-radius: 6px; }
        .metric-label { font-size: 8px; text-transform: uppercase; font-weight: 700; color: #64748b; margin-bottom: 2px; }
        .metric-val { font-size: 14px; font-weight: 800; }
        
        .metric-total { background-color: #f8fafc; color: #0f172a; }
        .metric-points { background-color: #f0fdf4; color: #166534; border-color: #bbf7d0; }
        .metric-avg { background-color: #f0f9ff; color: #0369a1; border-color: #bae6fd; }

        .filter-badges { margin-bottom: 12px; font-size: 9px; color: #475569; }
        .filter-item { display: inline-block; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; margin-right: 6px; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table.data-table thead { background-color: #0284c7; color: #ffffff; }
        table.data-table th { padding: 6px 8px; font-size: 9px; text-align: left; font-weight: 700; letter-spacing: 0.3px; }
        table.data-table tbody tr { border-bottom: 1px solid #e2e8f0; }
        table.data-table tbody tr:nth-child(even) { background-color: #f8fafc; }
        table.data-table td { padding: 6px 8px; font-size: 9px; vertical-align: middle; }

        .rank-badge { display: inline-block; width: 22px; height: 22px; line-height: 22px; text-align: center; border-radius: 50%; font-size: 9px; font-weight: 800; }
        .rank-1 { background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .rank-2 { background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .rank-3 { background-color: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }
        .rank-other { color: #64748b; font-weight: 700; }

        .points-pill { font-weight: 800; color: #166534; background: #dcfce7; padding: 2px 8px; border-radius: 10px; display: inline-block; }

        .signatories { width: 100%; margin-top: 35px; border-collapse: collapse; }
        .signatory-box { width: 50%; vertical-align: top; padding: 0 20px; }
        .signatory-label { font-size: 9px; color: #64748b; margin-bottom: 35px; }
        .signatory-name { font-size: 10px; font-weight: 800; color: #0f172a; text-decoration: underline; text-underline-offset: 3px; }
        .signatory-title { font-size: 9px; color: #475569; margin-top: 2px; }

        .footer { margin-top: 25px; border-top: 1px solid #e2e8f0; padding-top: 8px; font-size: 8px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <p class="republic">Republic of the Philippines &bull; Province of Bohol &bull; Municipality of Talibon</p>
        <p class="brgy">BARANGAY SAN ISIDRO</p>
        <p class="office">Ecological Waste Incentive &amp; Community Engagement Program</p>
        <h1 class="report-title">Resident Waste Disposal Participation &amp; Points Ranking</h1>
        <p class="meta-period">Report Generated on: {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    @if($selectedZone || $minPoints)
    <div class="filter-badges">
        <strong>Filters Applied:</strong>
        @if($selectedZone)
            <span class="filter-item">Purok / Zone: <strong>{{ $selectedZone }}</strong></span>
        @endif
        @if($minPoints)
            <span class="filter-item">Minimum Points: <strong>{{ $minPoints }}</strong></span>
        @endif
    </div>
    @endif

    <table class="metrics-table">
        <tr>
            <td class="metric-cell metric-total" style="width: 33%;">
                <div class="metric-label">Active Enrolled Residents</div>
                <div class="metric-val">{{ $residents->count() }}</div>
            </td>
            <td class="metric-cell metric-points" style="width: 33%;">
                <div class="metric-label">Total Incentive Points Awarded</div>
                <div class="metric-val">{{ number_format($residents->sum('total_points')) }}</div>
            </td>
            <td class="metric-cell metric-avg" style="width: 34%;">
                <div class="metric-label">Average Points per Resident</div>
                <div class="metric-val">{{ $residents->count() > 0 ? round($residents->avg('total_points'), 1) : 0 }}</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">Rank</th>
                <th>Resident Full Name</th>
                <th style="width: 110px;">Contact Number</th>
                <th style="width: 140px;">Registered Purok</th>
                <th style="width: 100px; text-align: right;">Points Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($residents as $i => $resident)
            <tr>
                <td style="text-align: center;">
                    @if($i === 0)
                        <span class="rank-badge rank-1">1</span>
                    @elseif($i === 1)
                        <span class="rank-badge rank-2">2</span>
                    @elseif($i === 2)
                        <span class="rank-badge rank-3">3</span>
                    @else
                        <span class="rank-other">#{{ $i + 1 }}</span>
                    @endif
                </td>
                <td><strong>{{ $resident->name }}</strong></td>
                <td>{{ $resident->phone }}</td>
                <td>{{ $resident->zone->name ?? '—' }}</td>
                <td style="text-align: right;">
                    <span class="points-pill">{{ number_format($resident->total_points) }} pts</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px; color: #94a3b8;">
                    No active residents found matching the criteria.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signatories">
        <tr>
            <td class="signatory-box">
                <div class="signatory-label">Prepared &amp; Certified by:</div>
                <div class="signatory-name">{{ strtoupper($generatedBy) }}</div>
                <div class="signatory-title">Barangay Official / SWM Desk Officer</div>
            </td>
            <td class="signatory-box">
                <div class="signatory-label">Attested &amp; Noted by:</div>
                <div class="signatory-name">HON. BARANGAY CAPTAIN</div>
                <div class="signatory-title">Punong Barangay &bull; Barangay San Isidro</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Smart Waste Collection Monitoring &amp; Scheduling System (SWCMSS) &bull; Generated on {{ now()->format('F d, Y h:i A') }} &bull; Page 1 of 1
    </div>
</body>
</html>
