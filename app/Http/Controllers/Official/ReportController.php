<?php

namespace App\Http\Controllers\Official;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['resident', 'photos', 'respondedBy'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Official/Reports/Index', [
            'reports' => $reports,
        ]);
    }

    public function show(Report $report)
    {
        return Inertia::render('Official/Reports/Show', [
            'report' => $report->load(['resident', 'photos', 'respondedBy']),
        ]);
    }

    public function respond(Request $request, Report $report)
    {
        $request->validate([
            'official_response' => ['required', 'string'],
            'status' => ['required', 'in:reviewed,resolved'],
        ]);

        $report->update([
            'official_response' => $request->official_response,
            'status' => $request->status,
            'responded_by' => Auth::id(),
            'responded_at' => now(),
        ]);

        return back()->with('success', 'Response submitted successfully.');
    }
}
