<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        try {

            $output = shell_exec('ps -eo pid,lstart,cmd | grep queue:work');
            if (!isset($output) || !str_contains($output, "queue:work --tries=3 --queue=store_device_data,default")) {
                $schedule->command('queue:work --tries=3 --queue=store_device_data,default');
            }
        }catch (\Exception $exception){

        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
