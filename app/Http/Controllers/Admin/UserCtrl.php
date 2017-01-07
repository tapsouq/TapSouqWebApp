<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth, Validator, DB;

use App\User, App\Models\PlacementLog, App\Models\CreativeLog;

class UserCtrl extends Controller
{
	// _mTitle, the main title for all view relating to user module.
    private $_mTitle;
    protected $_user;

    /**
       * __construct
       *
       * @param void
       * @return void
       * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
       * @copyright Smart Applications Co. <www.smartapps-ye.com>
       */
      public function __construct ( ){
        $this->_user = Auth::user();
      	$this->_mTitle = trans( 'admin.users' );
      }


    /**
      * index
      *
      * @param void
      * @return \Illuminate\Http\Response
      * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
      * @copyright Smart Applications Co. <www.smartapps-ye.com>
      */
     public function index ( Request $request ){
        $mTitle = $this->_mTitle;
        
        // To manage tabs between advertisers and publisher users.
        if( $request->has('adv') ){
            $title  = trans( 'admin.all_users' ) . ' > ' . trans('admin.advertisers');
            $camps  =   User::leftJoin( 'campaigns', 'campaigns.user_id', '=', 'users.id')
                            ->leftJoin( 'ad_creative', 'ad_creative.camp_id', '=', 'campaigns.id' )
                            ->leftJoin('creative_log', 'creative_log.ads_id', '=', 'ad_creative.id')
                            ->select(
                                "users.*",
                                "creative_log.created_at AS time",
                                DB::raw('DATE( `creative_log`.`created_at` ) AS date'),
                                DB::raw('SUM(`creative_log`.`requests`) AS requests '),
                                DB::raw('SUM(`creative_log`.`impressions`) AS impressions '),
                                DB::raw('SUM(`creative_log`.`clicks`) AS clicks '),
                                DB::raw('SUM(`creative_log`.`installed`) AS installed ')
                            )
                           ->where( 'users.role', '=', DEV_PRIV );
            
            // Filter advertisers users's records by the time period
            filterByTimeperiod($camps, $request, 'creative_log');

            // Get the array for the advertisers users's main chart.
            $chartData  = adaptChartData( clone($camps), 'creative_log' );
            
            $tableItems = $camps->groupBy('users.id')
                        ->orderBy('users.created_at', 'ASC')
                        ->get();
        }else{
            $title  = trans( 'admin.all_users' ) . ' > ' . trans('admin.publishers');
            $apps   = User::leftJoin( 'applications', 'applications.user_id', '=', 'users.id' )
                            ->leftJoin( 'ad_placement', 'ad_placement.app_id', '=', 'applications.id' )
                                ->leftJoin( 'placement_log', 'placement_log.ads_id', '=', 'ad_placement.id' )
                                ->leftJoin( 'countries', 'countries.id', '=', 'users.country' )
                                ->select( 
                                    'users.*',
                                    'applications.user_id',
                                    'countries.name as country_name',
                                    'placement_log.created_at as time',
                                    DB::raw('DATE(placement_log.created_at) AS date'),
                                    DB::raw('SUM(`placement_log`.`requests`) AS requests'), 
                                    DB::raw('SUM(`placement_log`.`impressions`) AS impressions'), 
                                    DB::raw('SUM(`placement_log`.`clicks`) AS clicks'),
                                    DB::raw('SUM(`placement_log`.`installed`) AS installed')
                                )
                                ->where( 'users.role', '=', DEV_PRIV );

            // Filter publishers users's records by the time period
            filterByTimeperiod($apps, $request, 'placement_log');
            
            // Get the array for the publishers users's main chart.
            $chartData  = adaptChartData( clone($apps), 'placement_log' );
            
            $tableItems = $apps->groupBy('users.id')
                        ->orderBy('placement_log.created_at', 'ASC')
                        ->get();
        }

        // Get all users except admin users.
        $allUsers = User::where('role', '=', DEV_PRIV)->get();

     	$data =[ 'mTitle', 'title', 'tableItems', 'chartData', 'allUsers' ];
     	return view( 'admin.user.index' )
     				->with( compact( $data ) );
    }

    /**
     * edit. To show edit user page.
     *
     * @param int $user_id
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function edit ( $user_id ){

        $mTitle = $this->_mTitle;
        $title = trans( "admin.edit_user" );
        $user = User::join( 'countries', 'countries.id', '=', 'users.country' )
                    ->select( 'users.*', 'countries.name as country_name' )
                    ->where( 'users.id', '=', $user_id )
                    ->first();
        
        // Redirect to dashboard with spam message if the user isn't exists. To avoid spams.
        if( is_null( $user ) ){
            return redirect('admin')
                    ->with( 'warning', trans( 'lang.spam_msg' ) );
        }

        $data = [ 'mTitle', 'title', 'user' ];
        return view( 'admin.user.edit' )
                    ->with( compact( $data ) );
    }

    /**
     * save. To save edited user
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function save ( Request $request ){
        $validator = Validator::make( $request->all(), [
                'role'          => "required|in:" . implode( ',' , array_keys( config('consts.user_roles') ) ), 
                'status'        => 'required|in:' . implode( ',' , array_keys( config('consts.user_status') ) ), 
                'id'            => 'required|exists:users,id'
            ] );
        if( $validator->fails() ){
            return redirect()->back()
                            ->withInput()
                            ->withErrors( $validator )
                            ->with( 'error', trans( 'lang.validate_msg' ) );
        }else{
            
            // save changed properties.
            $user           = User::find( $request->id );
            $user->status   = $request->status;
            $user->role     = $request->role;
            $user->save();

            return redirect()->back()
                            ->with( 'success', trans( 'lang.compeleted_msg' ) );
        }
    }

    /**
     * editProfile. To show edit profile for the developer.
     *
     * @param void
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function editProfile ( ){
        $mTitle = $this->_mTitle;
        $title = trans( 'admin.profile' );
        $user = $this->_user;

        $data = [ 'mTitle', 'title', 'user' ];
        return view( 'admin.user.profile' )
                    ->with( compact( $data ) );
    }

    /**
     * save. To save the edited profile.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function saveProfile( Request $request ){
        $validator = Validator::make( $request->all(), [
                'fname'     => 'required|max:255',
                'lname'     => "required|max:255",
                'country'   => "required|exists:countries,id",
                'city'      => "required|max:255",
                'address'   => "required|max:255",
                'company'   => "required|max:255",
                "password"  => "confirmed|min:6"
            ]);
        if( $validator->fails() ){
            return redirect()->back()
                            ->withInput()
                            ->withErrors( $validator )
                            ->with( 'error', trans( 'lang.validate_msg' ) );
        }else{

            // save profile properties
            $user           = $this->user;
            $user->fname    = $request->fname;
            $user->lname    = $request->lname;
            $user->country  = $request->country;
            $user->city     = $request->city;
            $user->address  = $request->address;
            $user->company  = $request->company;
            $user->password = $request->has( 'password' ) ? bcrypt( $request->password ) : $user->password; 
            $user->save();
            
            return redirect()->back()
                            ->with( 'success', trans( 'lang.compeleted_msg' ) );
        }       
    }
    
    /**
      * destroy. To deactivate the user.
      *
      * @param \Illuminate\Http\Request $request
      * @return void
      * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
      * @copyright Smart Applications Co. <www.smartapps-ye.com>
      */
     public function destroy ( Request $request ){

        if( $request->has( 'id' ) && $request->has('token') ){
            $user = User::find( $request->id );
            $session_token = session( "_token" );
            if( $request->token == $session_token && $user != null ){
                $user->status = SUSPEND_USER;
                $user->save();
                return redirect()->back()
                                ->with( 'success', trans( 'lang.compeleted_msg' ) );
            }
        }

        // If the token in invalid or the id isn't valid user id. redirect and show the spam message
        return redirect()->back()
                        ->with( 'warning', trans( 'lang.spam_msg' ) );
     } 
}
