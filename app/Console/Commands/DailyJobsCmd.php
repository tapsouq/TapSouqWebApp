<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DailyJobsCmd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execDailyJobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To excute some of the daily jobs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // To reset the imp_in_today columns in campaigns table back to 0.
        $this->_resetImpInTodayRecords();
    }

    /**
     * _resetImpInTodayRecords. To reset the imp_in_today columns in campaigns table back to 0.
     *
     * @param  void
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _resetImpInTodayRecords()
    {
        \DB::table('campaigns')
            ->update(['imp_in_today' => 0]);
    }
}
