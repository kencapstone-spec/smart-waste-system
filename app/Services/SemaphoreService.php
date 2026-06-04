<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SemaphoreService
{
    protected string $apiKey;
    protected string $senderName;
    protected string $apiUrl = 'https://api.semaphore.co/api/v4/messages';

    public function __construct()
    {
        $this->apiKey = config('services.semaphore.api_key');
        $this->senderName = config('services.semaphore.sender_name');
    }

    public function sendSms(string $phone, string $message): bool
    {
        if ($this->apiKey === 'your_api_key_here' || empty($this->apiKey)) {
            Log::info("DEV MODE - SMS to {$phone}: {$message}");
            return true;
        }

        try {
            $response = Http::post($this->apiUrl, [
                'apikey' => $this->apiKey,
                'number' => $this->formatPhone($phone),
                'message' => $message,
                'sendername' => $this->senderName,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Semaphore SMS error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendOtp(string $phone, string $code): bool
    {
        $message = "Your Smart Waste System OTP is: {$code}. Valid for 10 minutes. Do not share this code.";
        return $this->sendSms($phone, $message);
    }

    protected function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '63' . substr($phone, 1);
        }

        if (!str_starts_with($phone, '63')) {
            $phone = '63' . $phone;
        }

        return $phone;
    }
}