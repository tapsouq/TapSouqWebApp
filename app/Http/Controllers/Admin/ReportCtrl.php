<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth, DB, Validator;
use App\Models\Category, App\Models\Country, App\Models\Zone;
use App\Models\Ads, App\Models\SdkAction, App\Models\SdkRequest;
use App\Models\Application, App\Models\Device, App\Models\Language;

class ReportCtrl extends Controller
{
    // the main Title of all pages controlled by this controller
    protected $_mTitle;
    // authenticated user
    private $_user;
    // All categories
    private $_categories;
    // All countries
    private $_countries;
    // init per page
    private $_initPerPage;

    /**
     * __construct. To init the class
     * 
     * @param void
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function __construct (){
        $this->_mTitle      = trans( 'admin.reports' );
        $this->_user = Auth::user();
        $this->_categories      = array_pluck( Category::all()->toArray(), 'name', 'id' ); 
        $this->_countries       = Country::all();
        $this->_initPerPage     = config('consts.page_sizes')[0];
    }

    /**
     * showRelevant. To show the relevant creative ads.
     *
     * @param int $zone_id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function showRelevant ( $zone_id, Request $request){
        $mTitle = $this->_mTitle;
        $title = trans('admin.relevant_ads');
        $categories = $this->_categories;
        $countries  = $this->_countries;

        // Validate the ad placement. and get the package id to be used later.
        $zone = Zone::join('applications', 'applications.id', '=', 'ad_placement.app_id')
                    ->where('ad_placement.id', $zone_id)
                    ->select('ad_placement.*')
                    ->select('applications.package_id')
                    ->first();
        // If invalid ad placement id. Redirect to the admin page with spm message
        if( $zone == null ){
            return redirect('admin')
                        ->with('warning', trans('admin.spam_msg'));
        }

        // Validate country id and init the value with egypt id else 1.
        $egyptCountry   = Country::where('name', '=', 'egypt')->first();
        $countryId      = $request->has('country') ? $request->country : ($egyptCountry ? $egyptCountry->id : 1 );
        
        // Get the relevant creative ads for the ad placement.
        $relevantAds    = AdServingCtrl::getRelevantAds($zone_id, $countryId, $zone->package_id);

        // to Validate per page variable not to be null nor zero.
        $perPage        = $request->input('per-page') ?: $this->_initPerPage;

        // Paginate items with mentioned arguments.
        $items = sizeof($relevantAds) ? paginate( $relevantAds, $perPage, $request->input('page') ) : [];

        $data = ['title', 'mTitle', 'relevantAds', 'categories', 'countries', 'countryId', 'items'];
        
        return view('admin.reports.show-relevant')
                    ->with(compact($data));
    }

    
    /**
     * showShownAds. To show shown creative ads.
     *
     * @param int $zone_id
     * @param int $request
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function showShownAds( $zone_id, Request $request ){
        $mTitle = $this->_mTitle;
        $title = trans('admin.shown_ads');

        // to Validate per page variable not to be null nor zero.
        $perPage        = $request->input('per-page') ?: $this->_initPerPage;

        // Get shown creative
        $shownAds = SdkRequest::getShownAds($zone_id, $request);

        // Paginate items with mentioned arguments.
        $items = sizeof($shownAds) ? paginate( $shownAds, $perPage, $request->input('page') ) : [];

        $data = [ 'title', 'mTitle', 'shownAds', 'items' ];
        return view('admin.reports.show-shown')
                ->with(compact($data));

    }

    /**
     * showCampAndAds. To show campaigns and creative ads page.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function showCampAndAds ( Request $request ){
        $mTitle     = $this->_mTitle;
        $title      = trans('admin.campaigns_and_creatives');
        $status     = RUNNING_AD;

        // To validate status. It is in ad creative states.
        if( $request->has('s') ){
            $input = $request->input('s');

            $adStates = array_keys(config('consts.ads_status'));
            $status = in_array($input, $adStates) ? $input : $status;
        }

        // to Validate per page variable not to be null nor zero.
        $perPage        = $request->input('per-page') ?: $this->_initPerPage;
        
        // To get the related campaigns and creatives for all users in specific $status from db model
        $items      = Ads::getCampsAndAds( $status )
                            ->paginate( $perPage );     

        $data = [ 'mTitle', 'title', 'items', 'status' ];
        return view('admin.reports.show-camp-ads')
                ->with( compact( $data ) );
    }

    /**
     * showAllApps. To show the page of all applications information.
     *
     * @param \Illuminate\Http\Request $request
     * @return return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function showAllApps ( Request $request ){
        $mTitle     = $this->_mTitle;
        $title      = trans('admin.show_apps_info');
        $status     = ACTIVE_APP;
        
        // To validate status. It is in application states.
        if( $request->has('s') ){
            $input = $request->input('s');

            $appStates  = array_keys(config('consts.app_status'));
            $status     = in_array($input, $appStates) ? $input : $status;
        }

        // to Validate per page variable not to be null nor zero.
        $perPage        = $request->input('per-page') ?: $this->_initPerPage;

        // get the applications information from db model
        $items  = Application::getAppsInfo( $status )
                                ->paginate( $perPage );

        $data = [ 'mTitle', 'title', 'items', 'status' ];
        return view('admin.reports.show-apps-info')
                ->with( compact( $data ) );
    }

    /**
     * showDeviceReports. To show device country reports.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $reportType
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function showDeviceReports ( Request $request, $reportType ){
        $reportTypes = [ 'countries', 'languages', 'manefacturer', 'model', 'os_version', 'carrier' ];

        if( ! in_array($reportType, $reportTypes) ){
            return redirect('admin')
                            ->with('warning', trans('lang.spam_msg'));
        }

        switch ($reportType) {
            case 'countries':
                # code...
                return $this->_showDeviceCountries($request);
                break;
            case 'languages':
                #code
                return $this->_showDeviceLanguages($request);
            default:
                # code...
                return $this->_showOtherDeviceReports($request, $reportType);
                break;
        }
    }

    /**
     * _showDeviceCountries. To show device country reports.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _showDeviceCountries ( Request $request ){
        $mTitle     = $this->_mTitle;
        $title      = trans('admin.device_countries');

        // To get all devices for every country
        $allDevices = Country::leftJoin('devices',  'devices.country', '=', 'countries.id')
                                ->select(
                                        'countries.*',
                                        DB::raw('COUNT(distinct(devices.id)) as devicesCount')
                                    )
                                ->groupBy('countries.id')
                                ->orderBy('countries.id');

        // clone the allDevices object to get the new ones.
        $newDevices = clone($allDevices);
        filterByTimeperiod($newDevices, $request, "devices");

        // clone the allDevices object to get the active ones.
        $activeDevices  = clone($allDevices);
        $activeDevices->leftJoin('sdk_requests', 'sdk_requests.device_id', '=', 'devices.id');
        filterByTimeperiod($activeDevices, $request, "sdk_requests");

        $allDevices     = $allDevices->get();
        $newDevices     = array_pluck( $newDevices->get(), 'devicesCount', 'code');
        $activeDevices  = array_pluck( $activeDevices->get(), 'devicesCount', 'code');

        $items = $this->_adaptDeviceCountriesArray( $allDevices, $newDevices, $activeDevices );
        
        $data = [ 'mTitle', 'title', 'items', 'codes' ];
        return view('admin.reports.devices.countries')
                ->with( compact( $data ) );
    }

    /**
     * _showDeviceLanguages. To show devices languages reports.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response.
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _showDeviceLanguages(Request $request)
    {
        $mTitle     = $this->_mTitle; 
        $title      = trans("admin.devices_languages");
    
        // To get all devices for every language
        $allDevices = Language::leftJoin('devices',  'devices.language', '=', 'languages.id')
                                ->select(
                                        'languages.*',
                                        DB::raw('COUNT(distinct(devices.id)) as devicesCount')
                                    )
                                ->groupBy('languages.id')
                                ->orderBy('languages.id');

        // clone the allDevices object to get the new ones.
        $newDevices = clone($allDevices);
        filterByTimeperiod($newDevices, $request, "devices");

        // clone the allDevices object to get the active ones.
        $activeDevices  = clone($allDevices);
        $activeDevices->leftJoin('sdk_requests', 'sdk_requests.device_id', '=', 'devices.id');
        filterByTimeperiod($activeDevices, $request, "sdk_requests");

        $allDevices     = $allDevices->get();
        $newDevices     = array_pluck( $newDevices->get(), 'devicesCount', 'name');
        $activeDevices  = array_pluck( $activeDevices->get(), 'devicesCount', 'name');

        $items = $this->_adaptDeviceLanguagesArray( $allDevices, $newDevices, $activeDevices );

        $data  = [ 'mTitle', 'title', 'items' ];
        return view('admin.reports.devices.languages')
                    ->with( compact( $data ) );
    }

    /**
     * _adaptDevicesReports. To adapt device reports(models, os versions).
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _showOtherDeviceReports( Request $request, $report )
    {
        $mTitle = $this->_mTitle;
        $title  = trans("admin.{$report}_reports");

        // To get all devices for every language
        $allDevices =   Device::select(
                                        'devices.*',
                                        DB::raw('COUNT(distinct(devices.id)) as devicesCount')
                                    )
                                ->groupBy("devices.{$report}")
                                ->orderBy("devices.{$report}");

        // clone the allDevices object to get the new ones.
        $newDevices = clone($allDevices);
        filterByTimeperiod($newDevices, $request, "devices");

        // clone the allDevices object to get the active ones.
        $activeDevices  = clone($allDevices);
        $activeDevices->leftJoin('sdk_requests', 'sdk_requests.device_id', '=', 'devices.id');
        filterByTimeperiod($activeDevices, $request, "sdk_requests");

        $allDevices     = $allDevices->get();
        $newDevices     = array_pluck( $newDevices->get(), 'devicesCount', "{$report}");
        $activeDevices  = array_pluck( $activeDevices->get(), 'devicesCount', "{$report}");

        $items = $this->_adaptDeviceReportArray( $allDevices, $newDevices, $activeDevices, $report );

        $data = [ 'mTitle', 'title',  'items'];
        return view("admin.reports.devices.other-reports")
                    ->with(compact($data));
    }

    /**
     * _adaptDeviceCountriesArray. To adapt devices countries array.
     *
     * @param  array  $allDevices
     * @param  array  $newDevices
     * @param  array  $activeDevices
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _adaptDeviceCountriesArray( $allDevices, $newDevices, $activeDevices )
    {
        $allRecords = [];

        foreach ($allDevices as $key => $row) {

            $code = $row->code;
            
            $newDevice      = isset($newDevices[$code]) ? $newDevices[$code] : 0;
            $activeDevice   = isset($activeDevices[$code]) ? $activeDevices[$code] : 0;
            $code           = strtolower($code);

            $allRecords['allDevicesCount'][$code]      = $row->devicesCount;  
            $allRecords['newDevicesCount'][$code]      = $newDevice;  
            $allRecords['activeDevicesCount'][$code]   = $activeDevice;  

            $allRecords['allDevices'][] = [
                    'id'        => $row->id,
                    'name'      => $row->name,
                    'code'      => $code,
                    'all'       => $row->devicesCount,
                    'new'       => $newDevice,
                    'active'    => $activeDevice
                ];
        }

        return json_decode(json_encode($allRecords));
    }

    /**
     * _adaptDeviceLanguagesArray. To adapt devices languages array.
     *
     * @param  array  $allDevices
     * @param  array  $newDevices
     * @param  array  $activeDevices
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _adaptDeviceLanguagesArray( $allDevices, $newDevices, $activeDevices)
    {
        $allRecords = [ 'allDevices' =>[], 'allDevicesCount' => [], 'newDevicesCount' => [], 'activeDevicesCount' => [] ];

        foreach ($allDevices as $key => $row) {
            $newDevice = $activeDevice = 0;    
            $name = $row->name;

            if( isset($newDevices[$name]) ){
                
                $newDevice = (int)$newDevices[$name];
                $allRecords['newDevicesCount'][]      = [ 'name' => $name, 'y' => $newDevice ];  
            }
            if( isset($activeDevices[$name]) ){
                $activeDevice = (int)$activeDevices[$name];
                $allRecords['activeDevicesCount'][]   = [ 'name' => $name, 'y' => $activeDevice ];  
            }
            
            if( $row->devicesCount != 0 ){
                $allRecords['allDevicesCount'][]      = [ 'name' => $name, 'y' => (int)$row->devicesCount ];
            }

            $allRecords['allDevices'][] = [
                    'id'        => $row->id,
                    'name'      => $name,
                    'all'       => $row->devicesCount,
                    'new'       => $newDevice,
                    'active'    => $activeDevice
                ];
        }

        return json_decode(json_encode($allRecords));
    }

    /**
     * _adaptDeviceManefacturerArray. To adapt devices manefacturers array.
     *
     * @param  array  $allDevices
     * @param  array  $newDevices
     * @param  array  $activeDevices
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _adaptDeviceReportArray( $allDevices, $newDevices, $activeDevices, $report)
    {
        $allRecords = [ 'allDevices' =>[], 'allDevicesCount' => [], 'newDevicesCount' => [], 'activeDevicesCount' => [] ];

        foreach ($allDevices as $key => $row) {
            $newDevice  = $activeDevice = 0;    
            $reportName = $row->{$report};

            $escapedReportName = str_replace( ["'", '"'], ["##", ''], $reportName);

            if( isset($newDevices[$reportName]) ){
                $newDevice = (int)$newDevices[$reportName];
                $allRecords['newDevicesCount'][]      = [ 'name' => $escapedReportName, 'y' => $newDevice ];  
            }
            if( isset($activeDevices[$reportName]) ){
                $activeDevice = (int)$activeDevices[$reportName];
                $allRecords['activeDevicesCount'][]   = [ 'name' => $escapedReportName, 'y' => $activeDevice ];  
            }
            
            if( $row->devicesCount ){
                $allRecords['allDevicesCount'][]      = [ 'name' => $escapedReportName, 'y' => (int)$row->devicesCount ];
            }

            $allRecords['allDevices'][] = [
                    'id'        => $row->id,
                    'name'      => $reportName,
                    'all'       => $row->devicesCount,
                    'new'       => $newDevice,
                    'active'    => $activeDevice
                ];
        }

        return json_decode(json_encode($allRecords));
    }
}
