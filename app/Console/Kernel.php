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
        Commands\GetApplicationInfo::class,
        Commands\KeywordMatcher::class,
        Commands\SetLog::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
                 ->hourly();
        
        /** 
         * To run curl to get the information of the new added application, that        
         * is been added in last day
         */
        $schedule->command('application:getifo')
                 ->dailyAt('00:10');                 
        
        /**
         * To match between keywords that user entered in the last days with all application, 
         * the same for new application to be matched with all keywords
         */
        $schedule->command('matchKewords')
                 ->dailyAt('02:00');

        /**
         * To get the placement, creative logs from the sdk actions and requests
         */
         $schedule->command('setlog')
                  ->dailyAt('7:52');                 
    }
}
