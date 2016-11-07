<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon, DB;

class SetLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setlog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To derive the values from sdk_actions table into daily_log table';

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
        // All ads actions
        $actions    = [ REQUEST_ACTION, SHOW_ACTION, CLICK_ACTION, INSTALL_ACTION ];
        
        // Ads types
        $adsTypes   = [ 'placement_id' =>'placement_log' , 'creative_id' => 'creative_log' ];
        foreach ($adsTypes as $adsType => $logTable) {
            foreach ($actions as $key => $action) {
                // get action count for specific day
                $result= DB::select("
                            SELECT COUNT( * ) as `actions` , {$adsType} 
                                FROM `sdk_actions`
                                    INNER JOIN `sdk_requests` ON `sdk_requests`.`id` = `sdk_actions`.`request_id`  
                                    WHERE 
                                        `action` = $action
                                    AND
                                        DATE( `sdk_actions`.`created_at` ) = DATE_SUB( CURRENT_DATE(), INTERVAL 1 DAY )
                                GROUP BY {$adsType}
                        ");
                // To create an array of ids as key andcounts as it's value. 
                $actionsResult[$action] = array_pluck( $result, 'actions', $adsType );
            }

            if( count( $actionsResult[REQUEST_ACTION] ) > 0 ){
                $insertArray = [];
                foreach ($actionsResult[ REQUEST_ACTION ] as $adsId => $requests) {
                    
                    if( $adsId ){

                        // Adapting values to be inserted into DB 
                        $impressions    = isset($actionsResult[SHOW_ACTION][$adsId]) ? $actionsResult[SHOW_ACTION][$adsId] : 0;
                        $clicks         = isset($actionsResult[CLICK_ACTION][$adsId]) ? $actionsResult[CLICK_ACTION][$adsId] : 0;
                        $installed      = isset($actionsResult[INSTALL_ACTION][$adsId]) ? $actionsResult[INSTALL_ACTION][$adsId] : 0;
                        $time           = time() - ( 24 * 60 * 60 * 1);
                       
                        $insertArray[] = [
                                'ads_id'        => $adsId,
                                'requests'      => $requests,
                                'impressions'   => $impressions ,
                                'clicks'        => $clicks,
                                'installed'     => $installed,
                                'created_at'    => date('Y-m-d H:i:s', $time),
                                'updated_at'    => date('Y-m-d H:i:s', $time)
                            ];
                    }
                }
                // Insert data into DB
                DB::table($logTable)->insert( $insertArray );   
            }
        }
    }

}
