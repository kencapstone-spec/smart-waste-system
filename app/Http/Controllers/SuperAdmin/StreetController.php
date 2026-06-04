<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Street;
use App\Models\Zone;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StreetController extends Controller
{
    public function index()
    {
        $streets = Street::with('zone')->latest()->get();
        $zones = Zone::orderBy('name')->get(['id', 'name']);

        return Inertia::render('SuperAdmin/Streets/Index', [
            'streets' => $streets,
            'zones' => $zones,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'zone_id' => ['required', 'exists:zones,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        Street::create($request->only('zone_id', 'name'));

        return back()->with('success', 'Street created successfully.');
    }

    public function update(Request $request, Street $street)
    {
        $request->validate([
            'zone_id' => ['required', 'exists:zones,id'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $street->update($request->only('zone_id', 'name'));

        return back()->with('success', 'Street updated successfully.');
    }

    public function destroy(Street $street)
    {
        $street->delete();

        return back()->with('success', 'Street deleted successfully.');
    }
}