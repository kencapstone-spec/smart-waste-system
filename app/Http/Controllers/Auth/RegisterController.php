<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Street;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegisterController extends Controller
{
    public function show()
    {
        return Inertia::render('Auth/AuthScreen', [
            'activeTab' => 'register',
            'streets' => Street::with('zone')->orderBy('name')->get(['id', 'name', 'zone_id']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(09|\+639)[0-9]{9}$/', 'unique:users,phone'],
            'address' => ['required', 'string', 'max:500'],
            'street_id' => ['required', 'exists:streets,id'],
        ]);

        User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'street_id' => $request->street_id,
            'role' => 'resident',
            'status' => 'pending',
        ]);

        return back()->with('success', true);
    }
}
