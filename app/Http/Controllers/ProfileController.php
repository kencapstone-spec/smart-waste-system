<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return Inertia::render('Profile/Edit', [
            'user' => $request->user()->load('zone'),
            'zones' => Zone::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(09|\+639)[0-9]{9}$/', Rule::unique('users')->ignore($user->id)],
        ];

        if ($user->role === 'resident') {
            $rules['address'] = ['required', 'string', 'max:255'];
            $rules['zone_id'] = ['required', 'exists:zones,id'];
        }

        $validated = $request->validate($rules);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }
}
