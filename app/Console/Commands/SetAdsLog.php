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

        $logThirtyMinutesAgo = $this->_getLogThirtyMinutesAgo();

        if( count( $logThirtyMinutesAgo ) ){
            
            $this->_divideLogToPlacementAndCreativeLogs( $logThirtyMinutesAgo );

        }
        
    }

    /**
     * _getLogThirtyMinutesAgo. To get the log since thirty minutes ago.
     *
     * @param  param
     * @return array
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _getLogThirtyMinutesAgo()
    {
        return DB::select("
                SELECT `sdkactions_tmp`.*, `countries`.`tier`
                    FROM `sdkactions_tmp`
                    LEFT JOIN `countries` ON `countries`.`id` = `sdkactions_tmp`.`country_id`
                    WHERE 
                        `sdkactions_tmp`.`created_at` <  ( UNIX_TIMESTAMP() - 60 * 30 )
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
        $creditsData = [];
        $ids          = [];

        // get the admin user id
        $adminUserId      = $this->_getAdminUserId();

        if( count( $allLogs ) ){
            foreach ($allLogs as $key => $row) {
                $ids[] = $this->_setSdkActions($row, $sdkActions);
                $this->_setAdLog($row, 'placement_id', $placementLog);
                $this->_setAdLog($row, 'creative_id', $creativeLog);

                $this->_setCreditsLog($row, $creditsData, $adminUserId);
            }

            $this->_insertCreditData($creditsData);
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
    private function _setAdLog($row, $adsType, &$adLog)
    {
        $time        = time() - ( 30 * 60 ); 
        $adsId       = $row->{$adsType};
        $action      = $row->action;
        $tier        = $row->tier;
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
                    'created_at'    => $row->updated_at,
                    'updated_at'    => $row->updated_at
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
    private function _setSdkActions($row, &$sdkActions)
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
                'country_tier'  => $row->tier,
                'app_user'      => $row->app_user,
                'camp_user'     => $row->camp_user,
                'created_at'    => $row->updated_at,
                'updated_at'    => $row->updated_at,
            ];
        return $row->id;
    }


    /**
     * _getAdminUserId. To get the admin users ids.
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _getAdminUserId()
    {
        return \App\User::select('id')
                    ->where('role', ADMIN_PRIV)
                    ->first()
                    ->id;    
    }


    /**
     * _setCreditsLog. To set the credit log array.
     *
     * @param  object $row
     * @param  array $creditsData
     * @param  int $adminUserId
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _setCreditsLog($row, &$creditsData, $adminUserId)
    {   
        $appUser  = $row->app_user;
        $campUser = $row->camp_user;
        $credit   = $row->tier;
        $adminCredit = round($credit * 0.1, 2);
        
        if( $row->action < CLICK_ACTION || ! $campUser || ! $appUser ){
            return $row->id;
        }

        if( $campUser == $adminUserId ){
            
            if( isset( $creditsData[$campUser] ) ){
                $creditsData[$campUser]["spent"] += $adminCredit;
            }else{
                $creditsData[$campUser]["spent"] = $adminCredit;
            }
        }else{        
            if( isset($creditsData[$appUser] ) ){
                $creditsData[$appUser]["gained"] += $credit;
            }else{
                $creditsData[$appUser] = [
                        "gained"    => $credit,
                        "spent"     => 0
                    ];
            }

            if( isset($creditsData[$campUser]) ){
                $creditsData[$campUser]["spent"] += $credit;
            }else{
                $creditsData[$campUser] = [
                        "gained"    => 0,
                        "spent"     => $credit
                    ];
            }
            
            if( isset($creditsData[$adminUserId]) ){
                $creditsData[$adminUserId]["gained"] += $adminCredit;
            }else{
                $creditsData[$adminUserId]["gained"] = $adminCredit;
            }
        }
        return $row->id;
    }


    /**
     * _insertCreditData. To insert the credits into credits table.
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _insertCreditData($creditsData)
    {
        foreach ($creditsData as $userId => $data) {
            
            $gained = isset($data["gained"]) ? $data["gained"] : 0;
            $spent  = isset($data["spent"])  ? $data["spent"]  : 0;
            $netCredit =  $gained - $spent;
            
            $values = [ $netCredit, $gained, $spent, $userId ];
            
            DB::update(
                    "UPDATE `daily_log` 
                        SET `credit` = `credit` + ?, `gained_credit` = `gained_credit` + ?, `spent_credit` = `spent_credit` + ? 
                        WHERE `user_id` = ? AND date(created_at) = CURDATE()
                    ", $values);
        }
    }
}
