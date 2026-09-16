<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complaints &amp; Reports Summary</title>
    <style>
        @page { margin: 25px 30px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1e293b; margin: 0; padding: 0; line-height: 1.4; }
        
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #e11d48; padding-bottom: 12px; }
        .republic { font-size: 8px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin: 0; }
        .brgy { font-size: 14px; font-weight: 800; color: #be123c; margin: 2px 0 4px; letter-spacing: 0.5px; }
        .office { font-size: 9px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; }
        .report-title { font-size: 13px; font-weight: 800; color: #0f172a; margin: 10px 0 2px; text-transform: uppercase; }
        .meta-period { font-size: 9px; color: #64748b; margin: 0; }

        .metrics-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .metric-cell { padding: 8px 12px; text-align: center; border: 1px solid #e2e8f0; border-radius: 6px; }
        .metric-label { font-size: 8px; text-transform: uppercase; font-weight: 700; color: #64748b; margin-bottom: 2px; }
        .metric-val { font-size: 14px; font-weight: 800; }
        
        .metric-total { background-color: #f8fafc; color: #0f172a; }
        .metric-missed { background-color: #fff7ed; color: #c2410c; border-color: #fed7aa; }
        .metric-dumping { background-color: #fef2f2; color: #b91c1c; border-color: #fecaca; }
        .metric-resolved { background-color: #ecfdf5; color: #047857; border-color: #a7f3d0; }

        .filter-badges { margin-bottom: 12px; font-size: 9px; color: #475569; }
        .filter-item { display: inline-block; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; margin-right: 6px; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table.data-table thead { background-color: #be123c; color: #ffffff; }
        table.data-table th { padding: 6px 8px; font-size: 9px; text-align: left; font-weight: 700; letter-spacing: 0.3px; }
        table.data-table tbody tr { border-bottom: 1px solid #e2e8f0; }
        table.data-table tbody tr:nth-child(even) { background-color: #f8fafc; }
        table.data-table td { padding: 5px 8px; font-size: 9px; vertical-align: top; }

        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 8px; font-weight: 700; text-transform: uppercase; }
        .badge-missed_collection { background-color: #ffedd5; color: #9a3412; }
        .badge-illegal_dumping { background-color: #fee2e2; color: #991b1b; }
        .badge-pending { background-color: #fef3c7; color: #92400e; }
        .badge-resolved { background-color: #d1fae5; color: #065f46; }

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
        <p class="office">Resident Grievance &amp; Environmental Sanitation Desk</p>
        <h1 class="report-title">Complaints &amp; Waste Incidents Summary Report</h1>
        <p class="meta-period">
            Period: {{ $from ? \Carbon\Carbon::parse($from)->format('M d, Y') : 'All Recorded Dates' }} &mdash; {{ $to ? \Carbon\Carbon::parse($to)->format('M d, Y') : 'Present' }}
        </p>
    </div>

    @if($selectedZone || $type || $status)
    <div class="filter-badges">
        <strong>Filters Applied:</strong>
        @if($selectedZone)
            <span class="filter-item">Purok / Zone: <strong>{{ $selectedZone }}</strong></span>
        @endif
        @if($type)
            <span class="filter-item">Report Type: <strong>{{ $type === 'missed_collection' ? 'Missed Collection' : 'Illegal Dumping' }}</strong></span>
        @endif
        @if($status)
            <span class="filter-item">Status: <strong>{{ ucfirst($status) }}</strong></span>
        @endif
    </div>
    @endif

    <table class="metrics-table">
        <tr>
            <td class="metric-cell metric-total" style="width: 25%;">
                <div class="metric-label">Total Reports</div>
                <div class="metric-val">{{ $reports->count() }}</div>
            </td>
            <td class="metric-cell metric-missed" style="width: 25%;">
                <div class="metric-label">Missed Collection</div>
                <div class="metric-val">{{ $reports->where('type', 'missed_collection')->count() }}</div>
            </td>
            <td class="metric-cell metric-dumping" style="width: 25%;">
                <div class="metric-label">Illegal Dumping</div>
                <div class="metric-val">{{ $reports->where('type', 'illegal_dumping')->count() }}</div>
            </td>
            <td class="metric-cell metric-resolved" style="width: 25%;">
                <div class="metric-label">Resolved / Addressed</div>
                <div class="metric-val">{{ $reports->whereIn('status', ['resolved', 'reviewed'])->count() }}</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th style="width: 70px;">Date</th>
                <th style="width: 120px;">Resident Name &amp; Purok</th>
                <th style="width: 105px;">Incident Type</th>
                <th>Concern Description</th>
                <th style="width: 65px;">Status</th>
                <th style="width: 110px;">Action Taken / By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $i => $report)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $report->created_at->format('M d, Y') }}</td>
                <td>
                    <strong>{{ $report->resident->name ?? 'Unknown Resident' }}</strong>
                    <div style="font-size: 8px; color: #64748b;">{{ $report->resident->zone->name ?? '—' }}</div>
                </td>
                <td>
                    <span class="badge badge-{{ $report->type }}">
                        {{ $report->type === 'missed_collection' ? 'Missed Collection' : 'Illegal Dumping' }}
                    </span>
                </td>
                <td>
                    <div>{{ \Illuminate\Support\Str::limit($report->description, 120) }}</div>
                    @if($report->latitude && $report->longitude)
                        <div style="font-size: 7.5px; color: #64748b; margin-top: 2px;">GPS: {{ round($report->latitude, 5) }}, {{ round($report->longitude, 5) }}</div>
                    @endif
                </td>
                <td><span class="badge badge-{{ $report->status }}">{{ ucfirst($report->status) }}</span></td>
                <td>
                    @if($report->official_response)
                        <div style="font-size: 8px; color: #065f46;">{{ \Illuminate\Support\Str::limit($report->official_response, 60) }}</div>
                        <div style="font-size: 7.5px; color: #64748b;">By: {{ $report->respondedBy->name ?? 'Official' }}</div>
                    @else
                        <span style="color: #94a3b8; font-style: italic;">Awaiting action</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px; color: #94a3b8;">
                    No reports or complaints found matching the selected filter criteria.
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
