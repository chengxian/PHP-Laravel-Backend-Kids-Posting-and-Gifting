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
        Commands\Inspire::class,
        Commands\VaultTransitTest::class,
        Commands\TestGifts::class,
        Commands\TestGiftsSend::class,
        Commands\GenerateBetaCode::class,
        Commands\GenerateInviteCode::class,
        Commands\CheckRecurringContributions::class
    ];

    /**
     * Define the apeplication's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('usalliance:checkloansstatus --recent=yes')->everyFiveMinutes();
        $schedule->command('usalliance:checkloansstatus --recent=no')->hourly();
        $schedule->command('kidgifting:checkrecurringcontributions')->dailyAt('01:00');
    }
}
