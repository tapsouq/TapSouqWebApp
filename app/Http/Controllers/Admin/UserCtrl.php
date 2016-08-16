<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth, Validator;
use App\User;
class UserCtrl extends Controller
{
	private $_mTitle;
    protected $user;

    /**
       * __construct
       *
       * @param void
       * @return void
       * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
       * @copyright Smart Applications Co. <www.smartapps-ye.com>
       */
      public function __construct ( ){
        $this->user = Auth::user();
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
     public function index (  ){
        $mTitle = $this->_mTitle;
     	$title 	= trans( 'admin.all_users' );
     	$users  = User::leftJoin( 'countries', 'countries.id', '=', 'users.country' )
                        ->where( 'role', '=', DEV_PRIV )
                        ->select( 'users.*', 'countries.name as country_name' )
                        ->get();

     	$data =[ 'mTitle', 'title', 'users' ];
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
        
        if( is_null( $user ) ){
            return redirect()->back()
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
        $user = $this->user;

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
        return redirect()->back()
                        ->with( 'warning', trans( 'lang.spam_msg' ) );
     } 
}
