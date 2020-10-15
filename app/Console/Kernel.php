<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('cron:processEmailOnQueue')->everyMinute();
        $schedule->command('cron:processEmailTickets')->everyMinute();
        $schedule->command('cron:checkIncomingLeads')->everyThirtyMinutes();
        $schedule->command('cron:createPaymentCSV')->daily();
        //$schedule->command('cron:createRefundPaymentCSV')->dailyAt('02:00');
        $schedule->command('cron:checkInvoiceForProcessing')->dailyAt('01:00');
        $schedule->command('cron:generateRecurringInvoice')->cron('0 */12 * * *');
        $schedule->command('cron:downloadReturnFile')->dailyAt('00:30');
        $schedule->command('cron:readReturnFile')->dailyAt('00:45');
        $schedule->command('cron:downloadConfFile')->dailyAt('05:30');
        $schedule->command('cron:readConfFile')->dailyAt('03:00');
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
