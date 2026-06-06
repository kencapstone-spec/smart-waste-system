<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Point;
use App\Models\Redemption;
use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RewardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $totalPoints = Point::where('resident_id', $user->id)->sum('points');
        $spentPoints = Redemption::where('resident_id', $user->id)->whereIn('status', ['pending', 'approved'])->sum('points_spent');
        $availablePoints = $totalPoints - $spentPoints;

        $rewards = Reward::where('is_active', true)->where('stock', '>', 0)->latest()->get();
        $redemptions = Redemption::with('reward')->where('resident_id', $user->id)->latest()->get();

        return Inertia::render('Resident/Rewards/Index', [
            'rewards' => $rewards,
            'redemptions' => $redemptions,
            'availablePoints' => $availablePoints,
        ]);
    }

    public function redeem(Request $request, Reward $reward)
    {
        $user = Auth::user();

        // Wrap the critical section in a transaction to prevent race conditions
        $result = DB::transaction(function () use ($user, $reward) {
            // Lock the reward row to prevent concurrent redemptions of the same stock
            $lockedReward = Reward::where('id', $reward->id)->lockForUpdate()->first();

            if (! $lockedReward->is_active || $lockedReward->stock <= 0) {
                return ['error' => 'reward', 'message' => 'This reward is currently unavailable.'];
            }

            $totalPoints = Point::where('resident_id', $user->id)->sum('points');
            $spentPoints = Redemption::where('resident_id', $user->id)->whereIn('status', ['pending', 'approved'])->sum('points_spent');
            $availablePoints = $totalPoints - $spentPoints;

            if ($availablePoints < $lockedReward->points_required) {
                return ['error' => 'points', 'message' => 'Insufficient points to redeem this reward.'];
            }

            Redemption::create([
                'resident_id' => $user->id,
                'reward_id' => $lockedReward->id,
                'points_spent' => $lockedReward->points_required,
                'status' => 'pending',
            ]);

            $lockedReward->decrement('stock');

            return ['success' => true];
        });

        if (isset($result['error'])) {
            return back()->withErrors([$result['error'] => $result['message']]);
        }

        return back()->with('success', 'Reward redemption requested successfully!');
    }
}
