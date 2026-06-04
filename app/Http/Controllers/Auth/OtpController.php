<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Services\SemaphoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OtpController extends Controller
{
    public function __construct(
        protected OtpService $otpService,
        protected SemaphoreService $semaphoreService,
    ) {}

    public function showLoginForm()
    {
        return Inertia::render('Auth/Login');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^(09|\+639)[0-9]{9}$/'],
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'No account found with this phone number.']);
        }

        if ($user->status === 'pending') {
            return back()->withErrors(['phone' => 'Your account is pending approval.']);
        }

        if ($user->status === 'rejected') {
            return back()->withErrors(['phone' => 'Your account has been rejected.']);
        }

        $code = $this->otpService->generate($request->phone);
        $this->semaphoreService->sendOtp($request->phone, $code);

        return back()->with('otpSent', true);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $valid = $this->otpService->verify($request->phone, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Invalid or expired OTP.']);
        }

        $user = User::where('phone', $request->phone)->firstOrFail();

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}