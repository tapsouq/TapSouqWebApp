<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use Mail;
use \Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'fname'     => 'required|max:255',
            'lname'     => "required|max:255",
            'email'     => 'required|email|max:255|unique:users',
            "company"   => 'required|max:255',
            'password'  => 'required|confirmed|min:6',
            'country'   => 'required|exists:countries,id',
            'city'      => "required|max:255",
            'address'   => "required|max:255",
            'agree'     => "required"
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = new User;

        $user->fname     = $data['fname'];
        $user->lname     = $data['lname'];
        $user->email     = $data['email'];
        $user->company   = $data['company'];
        $user->password  = bcrypt($data['password']);
        $user->country   = $data['country'];
        $user->city      = $data['city'];
        $user->address   = $data['address'];
        $user->remember_token  = getToken();

        if( $this->_sendVerifyMail( $user ) ){
            $user->save();
            return $user;
        }
    }

    /**
     * redirectPath
     *
     * @param void
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function redirectPath (  ){
        return '/admin';
    }
        
    /**
     * _sendVerifyMail. To send emailto the user to verify the mail
     *
     * @param App\User $user. 
     * @return boolean.
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _sendVerifyMail ( $user ){
        //$site_info = 
        return Mail::send('admin.emails.verify', ['user' => $user ], function ($m) use ( $user ) {
           $m->from( getSiteInfo()->site_email , getSiteInfo()->site_title );

           $m->to( $user->email , $user->fname . " " . $user->lname )->subject( trans( 'admin.please_verfiy_your_email' ) );
       });
    }

    /**
     * verifyEmail. To verify the user email.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function verifyEmail ( Request $request ){
        
        $msg = trans( 'admin.error_at_verification' );
        
        if( $request->has( 'token' ) && $request->has( 'email' ) ){
            $user = User::where( 'email', '=', $request->email )
                        ->where( 'remember_token', '=', $request->token )
                        ->first();
            if( $user ){
                $user->status = AC;
                $user->save();
                $msg = trans( "admin.success_verification" );
            }
        }

        return view( 'auth.verify-email' )
                    ->with( 'msg', $msg );
    }
}
