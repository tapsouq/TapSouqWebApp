<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB, Storage, Auth, Mail;
class TestCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( )
    {

        $insertArr = [];
        set_time_limit(500);
        $result = unserialize(Storage::get("deviceCategoriesLogFile"));
        
        foreach ($result as $categoryId => $devicesStr) {
            $devicesArr = explode(",", $devicesStr);
            $log = collect($devicesArr)->map(function($item) use ($categoryId){
                        
                        return [
                                "cat_id"        => $categoryId,
                                "device_id"     => $item,
                                "created_at"    => date("Y-m-d H:i:s"),
                                "updated_at"    => date("Y-m-d H:i:s")
                            ];
            })->toArray();
            $insertArr = array_merge($insertArr, $log);    
        }
        return DB::table('category_devices')
                    ->insert($insertArr);

        error_reporting(E_ALL);
       $logThirtyMinutesAgo = $this->_getLogThirtyMinutesAgo();

       if( count( $logThirtyMinutesAgo ) ){
           
           $this->_divideLogToPlacementAndCreativeLogs( $logThirtyMinutesAgo );

       }else{
        echo "no sdkactions";
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
                        `sdkactions_tmp`.`created_at` <  ( UNIX_TIMESTAMP() - 30 )
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
        $appsCategoriesLog = [];
        $campsCategoriesLog = [];
        
        // get the admin user id
        $this->appsCategories   = $this->_getCategoriesForAppOrCamp('ad_placement', 'applications', 'app_id');
        $this->campsCategories  = $this->_getCategoriesForAppOrCamp('ad_creative', 'campaigns', 'camp_id');
        $deviceCategoriesLog    = $this->_getLastDevicesCatsLogFromFile();

        if( count( $allLogs ) ){
            foreach ($allLogs as $key => $row) {

                //$this->_setCategoryLog($row, $appsCategoriesLog, $campsCategoriesLog);

                $this->_setDeviceCatLog($row, $deviceCategoriesLog);
            }
        }
        
        $this->_saveDevicesCatsLog($deviceCategoriesLog);

        //DB::table('appcats_log')->insert(array_values($appsCategoriesLog));
        //DB::table('campcats_log')->insert(array_values($campsCategoriesLog));
    }
}