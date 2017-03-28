<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Country, App\Models\Category, App\Models\Campaign;
use App\Models\Ads, App\Models\CreativeLog;
use App\Models\Keyword, App\Models\SimilarCategory;
use Validator, DB, Auth, App\User;

use Raulr\GooglePlayScraper\Scraper;

class CampaignCtrl extends Controller
{
    // the main Title of all pages controlled by this controller
    protected $_mTitle;
    // authenticated user
    private $_user;
    // all countries
    private $_countries;
    //all categories
    private $_categories;
    // all Keywords
    private $_keywords;

    /**
     * __construct. To init the class
     *
     * @param void
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function __construct ( ){
        $this->_mTitle      = trans( 'admin.campaigns' );
        $this->_user        = Auth::user();
        $this->_categories  = Category::all();
        $this->_countries   = Country::all();
        $this->_keywords    = Keyword::all();
        $this->_initRules   = [
                'name'              => 'required|max:255',
                'fcategory'         => 'exists:categories,id',
                'target_platform'   => 'required|in:' . implode(',' , array_keys( config( 'consts.app_platforms' ) )),
                'ad_serving_pace'   => 'in:' . implode(',' , array_keys( config( 'consts.camp_serving' ) )),
                'country'           => 'array|exists:countries,id',
                'status'            => 'in:' . implode(',' , array_keys( config( 'consts.camp_status' ) )),
                'start_date'        => 'required|date_format:m/d/Y g:i A',
                'end_date'          => 'required|date_format:m/d/Y g:i A',
                'keyword'           => 'array|exists:keywords,id',
                'imp_per_day'       => 'required_with:imp_per_day_checkbox|integer|min:1'
            ];
    }
    
    /**
     * index. To show all campaigns page
     *
     * @param int $user_id
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function index ( Request $request, $user_id = null ){

        $mTitle = $this->_mTitle;
        $title  = trans( 'admin.all_campaigns' );
        
        $adsCount = Ads::leftJoin('campaigns', 'campaigns.id', '=', 'ad_creative.camp_id');

        $camps  = Campaign::leftJoin( 'ad_creative', 'ad_creative.camp_id', '=', 'campaigns.id' )
                               ->leftJoin( 'creative_log', 'creative_log.ads_id', '=', 'ad_creative.id' )
                               ->join( 'users', 'users.id', '=', 'campaigns.user_id' )
                               ->select(
                                    "campaigns.*",
                                    "creative_log.created_at AS time",
                                    DB::raw('DATE( `creative_log`.`created_at` ) AS date'),
                                    DB::raw('SUM(`creative_log`.`requests`) AS requests '),
                                    DB::raw('SUM(`creative_log`.`impressions`) AS impressions '),
                                    DB::raw('SUM(`creative_log`.`clicks`) AS clicks '),
                                    DB::raw('SUM(`creative_log`.`installed`) AS installed ')
                                );

        filterByTimeperiod($camps, $request, 'creative_log');

        if( $this->_user->role != ADMIN_PRIV ){

            $allCamps = Campaign::where('campaigns.user_id', '=', $this->_user->id)
                                    ->where('campaigns.status', '!=', DELETED_CAMP);

            $camps->where( 'campaigns.user_id', '=', $this->_user->id )
                    ->where( 'campaigns.status', '!=', DELETED_CAMP )
                    ->where( function ($query){
                        $query->whereNull('ad_creative.status')
                            ->orWhere('ad_creative.status', '!=', DELETED_AD);
                    });

            $adsCount->where('campaigns.user_id', '=', $this->_user->id )
                        ->where('campaigns.status', '!=', DELETED_CAMP)
                        ->where('ad_creative.status', '!=', DELETED_AD);

        } else if( $user_id != null ){ // If the user is admin and get all campaigns for the user has id ($user_id).

            $user       = User::find($user_id);
            // To validate the user
            if( $user == null ){
                return redirect('admin')
                            ->with('warning', trans('lang.spam_msg'));
            }
            
            $title      = $title . trans("admin.belongs_to") . "{$user->fname} {$user->lname}"; 
            $camps      = $camps->where('campaigns.user_id', '=', $user_id);
            $adsCount   = $adsCount->where('campaigns.user_id', '=', $user_id);
            $allCamps   = Campaign::where('campaigns.user_id', '=', $user_id);
        }else{
            $allCamps = new Campaign;
        }
        
        $chartData = adaptChartData( clone($camps), 'creative_log', IS_CAMPAIGN);
        
        $allCamps = $allCamps->get();
        $camps  = $camps->groupBy('campaigns.id')
                        ->orderBy('campaigns.created_at', 'ASC')
                        ->get();

        $adsCount = $adsCount->count();

        $data = [ 'mTitle', 'title', 'camps', 'adsCount', 'chartData', 'user_id', 'allCamps' ];
        return view( 'admin.campaign.index' )
                    ->with( compact( $data ) );
    }
    /**
     * create. To show create campign page.
     *
     * @param void
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function create ( ){
        $mTitle     = $this->_mTitle;
        $title      = trans( 'admin.add_new_campaign' );
        $countries  = $this->_countries;
        $categories = $this->_categories;
        $keywords   = $this->_keywords;

        $data = [ 'mTitle', 'title', 'countries', 'categories', 'keywords' ];
        return view( 'admin.campaign.create' )
                    ->with( compact( $data ) );
    }

    /**
     * store. To store created campaign
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function store ( Request $request ){
        $validator = Validator::make( $request->all(), array_merge($this->_initRules, [
                    'scategory'     => 'exists:categories,id|not_in:' . $request->input('fcategory'),
                ]));
        if( $validator->fails() ){
            return redirect()->back()
                            ->withInput()
                            ->withErrors( $validator )
                            ->with( 'error', trans( 'lang.validate_msg' ) );
        }else{
            $camp = $this->_store( $request );
            return redirect('ads/all/' . $camp->id )
                            ->with( 'success', trans( 'admin.created_camp_msg' ) );
        }
    }

    /**
     * edit. To show the edit campaign page.
     *
     * @param int $camp_id
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function edit ( $camp_id ){
        $mTitle     = $this->_mTitle;
        $title      = trans( 'admin.edit_campaign' );
        $categories = $this->_categories;
        $countries  = $this->_countries;
        $keywords   = $this->_keywords;

        $selectedKeys       = DB::table( 'campaign_keywords' )
                                ->leftJoin( 'keywords', 'keywords.id', '=', 'campaign_keywords.keyword_id' )
                                ->where( 'campaign_keywords.camp_id', '=', $camp_id )
                                ->lists('keyword_id');

        $camp   = Campaign::where( 'campaigns.id', '=', $camp_id );
        $camp   = $this->_user->role == ADMIN_PRIV ? $camp : $camp->where( 'campaigns.user_id', '=', $this->_user->id ) ; 
        $camp   = $camp->first();
        if( is_null( $camp ) ){
            return redirect()->back()
                            ->with( 'warning', trans('lang.spam_msg') );
        }

        $data = [ 'mTitle', 'title', 'camp', 'categories', 'countries', 'keywords', 'selected_countries', 'selectedKeys' ];
        return view( 'admin.campaign.create' )
                    ->with( compact( $data ) );
    }

    /**
     * save. To save edited campaign
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function save ( Request $request ){
        $rules      = array_merge( $this->_initRules, [ 
                'id'            => 'required|exists:campaigns,id',
                'scategory'     => 'exists:categories,id|not_in:' . $request->input('fcategory') 
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
                            ->with( 'success', trans( 'admin.updated_camp_msg' ) );
        }
    }

    /**
      * changeStatus. To change the campaign status.
      *
      * @param \Illuminate\Http\Request $request
      * @return void
      * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com
      * @copyright Smart Applications Co. <www.smartapps-ye.com>
      */
    public function changeStatus ( Request $request ){
        if( $request->has( 'id' ) && $request->has('token') && $request->has('s') ){
            $camp = Campaign::find( $request->id );
            $session_token = session( "_token" );

            $campStates = array_keys( config('consts.camp_status') );
            // Token Validation. and assure that campaign id exists in campaigns DB table 
            if( $request->token == $session_token && $camp != null && in_array( $request->input('s'), $campStates ) ){
                // To prevent users to delete another users's campagind ( than more step )
                if( $this->_user->role != ADMIN_PRIV && $camp->user_id != $this->_user->id ){ 
                    return redirect()->back()
                                    ->with( 'warning', trans( 'lang.spam_msg' ) );
                }

                $camp->status = $request->input('s');
                if( $camp->status == DELETED_CAMP ){
                    $camp->deleted_at = date( 'Y-m-d H:i:s' );

                    // delete all ads to these campaign
                    Ads::where( 'camp_id', '=', $camp->id )
                        ->update( [ 'status' => DELETED_AD ] );
                }
                $camp->save();

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
     * _store. To store created campaigns into DB.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _store ( Request $request ){

        if( $request->has('id' ) ){
            $camp = Campaign::find( $request->id );
        }else{
            $camp           = new Campaign;
            $camp->user_id  = $this->_user->id;
        }

        $camp->name             = $request->name;
        $camp->description      = $request->input('description');
        $camp->imp_per_day      = $request->has('imp_per_day_checkbox') ? $request->imp_per_day : null;

        $camp->start_date       = date_create_from_format( 'm/d/Y g:i A', $request->start_date )->format( 'Y-m-d H:i:s' );
        $camp->end_date         = date_create_from_format( 'm/d/Y g:i A', $request->end_date )->format( 'Y-m-d H:i:s' );
        $camp->target_platform  = $request->target_platform;
        $camp->ad_serving_pace  = $request->input( 'ad_serving_pace' );

        $camp->fcategory        = $request->has('fcategory') ? $request->fcategory : NULL;
        $camp->scategory        = $request->has('scategory') ? $request->scategory : NULL;
        $camp->simi_cats         = SimilarCategory::getSimiCats($camp->fcategory, $camp->scategory);

        $camp->countries        = $request->has('country') ? implode(',', $request->country) : null;

        $timeDiff = strtotime($request->end_date) - time();
        
        if( $timeDiff > 0 ){
            $camp->status = RUNNING_CAMP;
        }else{
            $camp->status = COMPLETED_CAMP;
        }

        if( $request->has('status') ){
            if( $request->status == DELETED_CAMP ){
                // delete all ads to these campaign
                Ads::where( 'camp_id', '=', $camp->id )
                    ->update( [ 'status' => DELETED_AD ] );
            }
            $camp->status = $request->status;
        }

        $camp->save();

        // save application keywords
        $this->_saveCampKeywords( $request, $camp->id );

        return $camp;
    }

    /**
     * _saveCampKeywords. To save campaign keywords
     *
     * @param \Illuminate\Http\Request $request
     * @param int $camp_id
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _saveCampKeywords ( Request $request, $camp_id ){

        $keywords = [];
        $new_keywords = [];
        
        // To save selected keywords that match this application.
        if( $request->has( 'keyword' ) ){
            $keywords = $request->keyword;
        }
        
        // To save new keywords and link it with this application
        if( $request->has('new_keywords') ){
            foreach( $request->new_keywords as $value  ){
                $keyword = new Keyword;
                $keyword->name = $value;
                $keyword->save();
                $new_keywords[] = $keyword->id;
            }
        }
        $keywords = array_merge($keywords, $new_keywords);
        syncPivot( 'campaign_keywords', 'camp_id', $camp_id, 'keyword_id', $keywords );
    }
}
