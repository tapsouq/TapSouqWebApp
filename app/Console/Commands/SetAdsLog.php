<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon, DB;
use App\Console\Commands\Ctrls\SetCatLogCtrl;
use Storage;

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

    private $appsCategories;
    private $campsCategories;

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
            $this->_loopOverTheRowsThirtyMinutesAgo( $logThirtyMinutesAgo );
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
                        AND
                            `sdkactions_tmp`.`created_at` >=  ( UNIX_TIMESTAMP() - 60 * 35 ) 
            ");
    }

    /**
     * _loopOverTheRowsThirtyMinutesAgo. To divide the gotten log to placement log array and creative log array.
     *
     * @param  array $allLogs
     * @return array
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _loopOverTheRowsThirtyMinutesAgo($allLogs)
    {
        $placementLog = [];
        $creativeLog  = [];
        $sdkActions   = [];
        $creditsData  = [];
        $ids          = [];
        $appsCategoriesLog  = [];
        $campsCategoriesLog = [];

        // Get the admin user id
        $adminUserId      = $this->_getAdminUserId();

        // Get the applications and campaigns categories.
        $appsCategories   = $this->_getCategoriesForAppOrCamp('ad_placement', 'applications', 'app_id');
        $campsCategories  = $this->_getCategoriesForAppOrCamp('ad_creative', 'campaigns', 'camp_id');
        
        // Get the saved devices categories from the file
        $deviceCategoriesLog    = $this->_getLastDevicesCatsLogFromFile();

        if( count( $allLogs ) ){
            foreach ($allLogs as $key => $row) {
                
                // Handle the values that will be inserted into the main sdkactions table.
                $ids[] = $this->_setSdkActions($row, $sdkActions);

                // Save the ads logs into placement_log and creative_log tables
                $this->_setAdLog($row, 'placement_id', $placementLog, 'app');
                $this->_setAdLog($row, 'creative_id', $creativeLog, 'camp');

                // Handle the gained and spent credits.
                $this->_setCreditsLog($row, $creditsData, $adminUserId);

                // Handle the # of requests, impressions ...etc for every category
                $catsLog = new SetCatLogCtrl($appsCategories, $campsCategories);
                $catsLog->setCategoryLog($row, $appsCategoriesLog, $campsCategoriesLog);

                // Handle the number of devices for every category
                $this->_setDeviceCatLog($row, $deviceCategoriesLog);
            }

            // Insert the credits data into database
            $this->_insertCreditData($creditsData);

            // save the new devices in categoris log in the file
            $this->_saveDevicesCatsLog($deviceCategoriesLog);

            // Save the results to the database
            \DB::table('placement_log')->insert( $placementLog );
            \DB::table('creative_log')->insert( $creativeLog );
            \DB::table('sdkactions')->insert( $sdkActions );
            \DB::table('sdkactions_tmp')->whereIn('id', $ids)->delete();
            \DB::table('appcats_log')->insert(array_values($appsCategoriesLog));
            \DB::table('campcats_log')->insert(array_values($campsCategoriesLog));
        }
    }

    /**
     * _setAdLog. To get the ad log, placement or creative.
     *
     * @param  object $row
     * @param  string $adsType. 'creative_id' or 'placement_id'
     * @param  array $adLog.
     * @param  strinf $paent. 'app' or 'camp'
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _setAdLog($row, $adsType, &$adLog, $parent)
    {
        $parentIdKey    = $parent . "_id";
        $parentUserKey  = $parent . "_user";

        $time           = time() - ( 30 * 60 ); 
        $adsId          = $row->{$adsType};
        $parentId       = $row->{$parentIdKey};
        $parentUserId   = $row->{$parentUserKey};
        $action         = $row->action;
        $tier           = $row->tier;
        $impressions    = ($action >= SHOW_ACTION) ? 1 : 0; 
        $clicks         = ($action >= CLICK_ACTION) ? 1 : 0; 
        $installed      = ($action == INSTALL_ACTION) ? 1 : 0;
        $credits        = ($action >= CLICK_ACTION) ? $tier : 0;

        if( isset($adLog[ $adsId ]) ){

            $adLog[ $adsId ]['requests']++;
            $adLog[ $adsId ]['impressions']  += $impressions;
            $adLog[ $adsId ]['clicks']       += $clicks;
            $adLog[ $adsId ]['installed']    += $installed;
            $adLog[ $adsId ]['credits']      += $credits;
        
        }else if( $adsId ){

            $adLog[ $adsId ] = [
                    'ads_id'            => $adsId,
                    "parent_id"         => $parentId,
                    "user_id"           => $parentUserId,
                    'requests'          => 1,
                    'impressions'       => $impressions,
                    'clicks'            => $clicks,
                    'installed'         => $installed,
                    'credits'           => $credits,        
                    'created_at'        => $row->updated_at,
                    'updated_at'        => $row->updated_at
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

    /**
     * _getCategoriesForAppOrCamp. Function desciption.
     *
     * @param  string $adsModel
     * @param  string $parentModel
     * @param  string $parentKey
     * @return array
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _getCategoriesForAppOrCamp($adsModel, $parentModel, $parentKey)
    {
        $all = \DB::table($adsModel)
                    ->leftJoin($parentModel, "{$parentModel}.id", "=", "{$adsModel}.{$parentKey}")
                    ->select("{$adsModel}.id", "fcategory", "scategory", "simi_cats")
                    ->get();

        $catsByModelId = [];
        foreach ($all as $key => $row) {
            if( $fcategory = $row->fcategory ){
                $catsByModelId[$row->id]['cats'][] = $fcategory;
            }

            if( $scategory = $row->scategory ){
                $catsByModelId[$row->id]['cats'][] = $scategory;
            }
            
            if( $simiCats = $row->simi_cats ){
                
                $catsByModelId[$row->id]['simi_cats'] = explode(',', $simiCats);
            }
        }
        return $catsByModelId;
    }

    /**
     * _setDeviceCatLog. To set the devices in categories log.
     *
     * @param  object $row
     * @param  array $deviceCategoriesLog
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _setDeviceCatLog($row, &$deviceCategoriesLog)
    {
        $placementId = $row->placement_id > 14 ? 14 : $row->placement_id;

        $appFirstCat  = $this->appsCategories[$placementId]["cats"][0];   
        $appSecondCat = $this->appsCategories[$placementId]["cats"][1];

        if( isset($deviceCategoriesLog[$appFirstCat]) ){
            $deviceCategoriesLog[$appFirstCat] .= $row->device_id . ",";
        }else{
            $deviceCategoriesLog[$appFirstCat] = $row->device_id . ",";
        }

        if( isset($deviceCategoriesLog[$appSecondCat]) ){
            $deviceCategoriesLog[$appSecondCat] .= $row->device_id . ",";
        }else{
            $deviceCategoriesLog[$appSecondCat] = $row->device_id . ",";
        }
    }

    /**
     * _saveDevicesCatsLog. To save the devices categories log into file.
     *
     * @param  array $deviceCategoriesLog
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _saveDevicesCatsLog($deviceCategoriesLog)
    {
        $finalDeviceCategoriesLog = [];
        foreach ($deviceCategoriesLog as $catId => $devicesStr) {
            $devices = array_unique( explode(",", $devicesStr) );
            $finalDeviceCategoriesLog[$catId] = implode(",", $devices);
        }

        Storage::put("deviceCategoriesLogFile", serialize($finalDeviceCategoriesLog) );
    }

    /**
     * _getLastDevicesCatsLogFromFile. To get the last devices categories log from the file.
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _getLastDevicesCatsLogFromFile()
    {
        return unserialize(Storage::get("deviceCategoriesLogFile"));    
    }
}
