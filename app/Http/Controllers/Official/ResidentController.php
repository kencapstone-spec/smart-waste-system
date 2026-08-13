<?php

namespace App\Http\Controllers\Official;

use App\Http\Controllers\Controller;
use App\Models\Point;
use App\Models\User;
use App\Services\SemaphoreService;
use Inertia\Inertia;

class ResidentController extends Controller
{
    public function __construct(protected SemaphoreService $semaphoreService) {}

    public function index()
    {
        $residents = User::with('zone')
            ->where('role', 'resident')
            ->latest()
            ->paginate(15);

        return Inertia::render('Official/Residents/Index', [
            'residents' => $residents,
            'zones'     => \App\Models\Zone::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(User $resident)
    {
        $points = Point::with('awardedBy', 'collectionTask')
            ->where('resident_id', $resident->id)
            ->latest()
            ->get();

        return Inertia::render('Official/Residents/Show', [
            'resident' => $resident->load('zone'),
            'points' => $points,
            'totalPoints' => $points->sum('points'),
        ]);
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'address' => 'required|string|max:255',
            'zone_id' => 'required|exists:zones,id',
        ]);

        $resident = User::create([
            ...$validated,
            'role' => 'resident',
            'status' => 'active',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        $this->semaphoreService->sendSms(
            $resident->phone,
            'Your Smart Waste System account has been created by the Barangay Office. You can now log in using your phone number.'
        );

        return back()->with('success', 'Resident registered successfully.');
    }

    public function approve(User $resident)
    {
        $resident->update([
            'status' => 'active',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        $this->semaphoreService->sendSms(
            $resident->phone,
            'Congratulations! Your Smart Waste System account has been approved. You can now log in.'
        );

        return back()->with('success', 'Resident approved successfully.');
    }

    public function reject(User $resident)
    {
        $resident->update(['status' => 'rejected']);

        $this->semaphoreService->sendSms(
            $resident->phone,
            'Your Smart Waste System account registration has been rejected. Please contact the Barangay Office for more information.'
        );

        return back()->with('success', 'Resident rejected.');
    }

    public function deactivate(User $resident)
    {
        $resident->update(['status' => 'rejected']);

        $this->semaphoreService->sendSms(
            $resident->phone,
            'Your Smart Waste System account has been deactivated. Please contact the Barangay Office if this is a mistake.'
        );

        return back()->with('success', 'Resident deactivated.');
    }

    public function reactivate(User $resident)
    {
        $resident->update(['status' => 'active']);

        $this->semaphoreService->sendSms(
            $resident->phone,
            'Your Smart Waste System account has been reactivated. You can now log in and use the system again.'
        );

        return back()->with('success', 'Resident reactivated successfully.');
    }

    public function destroy(User $resident)
    {
        $resident->delete();

        return back()->with('success', 'Resident account deleted successfully.');
    }
}
