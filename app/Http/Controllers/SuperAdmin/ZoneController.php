<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ZoneController extends Controller
{
    public function index()
    {
        $zones = Zone::withCount('streets')->latest()->get();

        return Inertia::render('SuperAdmin/Zones/Index', [
            'zones' => $zones,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:zones,name'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        Zone::create($request->only('name', 'description'));

        return back()->with('success', 'Zone created successfully.');
    }

    public function update(Request $request, Zone $zone)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:zones,name,'.$zone->id],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $zone->update($request->only('name', 'description'));

        return back()->with('success', 'Zone updated successfully.');
    }

    public function destroy(Zone $zone)
    {
        $zone->delete();

        return back()->with('success', 'Zone deleted successfully.');
    }
}
