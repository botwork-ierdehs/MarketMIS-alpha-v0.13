<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TwilioService;
use App\Models\User;
use App\Models\StallRental;
use App\Models\TablesRental;
use App\Models\Volantes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
class NotifyPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-payment';
    protected $description = 'Notify users if rental payment is due within 5 days (quarterly for Stall/Table, monthly for Volante)';

    public function handle()
    {
        $today = Carbon::today();
        $targetDate = $today->copy()->addDays(5);
        $twilioService = new TwilioService();

        // 🔸 Check each rental type
        $this->checkVolanteRentals($targetDate, $twilioService,  Volantes::class, 'Volante Rental');
        $this->checkQuarterlyRentals($targetDate, $twilioService, StallRental::class, 'Stall Rental');
        $this->checkQuarterlyRentals($targetDate, $twilioService, TablesRental::class, 'Table Rental');

        $this->info('Payment reminders processed successfully.');
    }

    protected function checkQuarterlyRentals(Carbon $targetDate, TwilioService $twilioService, string $modelClass, string $type)
    {
        $rentals = $modelClass::with(['permit', 'user'])->get()->filter(function ($rental) use ($targetDate) {
            if (!$rental->permits || !$rental->permits->issued_date) return false;

            $issueDate = Carbon::parse($rental->permits->issued_date);

            // Get quarterly due dates
            $dueDates = collect();
            for ($i = 0; $i < 12; $i += 3) {
                $dueDates->push($issueDate->copy()->addMonths($i));
            }

            // Move dueDates to this year
            $dueDates = $dueDates->map(fn($d) => Carbon::create(date('Y'), $d->month, $d->day));

            // If due date already passed this year, add next year
            $dueDates = $dueDates->map(fn($d) => $d->lt(Carbon::now()) ? $d->addYear() : $d);

            return $dueDates->contains(fn($d) => $d->isSameDay($targetDate));
        });

        foreach ($rentals as $rental) {
            $this->sendReminder($rental, $twilioService, $type);
        }
    }

    protected function sendReminder($rental, TwilioService $twilioService, string $type)
    {
        $user = $rental->user;
        $permit = $rental->permits;

        if (!$user || !$user->mobile || !$permit) return;

        $dueDay = Carbon::parse($permit->issued_date)->day;
        $dueDate = Carbon::now()->day($dueDay);
        $msg = "REMINDER: Your {$type} payment is due soon.\n";
        $msg .= "Permit No.: {$permit->permit_number}\n";
        $msg .= "Due Date: {$dueDate->format('F j, Y')}\n";
        $msg .= "Please settle your payment to avoid penalties.";

        try {
            $twilioService->sendSms($user->mobile, $msg);
            Log::info("{$type} reminder sent to {$user->mobile} (rental ID {$rental->id})");
        } catch (\Exception $e) {
            Log::error("Failed to send SMS to {$user->mobile}: " . $e->getMessage());
        }

    }
}
