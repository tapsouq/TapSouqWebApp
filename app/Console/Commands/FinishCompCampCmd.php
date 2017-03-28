<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FinishCompCampCmd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finishCompletedCampaigns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        \DB::table('campaigns')
            ->where('end_date', '<', date('Y-m-d H:i:s'))
            ->update(['status' => COMPLETED_CAMP]);

        \DB::table('users')
            ->whereRaw("consumed_imps >= free_imps")
            ->update([
                    'free_imps' => NULL
                ]);
    }
}
