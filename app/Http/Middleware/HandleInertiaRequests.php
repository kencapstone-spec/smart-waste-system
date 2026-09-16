<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    protected static ?string $assetVersion = null;

    public function version(Request $request): ?string
    {
        return static::$assetVersion ??= parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'phone' => $request->user()->phone,
                    'role' => $request->user()->role,
                    'status' => $request->user()->status,
                ] : null,
            ],
            'flash' => [
                'otpSent' => session('otpSent'),
                'success' => session('success'),
                'error' => session('error'),
            ],
        ];
    }
}
