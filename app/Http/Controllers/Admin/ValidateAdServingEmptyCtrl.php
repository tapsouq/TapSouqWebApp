<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator, DB;
class ValidateAdServingEmptyCtrl extends Controller
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
     * __construct. To init the class.
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function __construct( $placementId, $deviceId, $appPackage, $requestId)
    {
    	$this->_placementId = $placementId;
        $this->_deviceId = $deviceId;
        $this->_appPackage = $appPackage;
        $this->_requestId = $requestId;
    }

    /**
     * getEmptyRelevantError. To return the reason that there is no suitable ads.
     * @param  void
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function getEmptyRelevantError()
    {
        $validator = $this->_validateModelsIds();
        // If Ids validator fails
        if( $validator->fails() ){
            return [
                    'status'    => false,
                    'error'     => $validator->errors()->first()
                ];
        }else{
            if( $this->_validateAppPackageWithAdplacementId() == null){
                return [
                        'status'    =>false,
                        'error'     => "The ad placement isn't in the application which you send it's package id."
                    ];
            }
            // To check for the most of ad serving algorithm step by step till find the error.
            return $this->_validateAdServingParamatersStepByStep();
        }

    }

    /**
     * _validateModelsIds. To validate model ids as ad_placement, davice and request ids. and app package id.
     *
     * @param  void
     * @return Validator $validator
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _validateModelsIds()
    {
        $inputs = [
                'placementId'   => $this->_placementId,
                'deviceId'      => $this->_deviceId,
                'appPackage'    => $this->_appPackage,
                'requestId'     => $this->_requestId
            ];
        
        $rules = [
                'placementId'   => 'exists:ad_placement,id',
                'deviceId'      => 'exists:devices,id',
                'appPackage'    => 'exists:applications,package_id',
                'requestId'     => 'exists:sdk_requests,id'
            ];

        return Validator::make($inputs, $rules);
    }

    /**
     * _validateAppPackageWithAdplacementId. To check that the app package for the application which the ad placement within.
     *
     * @param  int $zoneId
     * @param  string $appPackage
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _validateAppPackageWithAdplacementId()
    {
        return  DB::table('applications')
                    ->join('ad_placement', 'ad_placement.app_id', '=', 'applications.id')
                    ->where('ad_placement.id', '=', $this->_placementId)
                    ->where('applications.package_id', '=', $this->_appPackage)
                    ->first();
    }

    /**
     * _validateAdServingParamatersStepByStep. To start validation where there is no suitable ads step by step untill find the error.
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _validateAdServingParamatersStepByStep()
    {
        return [
                'status'        => false,
                'error'         => 'There is no suitable ads'
            ];;
    }
}
