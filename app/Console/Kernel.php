<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // BEP20 USDT Deposit Processing - Every 5 minutes
        $schedule->command('deposits:process-bep20')->everyFiveMinutes();
        
        // Investment profit distribution (Monday to Friday at 9:00 AM)
        $schedule->command('investment:distribute-profit')->dailyAt('09:00')->weekdays();
        
        // BEP20 deposit monitoring - Every hour
        $schedule->command('monitor:bep20-deposits --notify-stuck')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
