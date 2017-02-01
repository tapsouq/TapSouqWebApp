<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, Validator, Session;
use App\Models\SdkActions, App\Models\SdkActionsTmp;
use App\Models\SdkRequest, App\Models\Device;
use App\Models\Country, App\Models\Language;
class SdkCtrl extends Controller
{
    /**
     * addDevice. To add new device while launching the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return json
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function addDevice ( Request $request ){
        $array     = $request->segments();

        $googleAdvId    = $array[ADD_ADVERTISING_ID];
        $platform       = $array[ADD_PLATFORM];
        $countryId      = $array[ADD_COUNTRY];
        $languageId     = $array[ADD_LANG];
        $sdkVersion     = (float)urldecode($array[TAPSOUQ_SDK_VER]);

        // To scheck is the sdk version before sdk version > 0.6
        if( $sdkVersion < 0.6 ){
            $device = Device::where('advertising_id', '=', $googleAdvId)->first();
            if( $device != null ){
                return [
                        'status'    => true,
                        'device_id' => $device->id
                    ];
            }
        }
       
        $results    = Device::validateDeviceLangAndCountryIds($languageId, $countryId); 

        if( count( $results ) == 0 ){
            $response = [ 
                    'status'    => false,
                    'error'     => "The country id isn't valid." 
                ];
            return response()->json($response);   
        }

        if( !( $results[0]->language )  ){
            $response = [ 
                    'status'    => false,
                    'error'     => "The language id isn't valid." 
                ];
            return response()->json($response);
        }

        $platforms = array_keys(config('consts.app_platforms'));
        if( ! in_array($platform, $platforms ) ){
             $response = [ 
                     'status'    => false,
                     'error'     => "The platform isn't valid." 
                 ];
             return response()->json($response);   
        }

        $device  = new Device();
    
        $device->language       = $languageId;
        $device->country        = $countryId;
        $device->platform       = $array[ADD_PLATFORM]; 
        $device->advertising_id = $googleAdvId; 
        $device->manefacturer   = urldecode($array[ADD_MANEFACTURER]); 
        $device->model          = urldecode($array[ADD_MODEL]);
        $device->os_version     = urldecode($array[ADD_OS_VER]);
        $device->os_api_version = urldecode($array[ADD_OS_API]);
        $device->carrier        = urldecode($array[ADD_CARRIER]);
        $device->sdk_version    = $sdkVersion;

        if( $device->save() ){
            $response = [
                    'status'        => true,
                    'device_id'     => $device->id
                ];
        }else{
            $response = [ 
                    'status'    => false,
                    'error'     => 'Ther are such error!'
                ];
        }

        return response()->json($response);
    }

    /**
     * updateDevice. To update device information
     *
     * @param \Illuminate\Http\Request $request
     * @return json
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function updateDevice ( Request $request ){

        $array      = $request->segments();
        $countryId    = $array[UPDATE_COUNTRY];
        $languageId   = $array[UPDATE_LANG];

        $deviceId   = $array[UPDATE_DEVICE_ID];
        $device     = Device::find($deviceId);

        if( $device != null ){

            $results    = Device::validateDeviceLangAndCountryIds($languageId, $countryId);
            
            if( count( $results ) != 0 ){
                if( $results[0]->language ){

                    $device->language       = $languageId;
                    $device->country        = $countryId;
                    $device->manefacturer   = urldecode($array[UPDATE_MANEFACTURER]); 
                    $device->model          = urldecode($array[UPDATE_MODEL]);
                    $device->os_version     = urldecode($array[UPDATE_OS_VER]);
                    $device->os_api_version = urldecode($array[UPDATE_OS_API]);
                    $device->carrier        = urldecode($array[UPDATE_CARRIER]);
                    $device->sdk_version    = urldecode($array[UPDATE_SDK_VER]);

                    if( $device->save() ){
                        $response = [
                                'status'        => true,
                                'device_id'     => $device->id,
                                'msg'           => 'Successfully update the device.'
                            ];
                    }else{
                        $response = [
                                'status'        => false,
                                'error'         => "Ther are such error."
                            ];
                    }
                }else {
                    $response = [
                            'status'    => false,
                            'error'     => "The language id isn't valid."
                        ];
                }
            }else {
                $response = [
                        'status'    => false,
                        'error'     => "The country id isn't valid."
                    ];
            }
        }else{
            $response = [
                    'status'        => false,
                    'error'         => "The device id isn't valid."
                ];
        }
        return response()->json($response);
    }

    /**
     * setAction. To save the action into db.
     *
     * @param \Illuminate\Http\Request $request
     * @return json
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function setAction ( Request $request ){
        
        $array          = $request->segments();
        $action         = $array[ACTION_NAME];
        $requestId      = $array[REQUEST_ID];
        $creativeId     = $array[CREATIVE_ID];
        $deviceId       = $array[DEVICE_ID];

        switch ($action) {
            case REQUEST_ACTION:
                # code ...
                return $this->_insertRequestAction($request);
                break;
            case SHOW_ACTION:
                # code ...
                return $this->_insertSdkAction($request);
                break;
            case CLICK_ACTION:
                # code ...
                return $this->_insertSdkAction($request);
                break;
            case INSTALL_ACTION:
                # code ...
                return $this->_insertSdkAction( $request);
                break;
            default:
                return $this->_returnNotValidAction( $action );
                break;
        }
    }

    /**
      * _insertRequestAction. To insert the request action.
      *
      * @param \Illuminate\Http\Request $request
      * @return void
      * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
      * @copyright Smart Applications Co. <www.smartapps-ye.com>
      */
     private function _insertRequestAction ( $request ){
        
        $inputs          = $request->segments();

        $deviceId       = $inputs[DEVICE_ID];
        $placementId    = $inputs[PLACEMENT_ID];
        $appPackage     = $inputs[APP_PACKAGE];

        if( $request->has('country') ){
            $countryId = $request->input('country');
        }else{
            $device = Device::find($deviceId);
            $countryId = ($device != null) ? $device->country : 0; 
        }

        // to insert request action into database
        $requestId = SdkActionsTmp::insertRequest($placementId, $deviceId);
        if( $requestId ){

            // To get suitable creative ad if ther is.
            $AdServing = new AdServingCtrl($placementId, $deviceId, $appPackage, $requestId, $countryId );
            return $AdServing->getSuitableCreativeAd($request->input('ads'));
        }else{
            $response = [
                    'status'    => false,
                    'error'     => 'There are such error.'
                ];
        }
        return response()->json($response);
    }

    /**
     * _insertSdkAction. To insert sdk actions
     *
     * @param  \Illuminate\Http\Request $request
     * @return json
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _insertSdkAction ( $request){
        
        // insert the sdk action into DB
        $inserted = SdkActionsTmp::insertAction($request);

        if($inserted){
            $response = [
                    'status'    => true
                ];
        }else{
            $response = [
                    'status'    => false,
                    'error'     => 'There are such error.'
                ];
        }
        return response()->json($response);
    }

    /**
     * _returnNotValidAction. To return msg of not valid action.
     *
     * @param int $action
     * @return json
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _returnNotValidAction ( $action ){
        $response =  [
                'status'    => false,
                'error'     => "Action `" . $action . "` isn't valid. Please enter action within values " . implode( ',' , array_keys( config('consts.sdk_actions') ) ) 
            ];

        return response()->json( $response );
    }
}
