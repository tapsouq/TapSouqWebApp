<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Authenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( $this->auth->guest()  || ( $this->auth->user() && $this->auth->user()->status == SUSPEND_USER ) ) {
            
            // log out if the user is suspended 
            $this->auth->logout();
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('auth/login');
            }
        }

        if( $this->auth->user() ){
            if( $this->auth->user()->status == PENDING_USER ){

                // log out because the user is pending
                $this->auth->logout();
                if ($request->ajax()) {
                    return response('Unauthorized.', 401);
                } else {
                    return redirect()->guest('auth/login')->with( 'danger', trans( 'lang.verify_your_email' ) );
                }   
            }
        }

        return $next($request);
    }
}
