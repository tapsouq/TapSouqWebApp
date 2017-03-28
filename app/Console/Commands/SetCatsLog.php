<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon, DB;

class SetCatsLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setCatsLog';

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
        $appCategoriesLog = [];
        $campCategoriesLog = [];

        // get the admin user id
        $this->appsCategories   = $this->_getCategoriesForAppOrCamp('ad_placement', 'applications', 'app_id');
        $this->campsCategories  = $this->_getCategoriesForAppOrCamp('ad_creative', 'campaigns', 'camp_id');

        if( count( $allLogs ) ){
            foreach ($allLogs as $key => $row) {

                $this->_setAppCategoryLog($row, $appsCategoriesLog);
                // $this->_setCategoryLog($row, $campCategories, $campCategoriesLog);
            }
        }

        echo json_encode($appsCategoriesLog);
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
     * _setAppCategoryLog. To set category log.
     *
     * @param  object $row
     * @param  array $appsCategoriesLog
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _setAppCategoryLog($row, &$appsCategoriesLog)
    {
        if( ! isset( $this->appsCategories[$row->placement_id] ) ){
            return;
        }

        if( $row->action == REQUEST_ACTION ){
            
            $this->_setAppCategoryRequestsLog($appsCategoriesLog, $row->placement_id);
        }else{
            $relevantCats = $this->_getRelevantCats($row);
            if( count($relevantCats) ){
                $this->_setAppCategorySdkActionsLog($relevantCats);
            }

        }
    }

    /**
     * _setAppCategoryRequestsLog. To set category log metrics like requests, impressions ..etc.
     *
     * @param  array $appsCategoriesLog
     * @param  int $placementId
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _setAppCategoryRequestsLog(&$appsCategoriesLog, $placementId)
    {
        $appCategories  = $this->appsCategories[$placementId];
        $appFirstCat    = $appCategories["cats"][0];
        $appSecondCat   = $appCategories["cats"][1];

        if( isset($appsCategoriesLog[$appFirstCat]) ){
            $appsCategoriesLog[$appFirstCat]["requests"] ++; 
        }else{
            $appsCategoriesLog[$appFirstCat]["requests"] = 0;
        }

        if( isset($appsCategoriesLog[$appSecondCat]) ){
            $appsCategoriesLog[$appSecondCat]["requests"] ++;
        }else{
            $appsCategoriesLog[$appSecondCat]["requests"] = 0;
        }        
    }

    /**
     * _getRelevantCats. To get the relevant categories between the application and the campaign in this sdk action.
     *
     * @param  object $row
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _getRelevantCats($row)
    {
        $relevantCats = [];
        
        $campCategories = $this->campsCategories[$row->creative_id];
        $appCategories  = $this->appsCategories[$row->placement_id];
        

        $appFirstCat  = isset($appCategories["cats"][0]) ? $appCategories["cats"][0] : 0;
        $appSecondCat = isset($appCategories["cats"][1]) ? $appCategories["cats"][1] : 0;
        
        // Campaign support all categories 
        if( ! isset($campCategories["cats"]) ){
            $relevantCats = [
                    $appCategories["cats"][0], 0
                ];
        }else{
            if( isset($campCategories["cats"][0]) ){
                $campFirstCat = $campCategories["cats"][0];
                in_array($campFirstCat, $appCategories["cats"]) ? array_push($relevantCats, $campFirstCat) : 0;
            }

            if( isset($campCategories["cats"][1]) ){
                $campSecondCat = $campCategories["cats"][1];
                in_array($campSecondCat, $appCategories["cats"]) ? array_push($relevantCats, $campSecondCat) : 0;
            }

            // If no relevant in first and second category, check the similar categories.
            if( ! count($relevantCats) ){
                $appSimiCats  = isset($appCategories["simi_cats"])  ? $appCategories["simi_cats"]  : [];
                $campSimiCats = isset($campCategories["simi_cats"]) ? $campCategories["simi_cats"] : [];

                in_array($appFirstCat,  $campSimiCats) ? array_push($relevantCats, $appFirstCat)   : 0;
                in_array($appSecondCat, $campSimiCats) ? array_push($relevantCats, $appSecondCat)  : 0;
                in_array($campFirstCat, $appSimiCats)  ? array_push($relevantCats, $campFirstCat)  : 0;
                in_array($campSecondCat, $appSimiCats) ? array_push($relevantCats, $campSecondCat) : 0;
            }
        }
    }

    /**
     * _setAppCategorySdkActionsLog. To set the aoolication category log for sdkactions like impressions, clicks and installed.
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _setAppCategorySdkActionsLog(&$appsCategoriesLog, $relevantCats, $row)
    {
        $impressions = $row->action > REQUEST_ACTION ? 1 : 0;
        $clicks      = $row->action > SHOW_ACTION    ? 1 : 0;
        $installed   = $row->action = INSTALL_ACTION   ? 1 : 0;

        foreach ($relevantCats as $relevantCat) {
            if( isset( $appsCategoriesLog[$relevantCat] ) ){
                $appsCategoriesLog[$relevantCat]["impressions"] += $impressions;
                $appsCategoriesLog[$relevantCat]["clicks"]      += $clicks;
                $appsCategoriesLog[$relevantCat]["installed"]   += $installed;
            }else{
                $appsCategoriesLog[$relevantCat]["impressions"] = $impressions;
                $appsCategoriesLog[$relevantCat]["clicks"]      = $clicks;
                $appsCategoriesLog[$relevantCat]["installed"]   = $installed;
            }
        }        
    }
}
