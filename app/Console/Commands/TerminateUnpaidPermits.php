<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StallRental;
use App\Models\Volantes;
use App\Models\TableRental;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\TwilioService;

class TerminateUnpaidPermits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:terminate-unpaid-permits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Terminate permits with unpaid rent (4 quarters for stalls, 4 months for volante)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->terminateStallRentals();
        $this->terminateTableRentals();
        $this->terminateVolanteRentals();

        $this->info('Permit termination process completed successfully.');
    }

     /**
     * Terminate Stall rentals with 1 year no payment
     */
    private function terminateStallRentals()
    {
        $yearAgo = Carbon::now()->subYear();

        $stallRentals = StallRental::with(['permits', 'permits.payments', 'user'])
            ->get()
            ->filter(function ($rental) use ($yearAgo) {
                $lastPayment = optional($rental->permits->payments->sortByDesc('created_at')->first())->created_at;
                return !$lastPayment || Carbon::parse($lastPayment)->lt($yearAgo);
            });

        foreach ($stallRentals as $rental) {
            $permit = $rental->permits;
            $vendor = $rental->user;

            if ($permit && $permit->status !== 'terminated') {
                $permit->update(['status' => 'terminated']);
                Log::info("Terminated StallRental permit #{$permit->id} due to 1 year of non-payment.");

                if ($vendor && $vendor->mobile) {
                    $msg = "NOTICE: Your Stall Rental permit ({$permit->permit_number}) has been TERMINATED due to 1 year of unpaid rent. Please contact the office for reactivation options.";
                    $this->sendSmsSafe($twilio, $vendor->mobile, $msg);
                }
            }
        }
    }

    /**
     * Terminate Table rentals with 1 year no payment
     */
    private function terminateTableRentals()
    {
        $yearAgo = Carbon::now()->subYear();

        $tableRentals = TableRental::with(['permits', 'permits.payments'])
            ->get()
            ->filter(function ($rental) use ($yearAgo) {
                $lastPayment = optional($rental->permits->payments->sortByDesc('created_at')->first())->created_at;
                return !$lastPayment || Carbon::parse($lastPayment)->lt($yearAgo);
            });

        foreach ($tableRentals as $rental) {
            $permit = $rental->permits;
            $vendor = $rental->user;

            if ($permit && $permit->status !== 'terminated') {
                $permit->update(['status' => 'terminated']);
                Log::info("Terminated TableRental permit #{$permit->id} due to 1 year of non-payment.");

                if ($vendor && $vendor->mobile) {
                    $msg = "NOTICE: Your Table Rental permit ({$permit->permit_number}) has been TERMINATED due to 1 year of unpaid rent. Please settle your account or contact the office.";
                    $this->sendSmsSafe($twilio, $vendor->mobile, $msg);
                }
            }
        }
    }

    /**
     * Terminate Volante rentals with 4 months no payment this year
     */
    private function terminateVolanteRentals()
    {
        $fourMonthsAgo = Carbon::now()->subMonths(4);
        $thisYear = Carbon::now()->year;

        $volanteRentals = Volantes::with(['permit', 'permit.payments'])
            ->get()
            ->filter(function ($rental) use ($fourMonthsAgo, $thisYear) {
                $payments = $rental->permits->payments ?? collect();

                $recentPayments = $payments->filter(function ($payment) use ($thisYear) {
                    return Carbon::parse($payment->created_at)->year == $thisYear;
                });

                $lastPayment = $recentPayments->sortByDesc('created_at')->first();

                return !$lastPayment || Carbon::parse($lastPayment->created_at)->lt($fourMonthsAgo);
            });

        foreach ($volanteRentals as $rental) {
            $permit = $rental->permits;
            $vendor = $rental->user;

            if ($permit && $permit->status !== 'terminated') {
                $permit->update(['status' => 'terminated']);
                Log::info("Terminated VolanteRental permit #{$permit->id} due to 4 months of non-payment.");

                if ($vendor && $vendor->mobile) {
                    $msg = "NOTICE: Your Volante Rental permit ({$permit->permit_number}) has been TERMINATED due to 4 months of unpaid rent. Please contact the office immediately.";
                    $this->sendSmsSafe($twilio, $vendor->mobile, $msg);
                }
            }
        }
    }
}
