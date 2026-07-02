<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppOtpService
{
    public function send(string $phone, string $code): void
    {
        /*
        |--------------------------------------------------------------------------
        | UltraMsg Config
        |--------------------------------------------------------------------------
        */

        $instanceId = env('ULTRAMSG_INSTANCE_ID');

        $token = env('ULTRAMSG_TOKEN');

        /*
        |--------------------------------------------------------------------------
        | API URL
        |--------------------------------------------------------------------------
        */

        $url = "https://api.ultramsg.com/{$instanceId}/messages/chat";

        /*
        |--------------------------------------------------------------------------
        | WhatsApp Message
        |--------------------------------------------------------------------------
        */

        $message = "رمز التحقق الخاص بك هو: {$code}";

        /*
        |--------------------------------------------------------------------------
        | Send Request
        |--------------------------------------------------------------------------
        */

        $response = Http::post($url, [
            'token' => $token,
            'to' => $phone,
            'body' => $message,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Logs
        |--------------------------------------------------------------------------
        */

        Log::info('UltraMsg response', [
            'status' => $response->status(),
            'body' => $response->json(),
        ]);
    }
}