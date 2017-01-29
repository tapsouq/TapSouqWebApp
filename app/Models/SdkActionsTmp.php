<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \DB;
class SdkActionsTmp extends Model
{
    //
    protected $table = 'sdkactions_tmp';

    /**
     * insertRequest. To insert request action into sdkactions table.
     *
     * @param  int $placementId
     * @param  int $deviceId
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public static function insertRequest($placementId, $deviceId)
    {
        $values = [
                'placement_id'  => $placementId,
                'device_id'     => $deviceId,
                'creative_id'   => 0,
                'action'        => 1,
                'created_at'    => date('H:i:s'),
                'updated_at'    => date('H:i:s')
            ];

        return DB::table('sdkactions_tmp')->insertGetId( $values );
    }

    /**
     * insertAction. To insert the into database.
     *
     * @param \Illuminate\Http\Request $request
     * @return boolean
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public static function insertAction ( $request ){
        $array          = $request->segments();
        $requestId      = $array[REQUEST_ID];
        $action         = $array[ACTION_NAME];
        $creativeId     = $array[CREATIVE_ID];
        $placementId    = $array[PLACEMENT_ID];
        $deviceId    	= $array[DEVICE_ID];


        $appId       = (int)$request->input('app_id');
        $campId      = (int)$request->input('camp_id');
        $campUser    = (int)$request->input('camp_user');
        $appUser     = (int)$request->input('app_user');
        $countryId   = (int)$request->input('country');
        $countryTier = (int)$request->input('tier');
		
        if( $action == SHOW_ACTION ){
	        
	        if( $creativeId != 0 ){

	        	if( ! ( $campId && $campUser ) ) {
			        $camp = DB::table('campaigns')
			        			->select('campaigns.id', 'campaigns.user_id');
			        if( ! $campId ){
			        	$camp->leftJoin('ad_creative', 'ad_creative.camp_id', '=', 'campaigns.id')
			        		 	->where('ad_creative.id', '=', $creativeId);
			        }else{
			        	$camp->where('campaigns.id', '=', $campId);
			        }
			        $camp = $camp->first();
			        						
			        $campId    	= (int) $camp->id;
			        $campUser 	= (int) $camp->user_id;
	        		
	        	}

	        	if( ! ( $countryTier && $countryId) ){
	        		$country = DB::table('countries')
	        						->select('countries.id', 'countries.tier');
	        		
	        		if( ! $countryId ){
	        			$country->leftJoin('devices', 'devices.country', '=', 'countries.id')
	        					->where('devices.id', '=', $deviceId);
	        		}else{
	        			$country->where('countries.id', '=', $countryId);
	        		}
	        		$country = $country->first();

					$countryId 		= $country->id;			
					$countryTier 	= $country->tier;
	        	}
	        }

	        if( ! ( $appUser && $appId ) ){
		        $app = DB::table('applications')
		        			->select('applications.id', 'applications.user_id');

		        if( ! $appId ){
					$app->leftJoin('ad_placement', 'ad_placement.app_id', '=', 'applications.id')
							->where('ad_placement.id', '=', $placementId);
		        }else{
		        	$app->where('applications.id', '=', $appId);
		        }

		        $app = $app->first();
		        $appId    	= (int) $app->id;
		        $appUser 	= $app->user_id;
	        	
	        }

        	$values['app_id'] 		= $appId;
        	$values['camp_id'] 		= $campId;
        	$values['country_id'] 	= $countryId;
        	$values['country_tier'] = $countryTier;
            $values['app_user']    	= $appUser;
            $values['camp_user']    = $campUser;
        }
        $values['creative_id']  = $creativeId;
        $values['action'] = $action;
        $values['updated_at'] = date('H:i:s');

        return DB::table('sdkactions_tmp')
                    ->where( 'id', '=', $requestId )
                    ->update( $values );
    }
}
