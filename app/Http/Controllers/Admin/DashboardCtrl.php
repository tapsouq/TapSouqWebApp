<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use App\Models\CreativeLog, App\Models\PLacementLog;

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
     public function index ( Request $request){
     	$mTitle = $this->_mTitle;

     	if( $request->has('camps') ){
	     	$title 	= 	trans( "admin.all_camps_7days" );
	     	$items  =   CreativeLog::join( 'ad_creative', 'ad_creative.id', '=', 'creative_log.ads_id' )
			     	                ->join( 'campaigns', 'campaigns.id', '=', 'ad_creative.camp_id')
			     	                ->join('users', 'users.id', '=', 'campaigns.user_id')
			     	                ->select(
			     	                    "campaigns.user_id",
			     	                    "creative_log.created_at AS time",
			     	                    DB::raw('SUM(`users`.`credit`)')
			     	                    DB::raw('DATE( `creative_log`.`created_at` ) AS date'),
			     	                    DB::raw('SUM(`creative_log`.`requests`) AS requests '),
			     	                    DB::raw('SUM(`creative_log`.`impressions`) AS impressions '),
			     	                    DB::raw('SUM(`creative_log`.`clicks`) AS clicks '),
			     	                    DB::raw('( SUM(`users`.`credit`) - SUM(`users`.`debit`)) AS credit '),
			     	                    DB::raw('SUM(`creative_log`.`installed`) AS installed ')
			     	                )
	     	               			->where( 'users.role', '=', DEV_PRIV )
	     	               			->where( 'creative_log.created_at', '<=', date('Y-m-d') . " 23:59:59")
	     	               			->where( 'creative_log.created_at', '>=', date_create()->sub(date_interval_create_from_date_string('7 days'))->format("Y-m-d 00:00:00") );
     		
 		    $items      = filterByTimeperiod($items, $request, 'creative_log');
     		
     		$cloneItems = clone($items);
     		$total		= $cloneItems->first();
     		$chartData 	= adaptChartData ( $items, 'creative_log', IS_CAMPAIGN, IN_DASHBOARD );
     		
     	}else{
	     	$title 	= 	trans( "admin.all_apps_7days" );
	     	$items  =   PlacementLog::join( 'ad_placement', 'ad_placement.id', '=', 'placement_log.ads_id' )
			     	                ->join( 'applications', 'applications.id', '=', 'ad_placement.app_id')
			     	                ->join('users', 'users.id', '=', 'applications.user_id')
			     	                ->select(
			     	                    "applications.user_id",
			     	                    "placement_log.created_at AS time",
			     	                    DB::raw('DATE( `placement_log`.`created_at` ) AS date'),
			     	                    DB::raw('SUM(`placement_log`.`requests`) AS requests '),
			     	                    DB::raw('SUM(`placement_log`.`impressions`) AS impressions '),
			     	                    DB::raw('SUM(`placement_log`.`clicks`) AS clicks '),
			     	                    DB::raw('SUM(`placement_log`.`clicks`) AS credit '),
			     	                    DB::raw('SUM(`placement_log`.`installed`) AS installed ')
			     	                )
	     	               			->where( 'users.role', '=', DEV_PRIV )
	     	               			->where( 'placement_log.created_at', '<=', date('Y-m-d') . " 23:59:59")
	     	               			->where( 'placement_log.created_at', '>=', date_create()->sub(date_interval_create_from_date_string('7 days'))->format("Y-m-d 00:00:00") );
     		
 		    $items      = filterByTimeperiod($items, $request, 'placement_log');
     		
     		$cloneItems = clone($items);
     		$total		= $cloneItems->first();
     		$chartData 	= adaptChartData ( $items, 'placement_log', NOT_CAMPAIGN, IN_DASHBOARD);
     	}

     	$data = [ 'mTitle', 'title', 'chartData', 'total' ];
     	return view( 'admin.dashboard.index' )
     				->with( compact( $data ) );
     }
}
