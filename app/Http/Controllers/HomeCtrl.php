<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Validator, Mail;

class HomeCtrl extends Controller
{
    // the main Title of all pages controlled by this controller
    protected $_mTitle;
    
    /**
     * __construct. To init the class
     *
     * @param void
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function __construct (){
        $this->_mTitle  = trans( 'home.tapsouq' );
    }

    /**
     * Display a the home page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mTitle = $this->_mTitle;
        $title  = trans( 'home.home_page' );
        
        $data = [ 'mTitle', 'title' ];
        return view( 'home.index' )
                    ->with( compact( $data ) );
    }

    /**
     * showTermOfService. Show Term Of Service page.
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function showTermOfService()
    {
        $mTitle = $this->_mTitle;
        $title  = trans("admin.terms");
        $data = ['mTitle', 'title'];
        return view('home.terms')
                    ->with(compact($data));  
    }

    /**
     * showResourcesPage . Show Term Of Service page.
     *
     * @param  void
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function showResourcesPage()
    {
        $mTitle = $this->_mTitle;
        $title  = trans("admin.resources");
        $data = ['mTitle', 'title'];
        return view('home.resources')
                    ->with(compact($data));  
    }

    /**
     * showContactUs. To show contact us page.
     *
     * @param  void
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function showContactUs()
    {
        $mTitle = $this->_mTitle;
        $title  = trans('admin.contact_us');
        $data   = [ 'mTitle', 'title' ];

        return view('home.contact-us')
                    ->with(compact($data));
    }

    /**
     * saveContactUs. To save contact us post request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function saveContactUs( Request $request )
    {
        $validator = Validator::make($request->all(), [
                'name'      => 'required',
                'title'     => 'required',
                'email'     => 'required|email',
                'subject'   => 'required'
            ]);

        if( $validator->fails() ){
            return redirect()->back()
                        ->withInput()
                        ->withErrors( $validator )
                        ->with( 'error', trans( 'lang.validate_msg' ) );
        }else{
            if( $this->_sendContactUsEmail($request) ){
                $status = 'success';
                $msg = trans( 'lang.sucess_send_contactus' );
            }else{
                $status = 'error';
                $msg = trans( 'lang.there_are_such_error' );
            }

            return redirect()->back()
                        ->with($status, $msg );
        }
    }

    /**
     * _sendContactUsEmail. To send the mail to the contct-us@tapsouq.com mail.
     *
     * @param  \Illuminate\Http\Request $request
     * @return boolean
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _sendContactUsEmail(Request $request)
    {
        return Mail::send('emails.contact-us', ['request' => $request ], function ($mail) use ( $request ) {
                   $mail->from( $request->email , $request->name );

                   $mail->to( getSiteInfo()->contactUsEmail , getSiteInfo()->site_title )
                        ->subject( $request->title );
               });
    }
}
