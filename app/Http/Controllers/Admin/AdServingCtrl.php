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
    // Country id.
    private $_countryId;

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
    public function __construct($placementId, $deviceId, $appPackage, $requestId, $countryId = null)
    {
        $this->_placementId = $placementId;
        $this->_deviceId = $deviceId;
        $this->_appPackage = $appPackage;
        $this->_requestId = $requestId;
        $this->_countryId = $countryId;
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
        $relevantAds = $adServingQuery->getCreativeAds($this->_countryId);  

        if( sizeof($relevantAds) > 0 ){

            $relevantAdsPriority =  $this->_loopOverAdsAndDoSomeProcessing($relevantAds, $shownAds);

            // To exclude the shown ads for that device
            $filteredAds = $this->_excludeShownAdsForThatDevice($relevantAdsPriority, $shownAds);

            // The selected ad id by using the probability function.
            $selectedAdKey = $this->_sortAdsByProbability( $filteredAds, array_sum($filteredAds) );

            // Get the ad object using ad id.
            $selectedAd = $relevantAds[ $selectedAdKey ];

            return [
                    'status'        => true,
                    'imagesPath'    => url('uploads/ad-images/'),
                    'requestId'     => $this->_requestId,
                    'general_frequency_capping' => config('system.general_frequency_capping'),
                    'adsObject'     => (array) $selectedAd
                ];
        }else{
            return [
                    'status'    => false,
                    'error'     => "There is no suitable ads for this unit."
                ];
            // Get why there is no relevant ads. To help testing
            $errorValidator = new ValidateAdServingEmptyCtrl($this->_placementId, $this->_deviceId, $this->_appPackage, $this->_requestId);
            return $errorValidator->getEmptyRelevantError();
        }

    }

    /**
     * _loopOverAdsAndDoSomeProcessing. To loop over the ads array and process it.
     *
     * @param  reference $relevantAds
     * @param  string $shownAds
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _loopOverAdsAndDoSomeProcessing( &$relevantAds, $shownAds )
    {
        $new = [];
        $relevantAdsIds = [];
        $relevantAdsPriority = [];

        foreach( $relevantAds as $relevantAd ){
            // to prevent repeated Ids
            if( in_array($relevantAd->id, $relevantAdsIds ) ) continue;
            
            $relevantAdId = $relevantAd->id;
            $new[$relevantAdId] = $relevantAd;
            $relevantAdsIds[] = $relevantAdId; 

            $relevantAdsPriority[$relevantAdId] = $relevantAd->simi_relevant ? 0.5 : 1;
        }
        $relevantAds = $new;
        
        return $relevantAdsPriority;
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
