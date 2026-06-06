<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::with('photos')
            ->where('resident_id', Auth::id())
            ->latest()
            ->paginate(15);

        return Inertia::render('Resident/Reports/Index', [
            'reports' => $reports,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:missed_collection,illegal_dumping'],
            'description' => ['required', 'string', 'max:2000'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['image', 'max:5120'],
        ]);

        $report = Report::create([
            'resident_id' => Auth::id(),
            'type' => $request->type,
            'description' => $request->description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => 'pending',
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('reports', 'public');
                $report->photos()->create(['photo_path' => $path]);
            }
        }

        return back()->with('success', 'Report submitted successfully.');
    }
}
