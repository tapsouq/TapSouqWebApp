<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth, DB, Validator;
use App\Models\Ads, App\Models\Campaign, App\Models\CreativeLog;
class AdsCtrl extends Controller
{
    // the main Title of all pages controlled by this controller
    protected $_mTitle;
    // authenticated user
    private $_user;
    // Rules for create and edit validation
    private $_initRules;
    // All active campaigns
    private $_camps;

    /**
     * __construct. To init the class
     *
     * @param void
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function __construct ( ){
        $this->_mTitle      = trans( 'admin.ads' );
        $this->_user        = Auth::user();
        $this->_camps       = Campaign::where( 'user_id', '=', $this->_user->id )
                                        ->where( 'status', '!=', DELETED_CAMP )->get();
        $this->_initRules   = [
                'name'          => 'required|max:255',
                'format'        => 'required|in:' . implode(',' , array_keys( config( 'consts.all_formats' ) )),
                'type'          => 'required|in:' . implode(',' , array_keys( config( 'consts.ads_types' ) )),
                'click_url'     => 'required|max:255',
                'campaign'      => 'required|exists:campaigns,id', 
                'title'         => 'required_if:type,1|max:255',
                'description'   => 'required_if:type,' . TEXT_AD . '|max:255',
                'status'        => 'in:' . implode(',' , array_keys( config( 'consts.ads_status' ) ))
            ];
    }

    /**
     * index. to show all ads page
     *
     * @param int $camp_id
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function index ( Request $request, $camp_id = null ){
        $mTitle = $this->_mTitle;
        $title  = trans( 'admin.all_ads' );
        
        $ads    = Ads::leftJoin('creative_log', 'creative_log.ads_id', '=', 'ad_creative.id')
                        ->leftJoin( 'campaigns', 'campaigns.id', '=', 'ad_creative.camp_id' )
                        ->select( 
                                    'ad_creative.*',
                                    'creative_log.created_at as time',
                                    DB::raw('DATE(creative_log.created_at) as date'), 
                                    DB::raw('SUM(creative_log.requests) AS requests'), 
                                    DB::raw('SUM(creative_log.impressions) AS impressions'), 
                                    DB::raw('SUM(creative_log.clicks) AS clicks'),
                                    DB::raw('SUM(creative_log.installed) AS installed')
                                );

        $ads    = filterByTimeperiod($ads, $request, 'creative_log');

        if( $this->_user->role != ADMIN_PRIV ){
            $ads = $ads->where( 'campaigns.status', '!=', DELETED_CAMP  )
                            ->where( 'ad_creative.status', '!=', DELETED_AD )
                            ->where( 'campaigns.user_id', '=', $this->_user->id );
        }

        // to get the ads that within that campaign
        if( $camp_id != null ){
            $ads    = $ads->where('ad_creative.camp_id', '=', $camp_id);
            $camp   = Campaign::find($camp_id);
            $title  = trans('admin.ads_of') . $camp->name; 
        }

        $chartData = adaptChartData( clone($ads), 'creative_log', false );
        $ads = $ads->groupBy('ad_creative.id')
                        ->orderBy('created_at', 'ASC')
                        ->get();

        $data   = [ 'mTitle', 'title', 'ads', 'camp', 'chartData' ];
        return view( 'admin.ads.index' )
                    ->with( compact( $data ) );
    }

    /**
     * show. To show the ads page
     *
     * @param int ads_id
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function show ( $ads_id ){
        $mTitle = $this->_mTitle;
        $ads    = Ads::find($ads_id);

        $items  = CreativeLog::join('ad_creative', 'ad_creative.id', '=', 'creative_log.ads_id')
                                ->select( 
                                            'ad_creative.*',
                                            'creative_log.created_at as time',
                                            DB::raw('DATE(creative_log.created_at) as date'), 
                                            DB::raw('SUM(creative_log.requests) AS requests'), 
                                            DB::raw('SUM(creative_log.impressions) AS impressions'), 
                                            DB::raw('SUM(creative_log.clicks) AS clicks'),
                                            DB::raw('SUM(creative_log.installed) AS installed')
                                        )
                                ->where('ad_creative.id', '=', $ads_id);

        if( is_null($ads) ){
            return redirect('admin')
                        ->with('warning', trans('lang.spam'));
        }

        $chartData      = adaptChartData( clone($items), 'creative_log', false );
        $adsDetails    = $items->groupBy('ad_creative.id')
                            ->first();

        $title  = $ads->name;
        $data = [ 'mTitle', 'title', 'chartData', 'adsDetails' ];
        return view( 'admin.ads.show' )
                    ->with( compact( $data ) );
    }

    /**
     * create. To show create ads page
     *
     * @param void
     * @return \Illuminate\Http\Response.
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function create (){
        $mTitle     = $this->_mTitle;
        $title      = trans( 'admin.add_new_ad' );
        
        // To get the camp_id from the previous link
        $camp_id    = $this->_getIdFromPrevLink();
        
        if( ! $camp_id )
            return redirect('admin')->with( 'warning', trans('lang.spam_msg') );

        $data   = [ 'mTitle', 'title', 'camp_id' ];
        return view( 'admin.ads.create' )
                    ->with( compact( $data ) );
    }

    /**
     * store. To store new created ads.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function store ( Request $request ){
        $validator = Validator::make( $request->all(), array_merge($this->_initRules, [
                'image_file'    => 'required|image|mimes:png,bmp,jpeg,jpg,gif',
                'campaign'      => 'required|exists:campaigns,id' 
            ]));

        if( $validator->fails() ){
            return redirect()->back()
                            ->withInput()
                            ->withErrors( $validator )
                            ->with( 'error', trans( 'lang.validate_msg' ) );
        }else{
            $this->_store( $request );
            return redirect('ads/all')
                            ->with( 'success', trans( 'admin.created_ads_msg' ) );
        }
    }

    /**
     * edit. To show edit adspage.
     *
     * @param int $ad_id
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function edit ( $ad_id ){
        $mTitle = $this->_mTitle;
        $title  = trans( 'admin.edit_ads' );
        $ad     = Ads::where( 'ad_creative.id', '=', $ad_id );
        
        if( $this->_user->role != ADMIN_PRIV ){
            $ad = $ad->leftJoin('campaigns', 'campaigns.id', '=', 'ad_creative.camp_id' )
                    ->where( 'campaigns.user_id', '=', $this->_user->id )
                    ->where( 'ad_creative.status', '!=', DELETED_AD )
                    ->where( 'campaigns.status', '!=', DELETED_CAMP );
        }  
        $ad     = $ad->select('ad_creative.*')->first();

        if( is_null($ad) ){
            return redirect()->back()
                            ->with( 'warning', trans('lang.spam_msg') );
        }
        $camp_id = $ad->camp_id;

        $data = [ 'mTitle', 'title', 'camp_id', 'ad' ];
        return view( 'admin.ads.create' )
                    ->with( compact( $data ) );
    }

    /**
     * save. To save edited ads
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function save ( Request $request ){
        $rules = array_merge($this->_initRules, [
                'id'            => 'required|exists:ad_creative,id',
                'image_file'    => 'image|mimes:png,bmp,jpeg,jpg,gif'
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
                            ->with( 'success', trans( 'admin.updated_ads_msg' ) );
        }
    }

    /**
      * changeStatus. To change the ads status.
      *
      * @param \Illuminate\Http\Request $request
      * @return void
      * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
      * @copyright Smart Applications Co. <www.smartapps-ye.com>
      */
    public function changeStatus ( Request $request ){
        if( $request->has( 'id' ) && $request->has('token') && $request->has('s') ){
            $ad = Ads::leftJoin( 'campaigns', 'campaigns.id', '=', 'ad_creative.camp_id' )
                    ->where( 'ad_creative.id', '=', $request->id )
                    ->select('ad_creative.*');
            $session_token = session( "_token" );
            $adStates = array_keys( config('consts.ads_status') );
            // Token Validation. and assure that campaign id exists in campaigns DB table 
            if( $request->token == $session_token && $ad->first() != null && in_array( $request->input('s'), $adStates ) ){
                // To prevent users to delete another users's campagind ( than more step )
                if( $this->_user->role != ADMIN_PRIV ){
                    $ad = $ad->where( 'campaigns.user_id', '=', $this->_user->id );
                    if( is_null( $ad->first() ) ){
                        return redirect()->back()
                                        ->with( 'warning', trans( 'lang.spam_msg' ) );
                    } 
                }

                $ad = $ad->first();
                $ad->status = $request->input('s');
                if( $ad->status == DELETED_AD ){
                    $ad->deleted_at = date( 'Y-m-d H:i:s' );
                }
                $ad->save();

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
     * _store. To store data into DB
     *
     * @param void
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _store ( Request $request ){
        
        if( $request->has('id') ){
            $ad = Ads::find($request->id);
        }else{
            $ad = new Ads;
        }

        $ad->camp_id       = $request->campaign;
        $ad->name          = $request->name;
        $ad->type          = $request->type;
        $ad->format        = $request->format;
        $ad->click_url     = $request->click_url;
        if( $request->hasFile( 'image_file' ) ){
            $ad->image_file    = uploadFile($request->image_file , 'public/uploads/ad-images/');
        }

        $ad->title         = $request->type == TEXT_AD ?  $request->title : '';
        $ad->description   = $request->type == TEXT_AD ?  $request->description : '';

        if( $request->has('status') ){
            $ad->status = $request->status;
        }

        $ad->save();
    }

    /**
     * _getIdFromPrevLink. To get the Camp_id from the previous link
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
        if( !( $segments[ $count- 3] == 'ads' && (int)$segments[ $count - 1 ]  ) ){
            return false;
        }

        return $segments[ $count - 1 ];
    }
}
