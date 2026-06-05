<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use App\Models\Street;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return Inertia::render('Profile/Edit', [
            'user' => $request->user()->load('street.zone'),
            'streets' => Street::with('zone')->orderBy('name')->get(['id', 'name', 'zone_id']),
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
            $rules['street_id'] = ['required', 'exists:streets,id'];
        }

        $validated = $request->validate($rules);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }
}
