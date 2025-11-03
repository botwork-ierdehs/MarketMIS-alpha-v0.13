<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StallRental;
use App\Models\TableRental;
use App\Models\Volante;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckPermitRenewal extends Command
{
    protected $signature = 'rental:check-renewals';
    protected $description = 'Check rentals due for renewal and send SMS notifications.';

    public function handle()
    {
        $today = Carbon::today();

        $this->checkStallAndTable($today);
        $this->checkVolante($today);

        $this->info('✅ Renewal check with SMS notifications completed.');
        Log::info('Rental renewal cron ran successfully on ' . now());
    }

    private function checkStallAndTable($today)
    {
        $threeYearsAgo = $today->copy()->subYears(3);

        $stallRentals = StallRental::with('permits')->whereDate('issued_date', '<=', $threeYearsAgo)
            ->where('type', 1)
            ->get();

        $tableRentals = TableRental::with('permits')->whereDate('issued_date', '<=', $threeYearsAgo)
            ->where('type', 1)
            ->get();

        foreach ($stallRentals as $rental) {
            $this->notifyRenewal($rental, 'Stall Rental');
        }

        foreach ($tableRentals as $rental) {
            $this->notifyRenewal($rental, 'Table Rental');
        }
    }

    private function checkVolante($today)
    {
        $oneYearAgo = $today->copy()->subYear();

        $volanteRentals = Volante::with('permits')->whereDate('issued_date', '<=', $oneYearAgo)
            ->where('type', 1)
            ->get();

        foreach ($volanteRentals as $rental) {
            $this->notifyRenewal($rental->permits, 'Volante Rental');
        }
    }

    private function notifyRenewal($rental, $type)
    {
        $rental->update(['renewal_notified_at' => now()]);

        $message = "Your {$type} (ID: {$rental->id}) issued on {$rental->issued_date} is due for renewal.";

        if ($rental->mobile) {
            $this->sendSms($rental->mobile, $message);
        }

        Log::info("{$type} ID {$rental->id} notified via SMS.");
        $this->info("{$type} #{$rental->id} SMS sent to {$rental->mobile}");
    }

    private function sendSms($phone, $message)
    {
        try {
            // ✅ Example for Semaphore SMS API
            Http::post('https://api.semaphore.co/api/v4/messages', [
                'apikey' => env('SEMAPHORE_API_KEY'),
                'number' => $phone,
                'message' => $message,
                'sendername' => env('SEMAPHORE_SENDER_NAME', 'LGU'),
            ]);

            Log::info("SMS sent to {$phone}: {$message}");
        } catch (\Exception $e) {
            Log::error("❌ Failed to send SMS to {$phone}: " . $e->getMessage());
        }
    }
}
