<?php

namespace App\Services;

use App\Models\OtpCode;
use Illuminate\Support\Str;

class OtpService
{
    public function generate(string $phone, ?string $customCode = null): string
    {
        OtpCode::where('phone', $phone)
            ->where('used', false)
            ->delete();

        $code = $customCode ?? str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'phone' => $phone,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
            'used' => false,
        ]);

        return $code;
    }

    public function verify(string $phone, string $code): bool
    {
        $otp = OtpCode::where('phone', $phone)
            ->where('code', $code)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp) {
            return false;
        }

        $otp->update(['used' => true]);

        return true;
    }
}