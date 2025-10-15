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
        
        // Investment profit distribution (Monday to Friday)
  $schedule->command('investment:distribute-profit')
            ->hourly()                    // ✅ Check every hour for 24h intervals
            ->weekdays()                  // ✅ Only Monday-Friday
            ->withoutOverlapping()        // ✅ Prevent simultaneous runs
            ->onOneServer()               // ✅ Run on one server (if clustered)
            ->runInBackground();          // ✅ Don't block other tasks
        
        // php artisan investment:distribute-profit --force  local testing profit give 
       

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
