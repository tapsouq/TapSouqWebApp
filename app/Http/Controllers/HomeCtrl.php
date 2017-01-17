<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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
     * @param  param
     * @return return
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

 
}
