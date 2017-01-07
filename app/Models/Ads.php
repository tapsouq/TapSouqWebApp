<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    //
    protected $table = "ad_creative";

    /**
     * getCampsAndAds. To get the creative and campaigns with specific properties.
     *
     * @param int $status
     * @return array
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public static function getCampsAndAds ( $status ){
    	
    	return Self::join('campaigns', 'campaigns.id', '=', 'ad_creative.camp_id')
    				->join('users', 'users.id', '=', 'campaigns.user_id')
    				->where('ad_creative.status', '=', $status)
    				->select(
    							'ad_creative.*',
    							'campaigns.name as campName',
    							'users.fname',
    							'users.lname',
    							'campaigns.fcategory',
    							'campaigns.scategory'
    						)
    				->orderBy('ad_creative.created_at', 'DESC');

    }
}
