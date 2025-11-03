<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Kernel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:kernel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('app:terminate-unpaid-permits')->dailyAt('08:00');
        $schedule->command('app:notify-payment')->dailyAt('08:00');
        $schedule->command('rental:check-renewals')->everyTwoMinutes();
    }


}
