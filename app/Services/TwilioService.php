<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected $twilio;
    protected $from;

    public function __construct()
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $this->from = config('services.twilio.from');

        $this->twilio = new Client($sid, $token);
    }

    /**
     * Send an SMS message using Twilio.
     *
     * @param  string  $to
     * @param  string  $message
     * @return bool
     */
    public function sendSms(string $to, string $message): bool
    {
        try {
            // Normalize Philippine number if starts with 0
            if (preg_match('/^0/', $to)) {
                $to = preg_replace('/^0/', '+63', $to);
            }

            $this->twilio->messages->create($to, [
                'from' => $this->from,
                'body' => $message,
            ]);

            Log::info("✅ SMS sent to {$to}");
            return true;
        } catch (\Exception $e) {
            Log::error('❌ Twilio SMS failed: ' . $e->getMessage());
            return false;
        }
    }
}
