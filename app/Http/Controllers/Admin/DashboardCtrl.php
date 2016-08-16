<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class DashboardCtrl extends Controller
{
	// Main title for all views for that controller
	private $_mTitle ;

	/**
	 * __construct
	 *
	 * @param void
	 * @return void
	 * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
	 * @copyright Sapps Company <>
	 */
	public function __construct (  ){
		$this->_mTitle = trans( 'admin.dashboard' );
	}

     /**
      * index
      *
      * @param void
      * @return \Illuminate\Http\Response
      * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
      * @copyright Sapps Company <>
      */
     public function index ( ){
     	$mTitle = $this->_mTitle;
     	$title = trans( "admin.dashboard" );

     	$data = [ 'mTitle', 'title' ];
     	return view( 'admin.dashboard.index' )
     				->with( compact( $data ) );
     }
}
