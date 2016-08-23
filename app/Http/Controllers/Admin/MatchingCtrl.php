<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth, DB, Validator;
class MatchingCtrl extends Controller
{
    // the main Title of all pages controlled by this controller
    protected $_mTitle;
    // authenticated user
    private $_user;
    //init rules for change priority and delete matching
    private $_initRules;
    /**
     * __construct. To init the class
     *
     * @param void
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function __construct ( ){
        $this->_mTitle  = trans( 'admin.matching' );
        $this->_user    = Auth::user();
        $this->_initRules = [
                'app_id'        => 'required|exists:applications,id',
                'keyword_id'    => 'required|exists:keywords,id',
                'token'         => 'required|in:' . session('_token'),
            ];
    }    

    /**
     * showMatched. To show matching keywords page
     *
     * @param void
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function showMatched (  ){
        $mTitle     = $this->_mTitle;
        $title      = trans( 'admin.matched_keywords' );
        $keywords   = DB::table('application_keywords')
                          ->leftJoin('app_details', 'app_details.id', '=', 'application_keywords.app_id')
                          ->leftJoin('keywords', 'keywords.id', '=', 'application_keywords.keyword_id')
                          ->select('application_keywords.*', 'app_details.*', 'keywords.name as keyword')
                          ->paginate(3);

        $data = [ 'mTitle', 'title', 'keywords' ];
        return view( 'admin.matching.matched' )
                    ->with( compact( $data ) );
    }

    /**
     * showEmpty. To show empty matching keywords
     *
     * @param void
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function showEmpty (  ){
        $mTitle     = $this->_mTitle;
        $title      = trans( 'admin.empty_keywords' );
        $keywords   = DB::table('keywords')
                          ->whereNotIn('keywords.id', DB::table('application_keywords')->lists('keyword_id') )
                          ->paginate(3);
                          
        $data = [ 'mTitle', 'title', 'keywords' ];
        return view( 'admin.matching.empty' )
                    ->with( compact( $data ) );
    }
    /**
     * changePriority. To chnage the priority of the matching
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function changePriority ( Request $request ){
        $changeRules = [
                's'             => 'required|in:p,m', // plus p Or minus m
                'priority'      => 'required|integer|min:1'
            ];
        $validator = Validator::make($request->all(), array_merge( $this->_initRules, $changeRules ));
        if( $validator->fails() ){
            dd( $validator->errors() );
            return redirect()->back()
                            ->withInput()
                            ->withErrors( $validator )
                            ->with( 'warning', trans( 'lang.spam_msg' ) );
        }else{
            // Increment or Decrement the matching priority
            $priority = $request->input( 'priority' );
            // check if priority more than zero
            $priority = $request->input('s') == 'p' ? $priority + 1 : ( $priority > 1 ? $priority - 1 : $priority ) ;
            DB::table('application_keywords')
                ->where('app_id', '=', $request->app_id )
                ->where('keyword_id', '=', $request->keyword_id )
                ->update( ['priority' => $priority] );

            return redirect()->back()
                            ->with( 'success', trans( 'lang.compeleted_msg' ) );
        }
    }

    /**
     * deleteMatching. To delete the matching keyword with application.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function deleteMatching ( Request $request ){
        $validator = Validator::make($request->all(), $this->_initRules);
        if( $validator->fails() ){
            return redirect()->back()
                            ->withInput()
                            ->withErrors( $validator )
                            ->with( 'warning', trans( 'lang.spam_msg' ) );
        }else{
            // Delete the matching
            DB::table('application_keywords')
                ->where('app_id', '=', $request->app_id )
                ->where('keyword_id', '=', $request->keyword_id )
                ->delete( );

            return redirect()->back()
                            ->with( 'success', trans( 'lang.compeleted_msg' ) );
        }   
    }
}
