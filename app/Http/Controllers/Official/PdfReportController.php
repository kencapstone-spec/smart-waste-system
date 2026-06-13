<?php

namespace App\Http\Controllers\Official;

use App\Http\Controllers\Controller;
use App\Models\CollectionTask;
use App\Models\Report;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfReportController extends Controller
{
    public function collectionSummary(Request $request)
    {
        $tasks = CollectionTask::with(['schedule.zone', 'personnel'])
            ->when($request->from, fn ($q) => $q->whereDate('collection_date', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->whereDate('collection_date', '<=', $request->to))
            ->latest()
            ->get();

        $pdf = Pdf::loadView('pdf.collection-summary', [
            'tasks' => $tasks,
            'from' => $request->from,
            'to' => $request->to,
        ]);

        return $pdf->download('collection-summary.pdf');
    }

    public function complaintsSummary(Request $request)
    {
        $reports = Report::with(['resident', 'respondedBy'])
            ->when($request->from, fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->get();

        $pdf = Pdf::loadView('pdf.complaints-summary', [
            'reports' => $reports,
            'from' => $request->from,
            'to' => $request->to,
        ]);

        return $pdf->download('complaints-summary.pdf');
    }

    public function residentParticipation(Request $request)
    {
        $residents = User::with(['points', 'zone'])
            ->where('role', 'resident')
            ->where('status', 'active')
            ->get()
            ->map(function ($resident) {
                $resident->total_points = $resident->points->sum('points');

                return $resident;
            })
            ->sortByDesc('total_points')
            ->values();

        $pdf = Pdf::loadView('pdf.resident-participation', [
            'residents' => $residents,
        ]);

        return $pdf->download('resident-participation.pdf');
    }
}
