<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth, DB, Validator;
use App\Models\Ads, App\Models\Campaign;
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
     * create. To show create ads page
     *
     * @param void
     * @return \Illuminate\Http\Response.
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function create (  ){
        $mTitle = $this->_mTitle;
        $title  = trans( 'admin.add_new_ad' );
        $camps  = $this->_camps;

        $data   = [ 'mTitle', 'title', 'camps' ];
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
        $validator = Validator::make( $request->all(), [ 'image_file' => 'required|image|mimes:png,bmp,jpeg,jpg,gif'] );

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
        $camps  = $this->_camps;
        $ad     = Ads::where( 'ad_creative.id', '=', $ad_id );  
        $ad     = $this->_user->role == ADMIN_PRIV ? $ad : $ad->leftJoin('campaigns', 'campaigns.id', '=', 'ad_creative.camp_id' )->where( 'campaigns.user_id', '=', $this->_user->id );
        $ad     = $ad->select('ad_creative.*')->first();

        if( is_null($ad) ){
            return redirect()->back()
                            ->with( 'warning', trans('lang.spam_msg') );
        }

        $data = [ 'mTitle', 'title', 'camps', 'ad' ];
        return view( 'admin.ads.create' )
                    ->with( compact( $data ) );
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
        $ad->image_file    = uploadFile($request->image_file , 'public/uploads/ad-images/');

        $ad->title         = $request->type == TEXT_AD ?  $request->title : '';
        $ad->description   = $request->type == TEXT_AD ?  $request->description : '';

        if( $request->has('status') ){
            $ad->status = $request->status;
        }

        $ad->save();
    }
}
