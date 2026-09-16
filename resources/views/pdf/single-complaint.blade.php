<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Incident Report #{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page { margin: 25px 30px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1e293b; margin: 0; padding: 0; line-height: 1.5; }
        
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #be123c; padding-bottom: 12px; }
        .republic { font-size: 8px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin: 0; }
        .brgy { font-size: 14px; font-weight: 800; color: #be123c; margin: 2px 0 4px; letter-spacing: 0.5px; }
        .office { font-size: 9px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; }
        .report-title { font-size: 13px; font-weight: 800; color: #0f172a; margin: 10px 0 2px; text-transform: uppercase; }
        .meta-ref { font-size: 10px; font-weight: 700; color: #be123c; margin: 0; }

        .section-header { font-size: 10px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px; background: #f1f5f9; padding: 5px 10px; border-left: 4px solid #be123c; margin: 15px 0 8px; }

        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table td { padding: 6px 10px; font-size: 9.5px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        .info-label { width: 28%; font-weight: 700; color: #64748b; }
        .info-value { width: 72%; color: #0f172a; }

        .narrative-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; font-size: 9.5px; color: #334155; margin-bottom: 12px; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 8.5px; font-weight: 700; text-transform: uppercase; }
        .badge-missed_collection { background-color: #ffedd5; color: #9a3412; }
        .badge-illegal_dumping { background-color: #fee2e2; color: #991b1b; }
        .badge-pending { background-color: #fef3c7; color: #92400e; }
        .badge-resolved { background-color: #d1fae5; color: #065f46; }

        .resolution-box { background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 10px 12px; font-size: 9.5px; color: #065f46; margin-bottom: 12px; }

        .photos-container { margin-top: 10px; margin-bottom: 15px; }
        .photo-wrapper { display: inline-block; margin-right: 10px; margin-bottom: 10px; vertical-align: top; text-align: center; }
        .photo-img { width: 140px; height: 105px; object-fit: cover; border: 1px solid #cbd5e1; border-radius: 4px; }
        .photo-caption { font-size: 8px; color: #64748b; margin-top: 3px; }

        .signatories { width: 100%; margin-top: 40px; border-collapse: collapse; }
        .signatory-box { width: 50%; vertical-align: top; padding: 0 20px; }
        .signatory-label { font-size: 9px; color: #64748b; margin-bottom: 40px; }
        .signatory-name { font-size: 10px; font-weight: 800; color: #0f172a; text-decoration: underline; text-underline-offset: 3px; }
        .signatory-title { font-size: 9px; color: #475569; margin-top: 2px; }

        .footer { margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 8px; font-size: 8px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <p class="republic">Republic of the Philippines &bull; Province of Bohol &bull; Municipality of Talibon</p>
        <p class="brgy">BARANGAY SAN ISIDRO</p>
        <p class="office">Office of the Barangay Council &bull; Committee on Environmental Protection</p>
        <h1 class="report-title">Waste Incident &amp; Resident Concern Report</h1>
        <p class="meta-ref">Reference No.: IR-{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }}</p>
    </div>

    <div class="section-header">1. Case &amp; Incident Information</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Incident Type:</td>
            <td class="info-value">
                <span class="badge badge-{{ $report->type }}">
                    {{ $report->type === 'missed_collection' ? 'Missed Collection' : 'Illegal Dumping' }}
                </span>
            </td>
        </tr>
        <tr>
            <td class="info-label">Current Status:</td>
            <td class="info-value">
                <span class="badge badge-{{ $report->status }}">
                    {{ ucfirst($report->status) }}
                </span>
            </td>
        </tr>
        <tr>
            <td class="info-label">Date &amp; Time Logged:</td>
            <td class="info-value">{{ $report->created_at->format('F d, Y \a\t h:i A') }}</td>
        </tr>
        <tr>
            <td class="info-label">Complainant / Resident:</td>
            <td class="info-value"><strong>{{ $report->resident->name ?? 'Anonymous / Resident' }}</strong></td>
        </tr>
        <tr>
            <td class="info-label">Contact Number:</td>
            <td class="info-value">{{ $report->resident->phone ?? '—' }}</td>
        </tr>
        <tr>
            <td class="info-label">Registered Purok:</td>
            <td class="info-value">{{ $report->resident->zone->name ?? '—' }}</td>
        </tr>
        @if($report->latitude && $report->longitude)
        <tr>
            <td class="info-label">GPS Geolocation:</td>
            <td class="info-value">{{ round($report->latitude, 6) }}, {{ round($report->longitude, 6) }}</td>
        </tr>
        @endif
    </table>

    <div class="section-header">2. Narrative of Concern / Description</div>
    <div class="narrative-box">
        {{ $report->description }}
    </div>

    @if($report->photos && $report->photos->count() > 0)
    <div class="section-header">3. Photographic Evidence Attached</div>
    <div class="photos-container">
        @foreach($report->photos as $pIdx => $photo)
            @php
                $photoPath = public_path('storage/' . $photo->photo_path);
            @endphp
            @if(file_exists($photoPath))
                <div class="photo-wrapper">
                    <img src="{{ $photoPath }}" class="photo-img" alt="Evidence #{{ $pIdx + 1 }}" />
                    <div class="photo-caption">Exhibit {{ chr(65 + $pIdx) }}</div>
                </div>
            @endif
        @endforeach
    </div>
    @endif

    <div class="section-header">{{ $report->photos && $report->photos->count() > 0 ? '4' : '3' }}. Official Action Taken &amp; Resolution</div>
    @if($report->official_response)
        <div class="resolution-box">
            <strong>Official Response / Investigation Remarks:</strong><br />
            {{ $report->official_response }}
            <div style="font-size: 8px; color: #065f46; margin-top: 6px;">
                Responded on {{ \Carbon\Carbon::parse($report->responded_at)->format('F d, Y \a\t h:i A') }} by <strong>{{ $report->respondedBy->name ?? 'Barangay Official' }}</strong>
            </div>
        </div>
    @else
        <div class="narrative-box" style="font-style: italic; color: #64748b;">
            This incident report is currently under review. No official resolution remarks have been recorded yet.
        </div>
    @endif

    <table class="signatories">
        <tr>
            <td class="signatory-box">
                <div class="signatory-label">Investigated &amp; Certified by:</div>
                <div class="signatory-name">{{ strtoupper($report->respondedBy->name ?? $generatedBy) }}</div>
                <div class="signatory-title">Barangay Official &bull; Solid Waste Committee</div>
            </td>
            <td class="signatory-box">
                <div class="signatory-label">Approved &amp; Noted by:</div>
                <div class="signatory-name">HON. BARANGAY CAPTAIN</div>
                <div class="signatory-title">Punong Barangay &bull; Barangay San Isidro</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Smart Waste Collection Monitoring &amp; Scheduling System (SWCMSS) &bull; Document Reference: IR-{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }} &bull; Generated on {{ now()->format('F d, Y h:i A') }}
    </div>
</body>
</html>
