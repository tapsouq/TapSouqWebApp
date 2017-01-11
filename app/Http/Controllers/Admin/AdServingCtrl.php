<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\SdkAction;

use Session, Validator;
class AdServingCtrl extends Controller
{
    // Ad placement id.
    private $_placementId;
    // Device id.
    private $_deviceId;
    // Application package id.
    private $_appPackage;
    // Sdk request id.
    private $_requestId;

    /**
     * __construct. To init class.
     *
     * @param  int $placementId
     * @param  int $deviceId
     * @param  int $appPackage
     * @param  int $requestId
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function __construct($placementId, $deviceId, $appPackage, $requestId)
    {
        $this->_placementId = $placementId;
        $this->_deviceId = $deviceId;
        $this->_appPackage = $appPackage;
        $this->_requestId = $requestId;
    }

    /**
     * getSuitableCreativeAd. To get the suitable creative ad object if there is,
     * else return error message with the reason of why there isn't suitable ad.
     *
     * @param  string $shownAds
     * @return mixed.
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function getSuitableCreativeAd( $shownAds = null )
    {
        $sumOfweights = 0;
        $adsProbabilities = []; 
        
        // Get a list of relevant ads that suit the placement ad that sent the sdk request
        $adServingQuery = new AdServingQueryCtrl($this->_placementId, $this->_appPackage);
        $relevantAds = $adServingQuery->getCreativeAds($this->_deviceId);  

        if( sizeof($relevantAds) > 0 ){

            // Get an array of ads ids as key and it's priority as value.
            $relevantAdsWithPriorities  = array_pluck($relevantAds, 'priority', 'id');

            // Adapt $relevantAds array by making an array with ads ids as key and the ads objects as it's values.
            $callBackFcn = [ "App\Http\Controllers\Admin\AdServingCtrl", "_adaptRelevantAdsArray"];
            $relevantAdsWithIdAsKey     = array_reduce($relevantAds, $callBackFcn, []);

            // To exclude the shown ads for that device
            $adsArray = $this->_excludeShownAdsForThatDevice($relevantAdsWithPriorities, $shownAds);
            
            // The selected ad id by using the probability function.
            $selectedAdKey = $this->_sortAdsByProbability( $adsArray, array_sum($adsArray) );

            // Get the ad object using ad id.
            $selectedAd = $relevantAdsWithIdAsKey[ $selectedAdKey ];

            return [
                    'status'        => true,
                    'requestId'     => $this->_requestId,
                    'adsObject'     => (array) $selectedAd
                ];
        }else{
            $test = [
                    'status'        => false,
                    'error'         => 'There is no suitable ads'
                ];
            // Get why there is no relevant ads. To help testing
            $errorValidator = new ValidateAdServingEmptyCtrl($this->_placementId, $this->_deviceId, $this->_appPackage, $this->_requestId);
            return $errorValidator->getEmptyRelevantError();
        }

    }

  	/**
  	 * _sortAdsByProbability. To sort ads using probability function.
  	 *
     * @param  array $adsArray
  	 * @param  int $sumOfweights
  	 * @return return
  	 * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
  	 * @copyright Smart Applications Co. <www.smartapps-ye.com>
  	 */
  	private function _sortAdsByProbability( $adsArray, $sumOfweights )
  	{
		$accumulated_probability = 0;
		$rand = mt_rand(1,$sumOfweights);
   		$adsArray = $this->_shuffleArray($adsArray);
 
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
   * @param  array $array
   * @return return
   * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
   * @copyright Smart Applications Co. <www.smartapps-ye.com>
   */
  	private function _shuffleArray( $array )
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
    private function _excludeShownAdsForThatDevice($relevantAds, $shownAds)
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
     * @param  mixed $item
     * @return array
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private static function _adaptRelevantAdsArray( &$result, $item )
    {
        $result[$item->id] = $item;
        return $result;
    }

}
