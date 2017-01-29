<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class SdkActions extends Model
{
    protected $table = 'sdkactions';



    /**
     * getShownAds. To get the related shown ads according to the status of the function argument.
     * * Getting the shown creative ads or placement according to placementshow boolean argument.
     *
     * @param int $adId
     * @param \Illuminate\Http\Request $request
     * @param boolean $status
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public static function getShownAds ( $adId, $request, $placemetShow = false){
        $actions   = [ REQUEST_ACTION, SHOW_ACTION, CLICK_ACTION, INSTALL_ACTION ];
        
        $categories     = array_pluck(\App\Models\Category::all(), 'name', 'id');
        $formats        = config('consts.all_formats');

        $mainTable      = $placemetShow ? 'ad_placement' : 'ad_creative';
        $fatherTable    = $placemetShow ? 'applications' : 'campaigns';
        $otherTable     = $placemetShow ? 'ad_creative'  : 'ad_placement';
        $id             = $placemetShow ? 'placement_id' : 'creative_id';
        $otherId        = $placemetShow ? 'creative_id'  : 'placement_id';
        $fatherId       = $placemetShow ? 'app_id' : 'camp_id';

        foreach ($actions as $key => $action) {
            // get action count for specific day
            $result = Self::select(
                                    DB::raw('count(*) as action'),
                                    "sdkactions.{$id} as adId"
                                );

            if( $request->has('from') && $request->has('to') ){
                $from       = $request->input("from");
                $to         = $request->input("to");
                $result->whereDate("sdkactions.created_at", ">=", $from)
                        ->whereDate("sdkactions.created_at", "<=", $to);
            }else{
                $interval = '6 days';
                $result->where( "sdkactions.created_at", '<=', date('Y-m-d') . " 23:59:59")
                        ->where( "sdkactions.created_at", '>=', date_create()->sub(date_interval_create_from_date_string($interval))->format("Y-m-d 00:00:00") );
            }

            if( $action == REQUEST_ACTION ){
                $result->join($mainTable, "{$mainTable}.id", '=', "sdkactions.{$id}" )
                        ->join($fatherTable, "{$fatherTable}.id", '=', "{$mainTable}.{$fatherId}")
                        ->join("users", "users.id", "=", "{$fatherTable}.user_id")
                        ->select(
                                    "sdkactions.{$id} as adId",
                                    "{$mainTable}.*",
                                    "{$fatherTable}.name as fatherName",
                                    "{$fatherTable}.fcategory",
                                    "{$fatherTable}.scategory",
                                    "users.fname as userFname",
                                    "users.lname as userLname",
                                    DB::raw('count(*) as action')
                                );
            }
            $result = $result->where("sdkactions.{$otherId}", '=', $adId)
                                ->where("sdkactions.action", '=', $action)
                                ->groupBy("sdkactions.{$id}")
                                ->get();

            // To create an array of ids as key andcounts as it's value. 
            $actionsResult[$action] = ($action == REQUEST_ACTION) ? $result : array_pluck( $result, 'action', 'adId' );
        }
    
        if( count( $actionsResult[REQUEST_ACTION] ) > 0 ){
            $returnArray = [];
            foreach ($actionsResult[ REQUEST_ACTION ] as $key => $item) {
                
                if( $item->adId ){

                    // Adapting values to be inserted into DB 
                    $impressions    = isset($actionsResult[SHOW_ACTION][$item->adId])   ? $actionsResult[SHOW_ACTION][$item->adId] : 0;
                    $clicks         = isset($actionsResult[CLICK_ACTION][$item->adId])  ? $actionsResult[CLICK_ACTION][$item->adId] : 0;
                    $installed      = isset($actionsResult[INSTALL_ACTION][$item->adId]) ? $actionsResult[INSTALL_ACTION][$item->adId] : 0;
                   
                    $returnArray[] = [
                            'adCreativeImage'   => $item->image_file,
                            'adCreativeLink'    => $item->click_url,
                            'adName'            => $item->name,
                            'fatherName'        => $item->fatherName,
                            'fatherId'          => $item->{$fatherId},
                            'accountName'       => $item->userFname . " " . $item->userLname,
                            'format'            => $formats[$item->format],
                            'fcategory'         => isset($categories[$item->fcategory]) ? $categories[$item->fcategory] : '',
                            'scategory'         => isset($categories[$item->scategory]) ? $categories[$item->scategory] : '',
                            'adId'              => $item->adId,
                            'requests'          => $item->action,
                            'impressions'       => $impressions,
                            'clicks'            => $clicks
                        ];
                }
            }
            return $returnArray;   
        }
    }
}
