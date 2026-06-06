<?php

namespace App\Http\Controllers\Official;

use App\Http\Controllers\Controller;
use App\Models\Redemption;
use App\Services\SemaphoreService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RedemptionController extends Controller
{
    public function __construct(protected SemaphoreService $semaphoreService) {}

    public function index()
    {
        $redemptions = Redemption::with(['resident', 'reward'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Official/Redemptions/Index', [
            'redemptions' => $redemptions,
        ]);
    }

    public function approve(Redemption $redemption)
    {
        $redemption->update([
            'status' => 'approved',
            'processed_by' => Auth::id(),
        ]);

        $this->semaphoreService->sendSms(
            $redemption->resident->phone,
            "Your reward redemption for '{$redemption->reward->name}' has been APPROVED! You can now claim it at the Barangay Office."
        );

        return back()->with('success', 'Redemption approved.');
    }

    public function reject(Redemption $redemption)
    {
        $redemption->update([
            'status' => 'rejected',
            'processed_by' => Auth::id(),
        ]);

        // Return stock
        $redemption->reward->increment('stock');

        $this->semaphoreService->sendSms(
            $redemption->resident->phone,
            "Your reward redemption for '{$redemption->reward->name}' was rejected. Your points have been refunded."
        );

        return back()->with('success', 'Redemption rejected.');
    }
}
