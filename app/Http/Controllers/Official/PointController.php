<?php

namespace App\Http\Controllers\Official;

use App\Http\Controllers\Controller;
use App\Models\Point;
use Illuminate\Http\Request;

class PointController extends Controller
{
    /**
     * Update the specified point record.
     */
    public function update(Request $request, Point $point)
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:0',
            'remarks' => 'nullable|string|max:255',
        ]);

        $point->update($validated);

        return back()->with('success', 'Resident points updated successfully.');
    }
}
