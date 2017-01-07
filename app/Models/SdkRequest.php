<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class SdkRequest extends Model
{
	protected $table = "sdk_requests";


	/**
	 * insertRequest. To insert the request sdk into database.
	 *
	 * @param int $placementId
	 * @param int $creativeId
	 * @param int $deviceId
	 * @return mixed
	 * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
	 * @copyright Smart Applications Co. <www.smartapps-ye.com>
	 */
	public static function insertRequest ( $placementId, $creativeId, $deviceId ){
	    
	    $request = new SdkRequest;
	    $request->placement_id  = $placementId;
	    $request->creative_id   = $creativeId;
	    $request->device_id     = $deviceId;
	    $request->created_at    = date('Y-m-d H:i:s', time());
	    $request->updated_at    = date('Y-m-d H:i:s', time());
	    if( $request->save() ){
	    	return $request->id;
	    }
	    return false;
	}

	/**
	 * updateRequest. To update the request sdk in database.
	 *
	 * @param int $requestId
	 * @param int $creativeId
	 * @return mixed
	 * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
	 * @copyright Smart Applications Co. <www.smartapps-ye.com>
	 */
	public static function updateRequest ( $requestId, $creativeId){
	    
	    $result = SdkRequest::where('id', '=', $requestId)
	    					   ->update(['creative_id' => $creativeId]);
	    
	    return $result;
	}

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
		$formats 		= config('consts.all_formats');

		$mainTable 		= $placemetShow ? 'ad_placement' : 'ad_creative';
		$fatherTable	= $placemetShow ? 'applications' : 'campaigns';
		$otherTable		= $placemetShow ? 'ad_creative'  : 'ad_placement';
		$id 			= $placemetShow ? 'placement_id' : 'creative_id';
		$otherId 		= $placemetShow ? 'creative_id'  : 'placement_id';
		$fatherId 		= $placemetShow ? 'app_id' : 'camp_id';

	    foreach ($actions as $key => $action) {
	        // get action count for specific day
	        $result = Self::join('sdk_actions', 'sdk_actions.request_id', '=', 'sdk_requests.id')
	        				->select(
	        						DB::raw('count(*) as action'),
	        						"sdk_requests.{$id} as adId"
	        					);

	        if( $request->has('from') && $request->has('to') ){
	            $from       = $request->input("from");
	            $to         = $request->input("to");
	            $result->whereDate("sdk_requests.created_at", ">=", $from)
	                    ->whereDate("sdk_requests.created_at", "<=", $to);
	        }else{
	            $interval = '6 days';
	            $result->where( "sdk_requests.created_at", '<=', date('Y-m-d') . " 23:59:59")
	                    ->where( "sdk_requests.created_at", '>=', date_create()->sub(date_interval_create_from_date_string($interval))->format("Y-m-d 00:00:00") );
	        }

	        if( $action == REQUEST_ACTION ){
	        	$result->join($mainTable, "{$mainTable}.id", '=', "sdk_requests.{$id}" )
	        			->join($fatherTable, "{$fatherTable}.id", '=', "{$mainTable}.{$fatherId}")
	        			->join("users", "users.id", "=", "{$fatherTable}.user_id")
	        			->select(
			        				"sdk_requests.{$id} as adId",
			        				"{$mainTable}.*",
			        				"{$fatherTable}.name as fatherName",
			        				"{$fatherTable}.fcategory",
			        				"{$fatherTable}.scategory",
			        				"users.fname as userFname",
			        				"users.lname as userLname",
	        						DB::raw('count(*) as action')
	        					);
	        }
	        $result = $result->where("sdk_requests.{$otherId}", '=', $adId)
	        					->where("sdk_actions.action", '=', $action)
	        					->groupBy("sdk_requests.{$id}")
	        					->get();

	        // To create an array of ids as key andcounts as it's value. 
	        $actionsResult[$action] = ($action == REQUEST_ACTION) ? $result : array_pluck( $result, 'action', 'adId' );
	    }
 
	    if( count( $actionsResult[REQUEST_ACTION] ) > 0 ){
	        $returnArray = [];
	        foreach ($actionsResult[ REQUEST_ACTION ] as $key => $item) {
	            
	            if( $item->adId ){

	                // Adapting values to be inserted into DB 
	                $impressions    = isset($actionsResult[SHOW_ACTION][$item->adId]) 	? $actionsResult[SHOW_ACTION][$item->adId] : 0;
	                $clicks         = isset($actionsResult[CLICK_ACTION][$item->adId]) 	? $actionsResult[CLICK_ACTION][$item->adId] : 0;
	                $installed      = isset($actionsResult[INSTALL_ACTION][$item->adId]) ? $actionsResult[INSTALL_ACTION][$item->adId] : 0;
	               
	                $returnArray[] = [
	                		'adCreativeImage'	=> $item->image_file,
	                		'adCreativeLink'	=> $item->click_url,
	                		'adName'			=> $item->name,
	                		'fatherName'		=> $item->fatherName,
	                		'fatherId'			=> $item->{$fatherId},
	                		'accountName'		=> $item->userFname . " " . $item->userLname,
	                		'format'			=> $formats[$item->format],
	                		'fcategory'			=> isset($categories[$item->fcategory]) ? $categories[$item->fcategory] : '',
	                		'scategory'			=> isset($categories[$item->scategory]) ? $categories[$item->scategory] : '',
	                        'adId'        		=> $item->adId,
	                        'requests'      	=> $item->action,
	                        'impressions'   	=> $impressions,
	                        'clicks'        	=> $clicks
	                    ];
	            }
	        }
	        return $returnArray;   
	    }
	}
}