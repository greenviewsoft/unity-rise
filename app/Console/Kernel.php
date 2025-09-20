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
        // $schedule->command('command:order')->everyMinute();
        // $schedule->command('command:withdraw')->everyMinute(); // Disabled for local development
       // $schedule->command('command:autoreceive')->everyMinute();
        // $schedule->command('command:spin')->everyMinute(); // Disabled for local development
        
        // Investment profit distribution (Monday to Friday at 9:00 AM)
        $schedule->command('investment:distribute-profit')->dailyAt('09:00')->weekdays();
        
        // $schedule->command('inspire')->hourly();
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
