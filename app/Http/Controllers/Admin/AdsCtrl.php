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
                'title'         => 'required_if:type,' . TEXT_AD . '|max:255',
                'description'   => 'required_if:type,' . TEXT_AD . '|max:255',
                'status'        => 'in:' . implode(',' , array_keys( config( 'consts.ads_status' ) )),
                'layout'        => 'required_if:format,' . INTERSTITIAL,
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

        filterByTimeperiod($ads, $request, 'creative_log');
        
        $allAds = Ads::leftJoin('campaigns', 'campaigns.id', '=', 'ad_creative.camp_id')->select('ad_creative.*');
        if( $this->_user->role != ADMIN_PRIV ){
            $ads->where( 'campaigns.status', '!=', DELETED_CAMP  )
                    ->where( 'ad_creative.status', '!=', DELETED_AD )
                    ->where( 'campaigns.user_id', '=', $this->_user->id );

            $allAds->where('campaigns.user_id', '=', $this->_user->id)
                    ->where( 'ad_creative.status', '!=', DELETED_AD )
                    ->where( 'campaigns.status', '!=', DELETED_CAMP  );
        }

        // to get the ads that within that campaign
        if( $camp_id != null ){
            
            $camp   = Campaign::find($camp_id);
            // To validate the campaign
            if( $camp == null ){
                return redirect('admin')
                            ->with('warning', trans('lang.spam_msg'));
            }

            $ads    = $ads->where('ad_creative.camp_id', '=', $camp_id);
            $title  = trans('admin.ads_of') . $camp->name; 

            $allAds->where('campaigns.id', '=', $camp_id);
        }else{
            if($request->has('user')){
                $ads->where('campaigns.user_id', '=', $request->input('user'));
                $allAds->where('campaigns.user_id', '=', $request->input('user') );
            }
        }

        $chartData  = adaptChartData( clone($ads), 'creative_log', IS_CAMPAIGN );
        $ads        = $ads->groupBy('ad_creative.id')
                            ->orderBy('created_at', 'ASC')
                            ->get();
        $allAds     = $allAds->get();

        $data   = [ 'mTitle', 'title', 'ads', 'camp', 'chartData', 'allAds' ];
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
    public function show ( Request $request, $ads_id ){
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

        filterByTimeperiod($items, $request, 'creative_log');

        if( is_null($ads) ){
            return redirect('admin')
                        ->with('warning', trans('lang.spam'));
        }

        $chartData     = adaptChartData( clone($items), 'creative_log', IS_CAMPAIGN );
        $adsDetails    = $items->groupBy('ad_creative.id')
                            ->first();

        $title  = $ads->name;
        $data = [ 'mTitle', 'title', 'chartData', 'adsDetails', 'ads' ];
        return view( 'admin.ads.show' )
                    ->with( compact( $data ) );
    }

    /**
     * create. To show create ads page
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response.
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function create ( Request $request ){
        $mTitle     = $this->_mTitle;
        $title      = trans( 'admin.add_new_ad' );
        
        // To get the camp_id
        $camp_id    = $request->input('camp');
        $previous   = \URL::previous();

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
                'image_file'    => 'required|image|mimes:png,bmp,jpeg,jpg,gif|image_size:' . $this->_getImageDimensions( $request ),
                'campaign'      => 'required|exists:campaigns,id' 
            ]));

        if( $validator->fails() ){
            return redirect()->back()
                            ->withInput()
                            ->withErrors( $validator )
                            ->with( 'error', trans( 'lang.validate_msg' ) );
        }else{
            $ad = $this->_store( $request );
            return redirect('ads/' . $ad->id)
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
            $ad->leftJoin('campaigns', 'campaigns.id', '=', 'ad_creative.camp_id' )
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
                'image_file'    => 'image|mimes:png,bmp,jpeg,jpg,gif|image_size:' . $this->_getImageDimensions( $request )
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
        $ad->layout        = ($request->format == INTERSTITIAL ) ?  $request->layout : null;
        $ad->click_url     = $request->click_url;
        
        if( $request->hasFile( 'image_file' ) ){
            $ad->image_file    = uploadFile($request->image_file , 'uploads/ad-images/');
        }

        $ad->title         = $request->type == TEXT_AD ?  $request->title : '';
        $ad->description   = $request->type == TEXT_AD ?  $request->description : '';

        if( $request->has('status') ){
            $ad->status = $request->status;
        }

        $ad->save();
        return $ad;
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

    /**
     * _getImageDimensions. To get the right dimensions to be used in image size validation.
     *
     * @param  \Illuminate\Http\Request $request
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _getImageDimensions(Request $request)
    {
        $dimensions = '*';
        switch ($request->input('format')) {
            case INTERSTITIAL:
                # code...
                switch ($request->input('type')) {
                    case IMAGE_AD:
                        # code...
                        $dimensions = "320,480";
                        break;
                    case TEXT_AD:
                        $dimensions = "200,200";
                        break;
                }
                break;
            case BANNER:
                # code...
                switch ($request->input('type')) {
                    case IMAGE_AD:
                        # code...
                        $dimensions = "320,50";
                        break;
                    case TEXT_AD:
                        $dimensions = "48,48";
                        break;
                }
                break;
        }
        return $dimensions;
    }
}
