<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\SdkAction;

use Session;
class AdServingCtrl extends Controller
{
	public function index(){
		//$rules = [0 => 20, 1 => 10, 2 => 40, 3 => 5, 4 => 25 ];
		$array = [5, 10, 20, 17];
        $bigArray = [5, 10, 20, 30, 40];

        //Session::put('device', 10);
        print_r( array_diff($bigArray, $array));
	}

    /**
     * getRelevantAd. To get the relevant ad object if there is.
     *
     * @param  int $zone_id
     * @param  int $countryId
     * @param  int $packageId
     * @param  int $requestId
     * @param  string $shownAds
     * @return mixed.
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public static function getCreativeAd( $zoneId, $countryId, $packageId, $requestId, $shownAds = null )
    {
        $sumOfweights = 0;
        $adsProbabilities = []; 
        
        // Get a list of relevant ads that suit the placement ad that sent the sdk request
        $relevantAds = SdkAction::getRelevantAds( $zoneId, $countryId, $packageId );  

        if( sizeof($relevantAds) > 0 ){

            // Get an array of ads ids as key and it's priority as value.
            $relevantAdsWithPriorities  = array_pluck($relevantAds, 'priority', 'id');

            // Adapt $relevantAds array by making an array with ads ids as key and the ads objects as it's values.
            $callBackFcn = [ "App\Http\Controllers\Admin\AdServingCtrl", "_adaptRelevatAdsArray"];
            $relevantAdsWithIdAsKey     = array_reduce($relevantAds, $callBackFcn, []);

            // To exclude the shown ads for that device
            $adsArray = self::_excludeShownAdsForThatDevice($relevantAdsWithPriorities, $shownAds);
            
            // The selected ad id by using the probability function.
            $selectedAdKey = self::_sortAdsByProbability( $adsArray, array_sum($adsArray) );

            // Get the ad object using ad id.
            $selectedAd = $relevantAdsWithIdAsKey[ $selectedAdKey ];

            return [
                    'status'        => true,
                    'requestId'     => $requestId,
                    'adsObject'     => (array) $selectedAd
                ];
        }else{
            // Get why there is no relevant ads. To help testing
            self::_getEmptyRelevantError($zone_id, $countryId, $packageId);
        }

    }

  	/**
  	 * _sortAdsByProbability. To sort ads using probability function.
  	 *
  	 * @param  param
  	 * @return return
  	 * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
  	 * @copyright Smart Applications Co. <www.smartapps-ye.com>
  	 */
  	private static function _sortAdsByProbability( $adsArray, $sumOfweights )
  	{
		$accumulated_probability = 0;
		$rand = mt_rand(1,$sumOfweights);
   		$adsArray = self::_shuffleArray($adsArray);
 
		foreach($adsArray as $key => $probability) {
  			$actual_probability = $probability + $accumulated_probability;
  			$accumulated_probability = $probability + $accumulated_probability;
 
  			if($rand < $actual_probability) {
    			return $key;
  			}
		}
    	return $key;
  	}
 
  /**
   * _shuffleArray. To shuffle arrays .
   *
   * @param  param
   * @return return
   * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
   * @copyright Smart Applications Co. <www.smartapps-ye.com>
   */
  	private static function _shuffleArray( $array )
  	{
  		$new = [];
  		$keys = array_keys($array);

  		shuffle($keys);
  		foreach($keys as $key) {
  		    $new[$key] = $array[$key];
  		}

  		return $new;
  	}

    /**
     * _excludeShownAdsForThatDevice. To exclude the creative ads that been shown once today in the device that sent the request.
     *
     * @param  array $relevantAds. all the relevant creative ads for the placement ads sent by the application 
     * @param  string $shownAds. the shown creative ads in the device today.
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private static function _excludeShownAdsForThatDevice($relevantAds, $shownAds)
    {
        $new = [];
        $shownAds = explode(",", $shownAds);
        // To get the the ads that's not being shown in the device.
        $diffKeys = array_diff(array_keys($relevantAds), $shownAds); 
        
        if( count($diffKeys) ){
            foreach ($diffKeys as $diffKey) {
                $new[$diffKey] = $relevantAds[$diffKey];
            }
            return $new;
        }

        return $relevantAds;
    }

    /**
     * _adaptRelevatAdsArray. To edit the array to make ads id as a key and the ads object as it's value.
     *
     * @param  array $adsArray
     * @return array
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private static function _adaptRelevatAdsArray( &$result, $item )
    {
        $result[$item->id] = $item;
        return $result;
    }
}
