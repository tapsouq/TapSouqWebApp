<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon, DB;

class SetAdsLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setAdsLog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To derive the values from sdk_actions table into log tables';

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

        $logFiveMinutesAgo = $this->_getLogFiveMinutesAgo();

        if( count( $logFiveMinutesAgo ) ){
            
            $this->_divideLogToPlacementAndCreativeLogs( $logFiveMinutesAgo );

        }
        
    }

    /**
     * _getLogFiveMinutesAgo. To get the log the range from 7 minutes ago to 2 minutes ago.
     *
     * @param  param
     * @return array
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _getLogFiveMinutesAgo()
    {
        return DB::select("
                SELECT * 
                    FROM `sdkactions_tmp`
                    WHERE 
                        `sdkactions_tmp`.`created_at` <  TIME( SUBTIME( NOW(), '00:30:00' ) )
            ");
    }

    /**
     * _divideLogToPlacementAndCreativeLogs. To divide the gotten log to placement log array and creative log array.
     *
     * @param  array $allLogs
     * @return array
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _divideLogToPlacementAndCreativeLogs($allLogs)
    {
        $placementLog = [];
        $creativeLog  = [];
        $sdkActions   = [];
        $ids          = [];

        if( count( $allLogs ) ){
            foreach ($allLogs as $key => $row) {
                $ids[] = $this->_setSdkActions($row, $sdkActions);
                $this->_setAdLog($row, 'placement_id', $placementLog);
                $this->_setAdLog($row, 'creative_id', $creativeLog);
            }
        }
        \DB::table('placement_log')->insert( $placementLog );
        \DB::table('creative_log')->insert( $creativeLog );
        \DB::table('sdkactions')->insert( $sdkActions );
        \DB::table('sdkactions_tmp')->whereIn('id', $ids)->delete();
    }

    /**
     * _setAdLog. To get the ad log, placement or creative.
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _setAdLog($row, $adsType, &$adLog)
    {
        $time        = time() - ( 30 * 60 ); 
        $adsId       = $row->{$adsType};
        $action      = $row->action;
        $tier        = $row->country_tier;
        $impressions = ($action >= SHOW_ACTION) ? 1 : 0; 
        $clicks      = ($action >= CLICK_ACTION) ? 1 : 0; 
        $installed   = ($action == INSTALL_ACTION) ? 1 : 0;
        $credits     = ($action >= CLICK_ACTION) ? $tier : 0;

        if( isset($adLog[ $adsId ]) ){

            $adLog[ $adsId ]['requests']++;
            $adLog[ $adsId ]['impressions']  += $impressions;
            $adLog[ $adsId ]['clicks']       += $clicks;
            $adLog[ $adsId ]['installed']    += $installed;
            $adLog[ $adsId ]['credits']      += $credits;
        
        }else if( $adsId ){
            
            $adLog[ $adsId ] = [
                    'ads_id'        => $adsId,
                    'requests'      => 1,
                    'impressions'   => $impressions,
                    'clicks'        => $clicks,
                    'installed'     => $installed,
                    'credits'       => $credits,        
                    'created_at'    => date('Y-m-d H:i:s', $time),
                    'updated_at'    => date('Y-m-d H:i:s', $time)
                ];
        }

    }

    /**
     * _setSdkActions. To insert sdk_actions row.
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _setSdkActions($row, &$sdkActions)
    {
        $sdkActions[] = [
                'id'            => $row->id,
                'creative_id'   => $row->creative_id,
                'placement_id'  => $row->placement_id,
                'device_id'     => $row->device_id,
                'action'        => $row->action,
                'app_id'        => $row->app_id,
                'camp_id'       => $row->camp_id,
                'country_id'    => $row->country_id,
                'country_tier'  => $row->country_tier,
                'app_user'      => $row->app_user,
                'camp_user'     => $row->camp_user,
                'created_at'    => date('Y-m-d') . ' ' . $row->created_at,
                'updated_at'    => date('Y-m-d') . ' ' . $row->updated_at,
            ];
        return $row->id;
    }
}
