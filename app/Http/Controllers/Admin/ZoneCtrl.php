<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth, Validator, DB;
use App\Models\Application, App\Models\Zone;
use App\Models\PlacementLog;

class ZoneCtrl extends Controller
{
    // the main Title of all pages controlled by this controller
    protected $_mTitle;
    // authenticated user
    private $_user;
    // Rules for create and edit forms
    private $_initRules;
    // all applications for the user.
    private $_applications;
    /**
     * __construct. To init the class
     *
     * @param void
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function __construct ( ){
        $this->_mTitle  = trans( 'admin.placement_ads' );
        $this->_user    = Auth::user();
        $this->_applications = Application::where( 'user_id', '=', $this->_user->id )
                                        ->where( 'status', '=', ACTIVE_APP )
                                        ->get();
        $this->_initRules = [
                'format'            => 'required|in:' . implode(',' , array_keys( config( 'consts.zone_formats' ) )),
                'device_type'       => 'required|in:' . implode(',' , array_keys( config( 'consts.zone_devices' ) )),
                'name'              => 'required|max:255',
                'daily_freq_cap'    => 'required_with:daily_freq|integer|min:1',
                'hourly_freq_cap'   => 'required_with:hourly_freq|integer|min:1',
                'layout'            => 'required_if:format,' . INTERSTITIAL,
                'refresh_interval'  => 'required_if:format,' . BANNER,
                'status'            => 'in:' . implode(',' , array_keys( config( 'consts.zone_status' ) ))
            ];
    }

    /**
     * index. To show all zones page ( placement ads)
     *
     * @param int $app_id
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function index ( $app_id = null ){
        $mTitle = $this->_mTitle;
        $title  = trans( 'admin.placement_ads' );
        $zones  = PlacementLog::join('ad_placement', 'ad_placement.id', '=', 'placement_log.ads_id')
                                ->join( 'applications', 'applications.id', '=', 'ad_placement.app_id' )
                                ->select( 
                                            'ad_placement.*',
                                            'placement_log.created_at as time',
                                            DB::raw('DATE(placement_log.created_at) as date'), 
                                            DB::raw('SUM(placement_log.requests) AS requests'), 
                                            DB::raw('SUM(placement_log.impressions) AS impressions'), 
                                            DB::raw('SUM(placement_log.clicks) AS clicks'),
                                            DB::raw('SUM(placement_log.installed) AS installed')
                                        );
                        
        if( $this->_user->role != ADMIN_PRIV ){
            $zones = $zones->where( 'applications.status', '!=', DELETED_APP  )
                            ->where( 'ad_placement.status', '!=', DELETED_ZONE )
                            ->where( 'applications.user_id', '=', $this->_user->id );
        }

        // Zones for the clicked application
        if( ! is_null( $app_id ) ){
            $zones = $zones->where( 'applications.id', '=', $app_id );
            $application = Application::find($app_id);
            $title = trans('admin.ads_of') . $application->name;  
        }

        $chartData = adaptChartData( clone($zones), 'placement_log' );
        $ads = $zones->groupBy('ad_placement.id')
                        ->orderBy('created_at', 'ASC')
                        ->get();

        $data   = [ 'mTitle', 'title', 'ads', 'application', 'chartData' ];
        return view( 'admin.zone.index' )
                    ->with( compact( $data ) );
    }
    
    /**
     * show. To show the zone page
     *
     * @param int zone_id
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function show ( $zone_id ){
        $mTitle = $this->_mTitle;
        $zone   = Zone::find($zone_id);

        $items  = PlacementLog::join('ad_placement', 'ad_placement.id', '=', 'placement_log.ads_id')
                                ->select( 
                                            'ad_placement.*',
                                            'placement_log.created_at as time',
                                            DB::raw('DATE(placement_log.created_at) as date'), 
                                            DB::raw('SUM(placement_log.requests) AS requests'), 
                                            DB::raw('SUM(placement_log.impressions) AS impressions'), 
                                            DB::raw('SUM(placement_log.clicks) AS clicks'),
                                            DB::raw('SUM(placement_log.installed) AS installed')
                                        )
                                ->where('ad_placement.id', '=', $zone_id);

        if( is_null($zone) ){
            return redirect('admin')
                        ->with('warning', trans('lang.spam'));
        }

        $chartData      = adaptChartData( clone($items), 'placement_log' );
        $zoneDetails    = $items->groupBy('ad_placement.id')
                            ->first();

        $title  = $zone->name;
        $data = [ 'mTitle', 'title', 'chartData', 'zoneDetails' ];
        return view( 'admin.zone.show' )
                    ->with( compact( $data ) );
    }
    
    /**
     * create. To show Create ad's zone page.
     *
     * @param void
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function create ( ){

        $mTitle         = $this->_mTitle;
        $title          = trans( 'admin.add_new_place_ad' );

        // get the id from the previous link
        $app_id = $this->_getIdFromPrevLink();

        if( ! $app_id ) 
            return redirect('admin')->with( 'warning', trans('lang.spam_msg') );

        $data = [ 'mTitle', 'title', 'app_id' ];
        return view( 'admin.zone.create' )
                    ->with( compact( $data ) );
    }

    /**
     * store. To store the created zone.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function store ( Request $request ){
        $rules       = array_merge( $this->_initRules, [
                'application'       => "required|exists:applications,id",
            ]); 
        $validator  = Validator::make( $request->all(), $rules );
        
        if( $validator->fails() ){
            return redirect()->back()
                            ->withInput()
                            ->withErrors( $validator )
                            ->with( 'error', trans( 'lang.validate_msg' ) );
        }else{
            $this->_store( $request );
            return redirect()->back()
                            ->with( 'success', trans( 'lang.compeleted_msg' ) );
        }
    }

    /**
     * edit. To show edit zone page
     *
     * @param int $zone_id
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function edit ( $zone_id ){
        $mTitle = $this->_mTitle;
        $title  = trans( 'admin.edit_place_ad' );
        
        $zone   = Zone::where( 'id', '=', $zone_id );
        if( $this->_user->role != ADMIN_PRIV ){
            $zone = $zone->leftJoin( 'applications', 'applications.id', '=', 'ad_placement.app_id' )
                        ->where( 'applications.user_id', '=', $this->_user->id )
                        ->where( 'applications.status', '!=', DELETED_APP )
                        ->where( 'ad_placement.status', '!=', DELETED_ZONE )
                        ->select( 'ad_placement.*' );
        }
        $zone       = $zone->first();  

        if( is_null( $zone ) ){
            return redirect()->back()
                            ->with( 'warning', trans('lang.spam_msg') );
        }

        $app_id    = $zone->app_id;
        
        $data   = [ 'mTitle', 'title', 'zone', 'app_id' ];
        return view( 'admin.zone.create' )
                    ->with( compact( $data ) );
    }

    /**
     * save. To save the edited zone
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function save ( Request $request ){
        $rules = array_merge( $this->_initRules, [
                'id'    => 'required|exists:ad_placement,id',
            ]);
        $validator = Validator::make( $request->all(), $rules );
        if( $validator->fails() ){
            return redirect()->back()
                            ->withInput()
                            ->withErrors( $validator )
                            ->with( 'error', trans( 'lang.validate_msg' ) );
        }else{
            $this->_store( $request );
            return redirect()->back()
                            ->with( 'success', trans( 'lang.compeleted_msg' ) );
        }
    }
    
    /**
      * destroy. To deactivate the ad placement.
      *
      * @param \Illuminate\Http\Request $request
      * @return void
      * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
      * @copyright Smart Applications Co. <www.smartapps-ye.com>
      */
    public function destroy ( Request $request ){
        if( $request->has( 'id' ) && $request->has('token') ){
            $zone = Zone::leftJoin( 'applications', 'applications.id', '=', 'ad_placement.app_id' )
                        ->where( 'ad_placement.id', '=', $request->id )
                        ->select( 'ad_placement.*', 'applications.user_id' )
                        ->first();
            $session_token = session( "_token" );

            // Token Validation. and assure that zone id exists in ad_placement DB table 
            if( $request->token == $session_token && $zone != null ){
                // To prevent users to delete another users's zones ( than more step )
                if( $this->_user->role != ADMIN_PRIV && $zone->user_id != $this->_user->id ){ 
                    return redirect()->back()
                                    ->with( 'warning', trans( 'lang.spam_msg' ) );
                }
                $zone->status = DELETED_ZONE;
                $zone->deleted_at = date( 'Y-m-d H:i:s' );
                $zone->save();

                return redirect()->back()
                                ->with( 'success', trans( 'lang.compeleted_msg' ) );
            }
        }
        return redirect()->back()
                        ->with( 'warning', trans( 'lang.spam_msg' ) );
    }

    /** *** ***
     * Private Methods
     */
    /**
     * _store. To store the created zone into DB
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _store ( Request $request ){
        if( $request->has('id') ){
            $zone = Zone::find( $request->id );
        }else{
            $zone = new Zone;
        }

        $zone->name             = $request->name;
        $zone->app_id           = $request->application;
        $zone->format           = $request->format;
        $zone->device_type      = $request->device_type;

        $zone->daily_freq_cap   = $request->has('daily_freq') ? $request->daily_freq_cap : NULL;
        $zone->hourly_freq_cap  = $request->has('hourly_freq') ? $request->hourly_freq_cap : NULL;

        $zone->layout           = ( $request->format == INTERSTITIAL ) ? $request->layout : NULL; 
        $zone->refresh_interval = ( $request->format == BANNER ) ? $request->refresh_interval : NULL;

        if( $request->has( 'status' ) ){
            $zone->status  = $request->status;
        }
         
        $zone->save(); 
    }

    /**
     * _getIdFromPrevLink. To get the App_id from the previous link
     *
     * @param void
     * @return int
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _getIdFromPrevLink ( ){
        
        $prevUrl        = \URL::previous();
        $segments = explode('/', $prevUrl);
        
        $segments = array_values(array_filter($segments, function ($v) {
                        return $v != '';
                    }));
        $count = count($segments);
        
        // To assure that the 
        if( !( $segments[ $count- 3] == 'zone' && (int)$segments[ $count - 1 ]  ) ){
            return false;
        }

        return $segments[ $count - 1 ];
    }
}
