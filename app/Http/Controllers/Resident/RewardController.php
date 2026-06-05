<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\Redemption;
use App\Models\Point;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

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
            'availablePoints' => $availablePoints
        ]);
    }

    public function redeem(Request $request, Reward $reward)
    {
        $user = Auth::user();

        if (!$reward->is_active || $reward->stock <= 0) {
            return back()->withErrors(['reward' => 'This reward is currently unavailable.']);
        }

        $totalPoints = Point::where('resident_id', $user->id)->sum('points');
        $spentPoints = Redemption::where('resident_id', $user->id)->whereIn('status', ['pending', 'approved'])->sum('points_spent');
        $availablePoints = $totalPoints - $spentPoints;

        if ($availablePoints < $reward->points_required) {
            return back()->withErrors(['points' => 'Insufficient points to redeem this reward.']);
        }

        Redemption::create([
            'resident_id' => $user->id,
            'reward_id' => $reward->id,
            'points_spent' => $reward->points_required,
            'status' => 'pending'
        ]);

        $reward->decrement('stock');

        return back()->with('success', 'Reward redemption requested successfully!');
    }
}
