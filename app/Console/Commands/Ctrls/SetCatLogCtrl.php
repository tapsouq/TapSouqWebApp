<?php

namespace App\Console\Commands\Ctrls;

use DB;

class SetCatLogCtrl
{

    private $appsCategories;
    private $campsCategories;

    /**
     * __construct. To init the class.
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function __construct($appsCategories, $campsCategories)
    {
        $this->appsCategories  = $appsCategories;
        $this->campsCategories = $campsCategories;
    }

    /**
     * _setCategoryLog. To set category log.
     *
     * @param  object $row
     * @param  array $appsCategoriesLog
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function setCategoryLog($row, &$appsCategoriesLog, &$campsCategoriesLog)
    {
        if( ! isset( $this->appsCategories[$row->placement_id] ) ){
            return;
        }

        if( $row->action == REQUEST_ACTION ){
            
            $this->_setAppCategoryRequestsLog($appsCategoriesLog, $row);
        }else{
            $relevantCats = $this->_getRelevantCats($row);

            if( count($relevantCats["appCats"]) ){
                $this->_setAppCategorySdkActionsLog($appsCategoriesLog, $relevantCats["appCats"], $row);
            }

            if( count( $relevantCats["campCats"] ) ){
                $this->_setCampCategorySdkActionsLog($campsCategoriesLog, $relevantCats["campCats"], $row);
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
    private function _setAppCategoryRequestsLog(&$appsCategoriesLog, $row)
    {
        $appCategories  = $this->appsCategories[$row->placement_id];
        $appFirstCat    = $appCategories["cats"][0];
        $appSecondCat   = $appCategories["cats"][1];

        if( isset($appsCategoriesLog[$appFirstCat]) ){
            $appsCategoriesLog[$appFirstCat]["requests"] ++; 
        }else{
            $appsCategoriesLog[$appFirstCat]["cat_id"] = $appFirstCat;
            $appsCategoriesLog[$appFirstCat]["requests"] = 1;
            $appsCategoriesLog[$appFirstCat]["impressions"] = 0;
            $appsCategoriesLog[$appFirstCat]["clicks"] = 0;
            $appsCategoriesLog[$appFirstCat]["installed"] = 0;
            $appsCategoriesLog[$appFirstCat]["created_at"] = $row->updated_at;
            $appsCategoriesLog[$appFirstCat]["updated_at"] = $row->updated_at;
        }

        if( isset($appsCategoriesLog[$appSecondCat]) ){
            $appsCategoriesLog[$appSecondCat]["requests"] ++;
        }else{
            $appsCategoriesLog[$appSecondCat]["cat_id"] = $appSecondCat;
            $appsCategoriesLog[$appSecondCat]["requests"] = 1;
            $appsCategoriesLog[$appSecondCat]["impressions"] = 0;
            $appsCategoriesLog[$appSecondCat]["clicks"] = 0;
            $appsCategoriesLog[$appSecondCat]["installed"] = 0;
            $appsCategoriesLog[$appSecondCat]["created_at"] = $row->updated_at;
            $appsCategoriesLog[$appSecondCat]["updated_at"] = $row->updated_at;
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
    private function _getRelevantCats($row)
    {
        $relevantCats = [ "appCats" => [], "campCats" => [] ];
        
        if( $row->creative_id == 0 ){
            return $relevantCats;
        }

        // Get the categories for this application and this campaign those have ad_placement and ad_creative.
        $campCategories = $this->campsCategories[$row->creative_id];
        $appCategories  = $this->appsCategories[$row->placement_id];
        
        // Get the first and the second application category
        $appFirstCat  = isset($appCategories["cats"][0]) ? $appCategories["cats"][0] : 0;
        $appSecondCat = isset($appCategories["cats"][1]) ? $appCategories["cats"][1] : 0;
        
        // Campaign support all categories 
        if( ! isset($campCategories["cats"]) ){

            $relevantCats["appCats"][] = $appFirstCat;
            $relevantCats["campCats"][] = 0;
        }else{
            // If there is first category for the campaign
            if( isset($campCategories["cats"][0]) ){
                $campFirstCat = $campCategories["cats"][0];
                // Check if the first camp category match any application categories
                if(in_array($campFirstCat, $appCategories["cats"]) ){
                    array_push($relevantCats["appCats"], $campFirstCat);
                    array_push($relevantCats["campCats"], $campFirstCat);
                }
            }

            // The same in second category
            if( isset($campCategories["cats"][1]) ){
                $campSecondCat = $campCategories["cats"][1];
                // Check if the second camp category match any application categories
                if(in_array($campSecondCat, $appCategories["cats"]) ){
                    array_push($relevantCats["appCats"], $campSecondCat);
                    array_push($relevantCats["campCats"], $campSecondCat);
                }
            }

            /** Comment it until test it and assure that it's working good
            // If no relevant in first and second category, check the similar categories.
            if( ! count($relevantCats["appCats"]) ){
                $appSimiCats  = isset($appCategories["simi_cats"])  ? $appCategories["simi_cats"]  : [];
                $campSimiCats = isset($campCategories["simi_cats"]) ? $campCategories["simi_cats"] : [];

                //Check if application first category in campaign similar categories  
                if( in_array($appFirstCat,  $campSimiCats) ){
                    array_push($relevantCats["appCats"], $appFirstCat);
                    array_push($relevantCats["campCats"], $appFirstCat);
                }

                //Check if application second category in campaign similar categories  
                if( in_array($appSecondCat,  $campSimiCats) ){
                    array_push($relevantCats["appCats"], $appSecondCat);
                    array_push($relevantCats["campCats"], $appSecondCat);
                }

                //Check if campaign first category in application similar categories  
                if( in_array($campFirstCat,  $appSimiCats) ){
                    array_push($relevantCats["appCats"], $campFirstCat);
                    array_push($relevantCats["campCats"], $campFirstCat);
                }

                //Check if campaign second category in application similar categories  
                if( in_array($campSecondCat,  $appSimiCats) ){
                    array_push($relevantCats["appCats"], $campSecondCat);
                    array_push($relevantCats["campCats"], $campSecondCat);
                }

            }
            */
        }

        return $relevantCats;
    }

    /**
     * _setAppCategorySdkActionsLog. To set the application category log for sdkactions like impressions, clicks and installed.
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _setAppCategorySdkActionsLog(&$appsCategoriesLog, $relevantCats, $row)
    {
        $requests    = $row->action >= REQUEST_ACTION ? 1 : 0;
        $impressions = $row->action > REQUEST_ACTION ? 1 : 0;
        $clicks      = $row->action > SHOW_ACTION    ? 1 : 0;
        $installed   = $row->action == INSTALL_ACTION   ? 1 : 0;

        foreach ($relevantCats as $relevantCat) {
            if( isset( $appsCategoriesLog[$relevantCat] ) ){
                $appsCategoriesLog[$relevantCat]["requests"]    += $requests;
                $appsCategoriesLog[$relevantCat]["impressions"] += $impressions;
                $appsCategoriesLog[$relevantCat]["clicks"]      += $clicks;
                $appsCategoriesLog[$relevantCat]["installed"]   += $installed;
            }else{
                $appsCategoriesLog[$relevantCat]["cat_id"]      = $relevantCat;
                $appsCategoriesLog[$relevantCat]["requests"]    = $requests;
                $appsCategoriesLog[$relevantCat]["impressions"] = $impressions;
                $appsCategoriesLog[$relevantCat]["clicks"]      = $clicks;
                $appsCategoriesLog[$relevantCat]["installed"]   = $installed;
                $appsCategoriesLog[$relevantCat]["created_at"]  = $row->updated_at;
                $appsCategoriesLog[$relevantCat]["updated_at"]  = $row->updated_at;
            }
        }        
    }

    /**
     * _setCampCategorySdkActionsLog. To set the campaign category log for sdkactions like impressions, clicks and installed.
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _setCampCategorySdkActionsLog(&$campsCategoriesLog, $relevantCats, $row)
    {
        $impressions = $row->action > REQUEST_ACTION ? 1 : 0;
        $clicks      = $row->action > SHOW_ACTION    ? 1 : 0;
        $installed   = $row->action == INSTALL_ACTION   ? 1 : 0;

        foreach ($relevantCats as $relevantCat) {
            if( isset( $campsCategoriesLog[$relevantCat] ) ){
                $campsCategoriesLog[$relevantCat]["impressions"] += $impressions;
                $campsCategoriesLog[$relevantCat]["clicks"]      += $clicks;
                $campsCategoriesLog[$relevantCat]["installed"]   += $installed;
            }else{
                $campsCategoriesLog[$relevantCat]["cat_id"]      = $relevantCat;
                $campsCategoriesLog[$relevantCat]["impressions"] = $impressions;
                $campsCategoriesLog[$relevantCat]["clicks"]      = $clicks;
                $campsCategoriesLog[$relevantCat]["installed"]   = $installed;
                $campsCategoriesLog[$relevantCat]["created_at"]  = $row->updated_at;
                $campsCategoriesLog[$relevantCat]["updated_at"]  = $row->updated_at;
            }
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
}
