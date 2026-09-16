<?php

namespace App\Http\Controllers\Official;

use App\Http\Controllers\Controller;
use App\Models\CollectionTask;
use App\Models\Report;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Zone;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PdfReportController extends Controller
{
    /**
     * Display the Printed Reports center.
     */
    public function index()
    {
        $zones = \Illuminate\Support\Facades\Cache::remember('zones_dropdown', 300, function () {
            return Zone::orderBy('name')->get(['id', 'name']);
        });

        $stats = \Illuminate\Support\Facades\Cache::remember('printed_reports_summary_stats', 30, function () {
            return [
                'totalTasks' => CollectionTask::count(),
                'completedTasks' => CollectionTask::where('status', 'completed')->count(),
                'totalReports' => Report::count(),
                'pendingReports' => Report::where('status', 'pending')->count(),
                'resolvedReports' => Report::whereIn('status', ['resolved', 'reviewed'])->count(),
                'activeResidents' => User::where('role', 'resident')->where('status', 'active')->count(),
                'activeSchedules' => Schedule::where('status', 'active')->count(),
            ];
        });

        $recentReports = Report::with(['resident.zone', 'respondedBy'])
            ->latest()
            ->take(6)
            ->get();

        return Inertia::render('Official/PrintedReports/Index', [
            'zones' => $zones,
            'stats' => $stats,
            'recentReports' => $recentReports,
        ]);
    }

    /**
     * Generate Collection Activity Summary PDF.
     */
    public function collectionSummary(Request $request)
    {
        $tasks = CollectionTask::with(['schedule.zone', 'personnel'])
            ->when($request->filled('from'), fn ($q) => $q->whereDate('collection_date', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('collection_date', '<=', $request->to))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('zone_id'), function ($q) use ($request) {
                $q->whereHas('schedule', fn ($sq) => $sq->where('zone_id', $request->zone_id));
            })
            ->latest('collection_date')
            ->get();

        $selectedZone = $request->filled('zone_id') ? Zone::find($request->zone_id)?->name : null;

        $pdf = Pdf::loadView('pdf.collection-summary', [
            'tasks' => $tasks,
            'from' => $request->from,
            'to' => $request->to,
            'status' => $request->status,
            'selectedZone' => $selectedZone,
            'generatedBy' => Auth::user()?->name ?? 'Barangay Official',
        ]);

        $pdf->setPaper('a4', 'landscape');

        if ($request->get('action') === 'download') {
            return $pdf->download('collection-activity-summary.pdf');
        }

        return $pdf->stream('collection-activity-summary.pdf');
    }

    /**
     * Generate Complaints & Reports Summary PDF.
     */
    public function complaintsSummary(Request $request)
    {
        $reports = Report::with(['resident.zone', 'respondedBy'])
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('zone_id'), function ($q) use ($request) {
                $q->whereHas('resident', fn ($rq) => $rq->where('zone_id', $request->zone_id));
            })
            ->latest()
            ->get();

        $selectedZone = $request->filled('zone_id') ? Zone::find($request->zone_id)?->name : null;

        $pdf = Pdf::loadView('pdf.complaints-summary', [
            'reports' => $reports,
            'from' => $request->from,
            'to' => $request->to,
            'type' => $request->type,
            'status' => $request->status,
            'selectedZone' => $selectedZone,
            'generatedBy' => Auth::user()?->name ?? 'Barangay Official',
        ]);

        $pdf->setPaper('a4', 'landscape');

        if ($request->get('action') === 'download') {
            return $pdf->download('complaints-summary.pdf');
        }

        return $pdf->stream('complaints-summary.pdf');
    }

    /**
     * Generate Resident Participation & Points Report PDF.
     */
    public function residentParticipation(Request $request)
    {
        $query = User::with(['points', 'zone'])
            ->where('role', 'resident')
            ->where('status', 'active')
            ->when($request->filled('zone_id'), fn ($q) => $q->where('zone_id', $request->zone_id));

        $residents = $query->get()
            ->map(function ($resident) {
                $resident->total_points = (int) $resident->points->sum('points');
                return $resident;
            });

        if ($request->filled('min_points')) {
            $minPoints = (int) $request->min_points;
            $residents = $residents->filter(fn ($r) => $r->total_points >= $minPoints);
        }

        if ($request->get('sort') === 'name') {
            $residents = $residents->sortBy('name')->values();
        } else {
            $residents = $residents->sortByDesc('total_points')->values();
        }

        $selectedZone = $request->filled('zone_id') ? Zone::find($request->zone_id)?->name : null;

        $pdf = Pdf::loadView('pdf.resident-participation', [
            'residents' => $residents,
            'selectedZone' => $selectedZone,
            'minPoints' => $request->min_points,
            'generatedBy' => Auth::user()?->name ?? 'Barangay Official',
        ]);

        $pdf->setPaper('a4', 'portrait');

        if ($request->get('action') === 'download') {
            return $pdf->download('resident-participation.pdf');
        }

        return $pdf->stream('resident-participation.pdf');
    }

    /**
     * Generate Single Complaint Incident Report PDF.
     */
    public function singleComplaint(Report $report, Request $request)
    {
        $report->load(['resident.zone', 'respondedBy', 'photos']);

        $pdf = Pdf::loadView('pdf.single-complaint', [
            'report' => $report,
            'generatedBy' => Auth::user()?->name ?? 'Barangay Official',
        ]);

        $pdf->setPaper('a4', 'portrait');

        if ($request->get('action') === 'download') {
            return $pdf->download("incident-report-{$report->id}.pdf");
        }

        return $pdf->stream("incident-report-{$report->id}.pdf");
    }

    /**
     * Generate Collection Schedules Master List PDF.
     */
    public function schedulesSummary(Request $request)
    {
        $schedules = Schedule::with(['zone', 'assignments.personnel', 'createdBy'])
            ->when($request->filled('zone_id'), fn ($q) => $q->where('zone_id', $request->zone_id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('frequency'), fn ($q) => $q->where('frequency', $request->frequency))
            ->orderBy('zone_id')
            ->get();

        $selectedZone = $request->filled('zone_id') ? Zone::find($request->zone_id)?->name : null;

        $pdf = Pdf::loadView('pdf.schedules-summary', [
            'schedules' => $schedules,
            'selectedZone' => $selectedZone,
            'status' => $request->status,
            'frequency' => $request->frequency,
            'generatedBy' => Auth::user()?->name ?? 'Barangay Official',
        ]);

        $pdf->setPaper('a4', 'portrait');

        if ($request->get('action') === 'download') {
            return $pdf->download('collection-schedules-master-list.pdf');
        }

        return $pdf->stream('collection-schedules-master-list.pdf');
    }
}
