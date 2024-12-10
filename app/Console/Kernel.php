<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('distribution')->hourly(); //->everyFiveMinutes();
        $schedule->command('delete:untranslated-words')->hourly();
        $schedule->command('logs:save-copy')->dailyAt('11:00');
        $schedule->command('logs:clear-main-by-size', [config('logging.log_file.delete_size')])
            ->dailyAt('23:00');
        $schedule->command('logs:delete-copies')->weeklyOn(1, '01:00');
        $schedule->command('delete:db-batches')->weeklyOn(1, '02:00');
        $schedule->command('delete:db-failed-jobs')->weeklyOn(1, '02:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
