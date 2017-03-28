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

        // To set user credits.
        $this->_setUsersCredits();

        // To reset users free impression
        $this->_resetUsersTodayImpressions();
    }

    /**
     * _resetImpInTodayRecords. To reset the imp_in_today columns in campaigns table back to 0.
     *
     * @param  void
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _resetImpInTodayRecords()
    {
        \DB::table('campaigns')
            ->update(['imp_in_today' => 0]);
    }

    /**
     * _resetUsersTodayImpressions. To reset the today_imps columns in users table back to 0.
     *
     * @param  void
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _resetUsersTodayImpressions()
    {
        \DB::table('users')
            ->update([
                    'today_imps' => 0
                ]);
    }

    /**
     * _setUsersCredits. To set user credits.
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _setUsersCredits()
    {
        $insertRows = \App\User::select('id', 'credit')
                        ->get()
                        ->map(function($row){
                            return [
                                    'user_id'    => $row->id,
                                    'credit'     => $row->credit,
                                    'date'       => date("Y-m-d H:i:s"),
                                    'created_at' => date("Y-m-d H:i:s"),
                                    'updated_at' => date("Y-m-d H:i:s")
                                ];
                         })->toArray();

        \DB::table('daily_log')
            ->insert($insertRows);
    }

}
