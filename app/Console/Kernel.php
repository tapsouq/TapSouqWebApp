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
        Commands\SetCreditLog::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        
        /** 
         * To run curl to get the information of the new added application, that        
         * is been added in last day
         */
        $schedule->command('getAppsInfo')
                 ->everyMinute()
                 ->withoutOverlapping();                 
        
        /**
         * To match between keywords that user entered in the last days with all application, 
         * the same for new application to be matched with all keywords
         */
        $schedule->command('matchKeywords')
                 ->everyMinute()
                 ->withoutOverlapping();                 

        /**
         * To get the placement, creative logs from the sdk actions and requests
         */
        $schedule->command('setLog')
                  ->everyMinute()
                  ->withoutOverlapping();

        /**
         * To set credit every day for every user.
         */
        $schedule->command('setCreditLog')
                  ->dailyAt('15:59:59');                 
    }
}
