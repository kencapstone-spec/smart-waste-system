<?php

namespace App\Http\Controllers\Official;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Point;
use App\Services\SemaphoreService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ResidentController extends Controller
{
    public function __construct(protected SemaphoreService $semaphoreService) {}

    public function index()
    {
        $residents = User::with('street.zone')
            ->where('role', 'resident')
            ->latest()
            ->paginate(15);

        return Inertia::render('Official/Residents/Index', [
            'residents' => $residents,
        ]);
    }

    public function show(User $resident)
    {
        $points = Point::with('awardedBy', 'collectionTask')
            ->where('resident_id', $resident->id)
            ->latest()
            ->get();

        return Inertia::render('Official/Residents/Show', [
            'resident' => $resident->load('street.zone'),
            'points' => $points,
            'totalPoints' => $points->sum('points'),
        ]);
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
            "Congratulations! Your Smart Waste System account has been approved. You can now log in."
        );

        return back()->with('success', 'Resident approved successfully.');
    }

    public function reject(User $resident)
    {
        $resident->update(['status' => 'rejected']);

        $this->semaphoreService->sendSms(
            $resident->phone,
            "Your Smart Waste System account registration has been rejected. Please contact the Barangay Office for more information."
        );

        return back()->with('success', 'Resident rejected.');
    }

    public function deactivate(User $resident)
    {
        $resident->update(['status' => 'rejected']);

        $this->semaphoreService->sendSms(
            $resident->phone,
            "Your Smart Waste System account has been deactivated. Please contact the Barangay Office if this is a mistake."
        );

        return back()->with('success', 'Resident deactivated.');
    }

    public function destroy(User $resident)
    {
        $resident->delete();
        return back()->with('success', 'Resident account deleted successfully.');
    }
}