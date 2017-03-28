<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB, Auth, App\Models\DailyLog;
use App\Models\CreativeLog, App\Models\PlacementLog;

class DashboardCtrl extends Controller
{
	// Main title for all views for that controller
	private $_mTitle ;
	private $_user;
	/**
	 * __construct
	 *
	 * @param void
	 * @return void
	 * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
	 * @copyright Sapps Company <>
	 */
	public function __construct (  ){
		$this->_mTitle 	= trans( 'admin.dashboard' );
		$this->_user   	= Auth::user();
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

     	// To get the array for the credit charts.
     	$creditCharts = $this->_adaptCreditLog($request);

     	// To mange tabs between campaigns and applications for the same user.
     	if( $request->has('camps') ){
	     	$title 	= 	trans( "admin.all_camps_7days" );
	     	$items  =   CreativeLog::join( 'ad_creative', 'ad_creative.id', '=', 'creative_log.ads_id' )
			     	                ->join( 'campaigns', 'campaigns.id', '=', 'ad_creative.camp_id')
			     	                ->join('users as camp_users', 'camp_users.id', '=', 'campaigns.user_id')
			     	                ->select(
			     	                    "campaigns.user_id",
			     	                    "creative_log.created_at AS time",
			     	                    DB::raw('DATE( `creative_log`.`created_at` ) AS date'),
			     	                    DB::raw('SUM(`creative_log`.`requests`) AS requests '),
			     	                    DB::raw('SUM(`creative_log`.`impressions`) AS impressions '),
			     	                    DB::raw('SUM(`creative_log`.`clicks`) AS clicks '),
			     	                    DB::raw('SUM(`creative_log`.`installed`) AS installed '),
			     	                    DB::raw('SUM(`creative_log`.`credits` ) AS credit')
			     	                );
	     	
	     	// To get only the user's campaigns if his role isn't admin
	     	if( $this->_user->role != ADMIN_PRIV ){
	     		$items->where('camp_users.id', '=', $this->_user->id);
	     	}

	     	// to filter the campaigns using the time period filter
 		    filterByTimeperiod($items, $request, 'creative_log');

     		$cloneItems = clone($items);
     		// total records to be shown in the visual user records in the dashboard page.
     		$total		= $cloneItems->first();

     		// get the array of the campaign's main chart.
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
			     	                    DB::raw('SUM(`placement_log`.`credits`) AS credit '),
			     	                    DB::raw('SUM(`placement_log`.`installed`) AS installed ')
			     	                );
			// To get only the user's applications if his role isn't admin.
			if( $this->_user->role != ADMIN_PRIV ){
	     		$items->where('applications.user_id', '=', $this->_user->id);
	     	}

	     	// Filter the applications with the time period. 
 		    filterByTimeperiod($items, $request, 'placement_log');

     		$cloneItems = clone($items);

     		// Get the total records to be shown in the visual records in the dashboard page.
     		$total		= $cloneItems->first();

     		// Get the array for the application's main chart.
     		$chartData 	= adaptChartData ( $items, 'placement_log', NOT_CAMPAIGN, IN_DASHBOARD);
     	}

     	$data = [ 'mTitle', 'title', 'chartData', 'total', 'creditCharts' ];
     	return view( 'admin.dashboard.index' )
     				->with( compact( $data ) );
    }

    /**
     * _addAdminCreditToCharts. To add admin credits to chartdata.
     * Don't be used now. Was used before. May be neede later.
     *
     * @param array $chartData
     * @param \Illuminate\Http\Request $request.
     * @return array.
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _addAdminCreditToCharts ( $chartData, $request ){
    	
		$adminData  = DB::table('sdk_requests')
							->join('sdk_actions', 'sdk_actions.request_id', '=', 'sdk_requests.id')
							->join('ad_placement', 'ad_placement.id', '=', 'sdk_requests.placement_id')
							->join('applications', 'applications.id', '=', 'ad_placement.app_id')
							->join('users as app_users', 'app_users.id', '=', 'applications.user_id')
							->join('ad_creative', 'sdk_requests.creative_id', '=', 'ad_creative.id')
							->join('campaigns', 'campaigns.id', '=', 'ad_creative.camp_id')
							->join('users as camp_users', 'camp_users.id', '=', 'campaigns.user_id')
							->select(
								DB::raw('count(*) as credit'),
								DB::raw('date(sdk_requests.created_at) as date')
							)
							->where('camp_users.role', '=', ADMIN_PRIV)
							->where('sdk_actions.action', '=', CLICK_ACTION)
							->where( 'sdk_requests.created_at', '<=', date('Y-m-d') . " 23:59:59")
							->where( 'sdk_requests.created_at', '>=', date_create()->sub(date_interval_create_from_date_string('7 days'))->format("Y-m-d 00:00:00") );
		
		if( $this->_user->role != ADMIN_PRIV ){
			$adminData->where('app_users.id', '=', $this->_user->id);
		}
		if( $request->has('from') && $request->has('to') ){
                $from       = $request->input("from");
                $to         = $request->input("to");
                $adminData->whereDate("sdk_requests.created_at", ">=", $from)
                          ->whereDate("sdk_requests.created_at", "<=", $to);
        }
    	
    	$adminData 	= $adminData->groupBy('date')
                        		->orderBy( "sdk_requests.created_at", 'ASC')
                        		->get();
        if(sizeof($chartData) > 0){
	        foreach ($chartData['credit'] as $key => $chartCredit) {
	        	if(!isset($adminData[$key])){
		        	$chartData['adminCredit'][$key] = [$chartCredit[0], 0];
	        		continue;
	        	}
	        	$adminCreditDay = $adminData[$key];
	        	if( strtotime($adminCreditDay->date) * 1000 == $chartCredit[0] ){
		        	$chartData['credit'][$key] = [ $chartCredit[0], (int)( $chartCredit[1] - $adminCreditDay->credit) ];
		        	$chartData['adminCredit'][$key] = [$chartCredit[0], (int)$adminCreditDay->credit];
	        	}
	        }
        }
        return $chartData;
    }

    /**
     * _adaptCreditLog. To adapt the data to be shown in credit charts.
     *
     * @param Illuminate\Http\Request $request
     * @return array
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _adaptCreditLog ( $request ){

    	$array = [];
    	$creditLog 	= DailyLog::where('user_id', '=', $this->_user->id);

    	// Check if the time periods range is selected.
    	if( $request->has('from') && $request->has('to') ){
    		$from       = $request->input("from");
    		$to         = $request->input("to");
    		$creditLog->whereDate("date", ">=", $from)
    		        	->whereDate("date", "<=", $to);
    	}else{
    		// the default time period range is 7 days before.
    		$creditLog->where('date', '<=', date('Y-m-d') . ' 23:59:59')
    					->where('date', '>=', date_create()->sub(date_interval_create_from_date_string('6 days'))->format("Y-m-d 00:00:00"));
    	}
    	$items = $creditLog->select(DB::raw('date, credit, gained_credit, spent_credit') )
    						->orderBy('date')->get();

    	// Init the array for the credit chart.
        foreach ($items as $key => $item) {
            if( $item->date ){
                $array['netCredit'][]    = [ strtotime($item->date) * 1000, (int)$item->credit ];
                $array['gainedCredit'][] = [ strtotime($item->date) * 1000, (int)$item->gained_credit ];
            	$array['spentCredit'][]  = [ strtotime($item->date) * 1000, (int)$item->spent_credit ];
            }
        }

    	return $array;
    }
}
