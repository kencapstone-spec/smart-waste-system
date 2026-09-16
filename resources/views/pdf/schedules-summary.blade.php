<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Collection Schedules Master List</title>
    <style>
        @page { margin: 25px 30px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1e293b; margin: 0; padding: 0; line-height: 1.4; }
        
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 12px; }
        .republic { font-size: 8px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin: 0; }
        .brgy { font-size: 14px; font-weight: 800; color: #4338ca; margin: 2px 0 4px; letter-spacing: 0.5px; }
        .office { font-size: 9px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; }
        .report-title { font-size: 13px; font-weight: 800; color: #0f172a; margin: 10px 0 2px; text-transform: uppercase; }
        .meta-period { font-size: 9px; color: #64748b; margin: 0; }

        .metrics-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .metric-cell { padding: 8px 12px; text-align: center; border: 1px solid #e2e8f0; border-radius: 6px; }
        .metric-label { font-size: 8px; text-transform: uppercase; font-weight: 700; color: #64748b; margin-bottom: 2px; }
        .metric-val { font-size: 14px; font-weight: 800; }
        
        .metric-total { background-color: #f8fafc; color: #0f172a; }
        .metric-active { background-color: #ecfdf5; color: #047857; border-color: #a7f3d0; }
        .metric-inactive { background-color: #fef2f2; color: #b91c1c; border-color: #fecaca; }

        .filter-badges { margin-bottom: 12px; font-size: 9px; color: #475569; }
        .filter-item { display: inline-block; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; margin-right: 6px; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table.data-table thead { background-color: #4338ca; color: #ffffff; }
        table.data-table th { padding: 6px 8px; font-size: 9px; text-align: left; font-weight: 700; letter-spacing: 0.3px; }
        table.data-table tbody tr { border-bottom: 1px solid #e2e8f0; }
        table.data-table tbody tr:nth-child(even) { background-color: #f8fafc; }
        table.data-table td { padding: 6px 8px; font-size: 9px; vertical-align: middle; }

        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: 700; text-transform: uppercase; }
        .badge-active { background-color: #d1fae5; color: #065f46; }
        .badge-inactive { background-color: #fee2e2; color: #991b1b; }

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
        <p class="office">Solid Waste Management &amp; Collection Logistics Desk</p>
        <h1 class="report-title">Waste Collection Schedules Master List</h1>
        <p class="meta-period">Master Roster as of {{ now()->format('F d, Y') }}</p>
    </div>

    @if($selectedZone || $status || $frequency)
    <div class="filter-badges">
        <strong>Filters Applied:</strong>
        @if($selectedZone)
            <span class="filter-item">Purok: <strong>{{ $selectedZone }}</strong></span>
        @endif
        @if($frequency)
            <span class="filter-item">Frequency: <strong>{{ ucfirst($frequency) }}</strong></span>
        @endif
        @if($status)
            <span class="filter-item">Status: <strong>{{ ucfirst($status) }}</strong></span>
        @endif
    </div>
    @endif

    <table class="metrics-table">
        <tr>
            <td class="metric-cell metric-total" style="width: 33%;">
                <div class="metric-label">Total Schedules</div>
                <div class="metric-val">{{ $schedules->count() }}</div>
            </td>
            <td class="metric-cell metric-active" style="width: 33%;">
                <div class="metric-label">Active Schedules</div>
                <div class="metric-val">{{ $schedules->where('status', 'active')->count() }}</div>
            </td>
            <td class="metric-cell metric-inactive" style="width: 34%;">
                <div class="metric-label">Inactive / Completed Schedules</div>
                <div class="metric-val">{{ $schedules->where('status', '!=', 'active')->count() }}</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th style="width: 110px;">Purok</th>
                <th>Schedule Title</th>
                <th style="width: 75px;">Frequency</th>
                <th style="width: 80px;">Collection Time</th>
                <th>Assigned Personnel</th>
                <th style="width: 65px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schedules as $i => $sched)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $sched->zone->name ?? '—' }}</strong></td>
                <td>{{ $sched->title }}</td>
                <td style="text-transform: capitalize;">{{ $sched->frequency }}</td>
                <td>{{ $sched->collection_time }}</td>
                <td>
                    @if($sched->assignments && $sched->assignments->count() > 0)
                        {{ $sched->assignments->map(fn($a) => $a->personnel?->name)->filter()->join(', ') }}
                    @else
                        <span style="color: #94a3b8; font-style: italic;">None assigned</span>
                    @endif
                </td>
                <td>
                    <span class="badge badge-{{ $sched->status }}">
                        {{ ucfirst($sched->status) }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px; color: #94a3b8;">
                    No schedules found matching the filter criteria.
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
