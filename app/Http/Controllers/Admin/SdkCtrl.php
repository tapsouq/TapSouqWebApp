<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB, Validator;

use App\Models\SdkAction, App\Models\SdkRequest, App\Models\Device;
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
        
        $country    = Country::find($array[ADD_COUNTRY]);
        $language   = Language::find($array[ADD_LANG]);
        $platform   = $array[ADD_PLATFORM];

        if( $language == null ){
             $response = [ 
                    'status'    => false,
                    'error'     => "The language id isn't valid." 
                 ];
             return response()->json($response);   
        }

        if( $country == null  ){
            $response = [ 
                    'status'    => false,
                    'error'     => "The country id isn't valid." 
                ];
            return response()->json($response);
        }


        if( ! in_array($platform, [1, 2] ) ){
             $response = [ 
                     'status'    => false,
                     'error'     => "The platform isn't valid." 
                 ];
             return response()->json($response);   
        }
        

        $device  = new Device();
    
        $device->language       = $language->id;
        $device->country        = $country->id;
        $device->platform       = $array[ADD_PLATFORM]; 
        $device->advertising_id = $array[ADD_ADVERTISING_ID]; 
        $device->manefacturer   = $array[ADD_MANEFACTURER]; 
        $device->model          = $array[ADD_MODEL];
        $device->os_version     = $array[ADD_OS_VER];
        $device->city           = $array[ADD_CITY];
        $device->carrier        = $array[ADD_CARRIER];
        $device->sdk_version    = $array[TAPSOUQ_SDK_VER];

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
        $country    = Country::find($array[UPDATE_COUNTRY]);
        $language   = Language::find($array[UPDATE_LANG]);
        
        $deviceId   = $array[UPDATE_DEVICE_ID];
        $device     = Device::find($deviceId);

        if( $device != null ){
            if( $language != null ){
                if( $country != null ){

                    $device->country        = $country->id;
                    $device->language       = $language->id;
                    $device->os_version     = $array[UPDATE_OS];
                    $device->model          = $array[UPDATE_MODEL];
                    $device->manefacturer   = $array[UPDATE_MANEFACTURER];

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
                            'error'     => "The country id isn't valid."
                        ];
                }
            }else {
                $response = [
                        'status'    => false,
                        'error'     => "The language id isn't valid."
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
        
        switch ($action) {
            case REQUEST_ACTION:
                # code ...
                return $this->_insertRequestAction($array);
                break;
            case SHOW_ACTION:
                # code ...
                return $this->_insertSdkAction(SHOW_ACTION, $requestId, $creativeId);
                break;
            case CLICK_ACTION:
                # code ...
                return $this->_insertSdkAction(CLICK_ACTION, $requestId, $creativeId);
                break;
            case INSTALL_ACTION:
                # code ...
                return $this->_insertSdkAction(INSTALL_ACTION, $requestId, $creativeId);
                break;
            default:
                return $this->_returnNotValidAction( $action );
                break;
        }
    }

    /**
      * _insertRequestAction. To insert the request action.
      *
      * @param array $inputs.
      * @return void
      * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
      * @copyright Smart Applications Co. <www.smartapps-ye.com>
      */
     private function _insertRequestAction ( $inputs ){
        
        $deviceId       = $inputs[DEVICE_ID];
        $placementId    = $inputs[PLACEMENT_ID];
        $appPackage     = $inputs[APP_PACKAGE];

        // to insert request action into database
        $requestId = SdkRequest::insertRequest($placementId, 0, $deviceId);
        if( $requestId ){

            // To get creative ads from database.
            $result = SdkAction::getCreativeAds($placementId, $deviceId, $appPackage);
            
            if( sizeof( $result ) > 0 ){
                $response = [
                        'status'        => true,
                        'requestId'     => $requestId,
                        'adsObject'     => (array) $result[mt_rand(0, count($result) - 1 )]
                    ];
            }else{
                $response = [
                        'status'    => false,
                        'error'     => 'There is no suitable ads.'
                    ];
            }
        }else{
            $response = [
                    'status'    => false,
                    'error'     => 'There are such error.'
                ];
        }
        return response()->json($response);
    }

    /**
     * _insertSdkAction. To insert show action
     *
     * @param int $action
     * @param int $requestId
     * @param int $creativeId
     * @return json
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _insertSdkAction ( $action, $requestId, $creativeId ){
        
        if( $action == SHOW_ACTION ){
            SdkRequest::updateRequest( $requestId, $creativeId);
        }

        $inserted = SdkAction::insertAction($action, $requestId);

        if($inserted){
            $response = [
                    'status'    => true,
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
                'error'     => "Action name `" . $action . "` isn't valid. Please enter action name in values " . implode( ',' , array_keys( config('consts.sdk_actions') ) ) 
            ];

        return response()->json( $response );
    }
}
