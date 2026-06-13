<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('zone')
            ->whereIn('role', ['barangay_official', 'personnel'])
            ->latest()
            ->paginate(15);

        return Inertia::render('SuperAdmin/Users/Index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        return Inertia::render('SuperAdmin/Users/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(09|\+639)[0-9]{9}$/', 'unique:users,phone'],
            'role' => ['required', 'in:barangay_official,personnel'],
        ]);

        User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'role' => $request->role,
            'status' => 'active',
        ]);

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return Inertia::render('SuperAdmin/Users/Edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(09|\+639)[0-9]{9}$/', 'unique:users,phone,'.$user->id],
            'role' => ['required', 'in:barangay_official,personnel'],
            'status' => ['required', 'in:active,pending,rejected'],
        ]);

        $user->update($request->only('name', 'phone', 'role', 'status'));

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
