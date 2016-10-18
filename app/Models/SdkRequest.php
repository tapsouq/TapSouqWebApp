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
	    $request->created_at    = date('Y-m-d H:i:s');
	    $request->updated_at    = date('Y-m-d H:i:s');
	    
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
}