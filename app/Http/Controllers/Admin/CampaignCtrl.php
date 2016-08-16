<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Country, App\Models\Category, App\Models\Campaign;
use Validator, DB, Auth;

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

    /**
     * __construct. To init the class
     *
     * @param void
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function __construct ( ){
        $this->_mTitle  = trans( 'admin.campaigns' );
        $this->_user    = Auth::user();
        $this->_categories = Category::all();
        $this->_countries = Country::all();
        $this->_initRules = [
                'name'              => 'required|max:255',
                'category'          => 'array|exists:categories,id',
                'target_platform'   => 'required|in:' . implode(',' , array_keys( config( 'consts.app_platforms' ) )),
                'ad_serving_pace'   => 'in:' . implode(',' , array_keys( config( 'consts.camp_serving' ) )),
                'country'           => 'array|exists:countries,id',
                'status'            => 'in:' . implode(',' , array_keys( config( 'consts.camp_status' ) )),
                'start_date'        => 'required|date_format:m/d/Y g:i A',
                'end_date'          => 'required|date_format:m/d/Y g:i A',
            ];
    }
    
    /**
     * index. To show all campaigns page
     *
     * @param void
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function index (  ){
        $mTitle = $this->_mTitle;
        $title  = trans( 'admin.all_campaigns' );
        $camps  = Campaign::leftJoin( 'users', 'users.id', '=', 'campaigns.user_id' );

        if( $this->_user->role != ADMIN_PRIV ){
            $camps = $camps->where( 'campaigns.user_id', '=', $this->_user->id );
        }
        $camps  = $camps->select( 'campaigns.*', 'users.fname', 'users.lname' )
                        ->get();

        $data = [ 'mTitle', 'title', 'camps' ];
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

        $data = [ 'mTitle', 'title', 'countries', 'categories' ];
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
        $validator = Validator::make( $request->all(), $this->_initRules );
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
     * edit. To show the edit campaign page.
     *
     * @param int $camp_id
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function edit ( $camp_id ){
        $mTitle = $this->_mTitle;
        $title  = trans( 'admin.edit_campaign' );
        $categories = $this->_categories;
        $countries  = $this->_countries;

        $camp   = Campaign::where( 'campaigns.id', '=', $camp_id );
        $camp   = $this->_user->role == ADMIN_PRIV ? $camp : $camp->where( 'campaigns.user_id', '=', $this->_user->id ) ; 
        $camp   = $camp->first();
        if( is_null( $camp ) ){
            return redirect()->back()
                            ->with( 'warning', trans('lang.spam_msg') );
        }

        $data = [ 'mTitle', 'title', 'camp', 'categories', 'countries' ];
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

        $rules      = array_merge( $this->_initRules, [ 'id' => 'required|exists:campaigns,id' ] );    
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
      * changeStatus. To change the campaign status.
      *
      * @param \Illuminate\Http\Request $request
      * @return void
      * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
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

        $camp->start_date       = date_create_from_format( 'm/d/Y g:i A', $request->start_date )->format( 'Y-m-d H:i:s' );
        $camp->end_date         = date_create_from_format( 'm/d/Y g:i A', $request->end_date )->format( 'Y-m-d H:i:s' );
        $camp->target_platform  = $request->target_platform;
        $camp->ad_serving_pace  = $request->input( 'ad_serving_pace' );

        if( $request->has('status') ){
            $camp->status = $request->status;
        }

        $camp->save();

        if( $request->has('category') ){
            // To make sure that two categories only be inserted
            $categories = array_slice($request->category, 0, 2);
            syncPivot( 'campaign_categories', 'camp_id', $camp->id, 'cat_id', $categories );
        }

        if( $request->has( 'country' ) ){
            $countries = $request->country;
            syncPivot( 'campaign_countries', 'camp_id', $camp->id, 'country_id', $countries );
        }

    }
}
