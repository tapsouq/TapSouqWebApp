<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get( 'test', function(){
/*
	$sdk = DB::select( "SELECT `created_at` from sdk_actions group by `created_at`" );
	$times = array_pluck($sdk, 'created_at');
	
	for( $i = 26; $i < 47; $i++ ){
		$created_at = $times[$i];

		$time = time() + ( ($i - 25) * 24 * 60 * 60 );
		$date = date( 'Y-m-d H:i:s', $time );
		DB::update(
				"update `sdk_actions` set `created_at` = '" . $date . "' where `created_at` = '" . $created_at ."'"
			);
	}

*/
	set_time_limit(1000);
	$insertArray = [];
	for ($i=0; $i < 1000; $i++) { 
 		$placementId 	= mt_rand( 1, 5 );
 		$creativeId 	= mt_rand( 1, 5 );
 		$deviceId 		= mt_rand( 1, 100 );
 		$time = time() + ( 15 * 24 * 60 * 60 );
 		$sharedData = [
 				'placement_id' 	=> $placementId,
 				'creative_id'	=> $creativeId,
 				'device_id'		=> $deviceId,
 				'created_at'	=> date('Y-m-d H:i:s', $time ),
 				'updated_at'	=> date('Y-m-d H:i:s', $time )
 			];

 		// insert request action
 		$insertArray[] = array_merge( $sharedData, ['action' => REQUEST_ACTION ] );

 		// get show action
 		if( mt_rand( 0, 100 ) != 0 ){
 			// insert show action
 			$insertArray[] = array_merge($sharedData, [ 'action' => SHOW_ACTION ]);

 			// get click action
 			if( mt_rand(0,3) != 0 ){
 				$insertArray[] = array_merge($sharedData, [ 'action' => CLICK_ACTION ]);

 				// get installed action 
 				if( mt_rand(0, 1) != 0 ){

 					// insert install action
	 				$insertArray[] = array_merge($sharedData, [ 'action' => INSTALL_ACTION ]);
 				}
 			}
 		}
 	} 	

	DB::table('sdk_actions')->insert( $insertArray );
	

} );

// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');
Route::get( 'verify-email', 'Auth\AuthController@verifyEmail' );

// Middlware fo authinticated active users
Route::group(['middleware' => 'auth'], function () {
	// Name space not to repeate admin per controller for every route
	Route::group(['namespace' => 'Admin'], function()
	{
		Route::get( 'admin', 'DashboardCtrl@index' );
	
		/** For User Module **/
		Route::get( 'profile', 'UserCtrl@editProfile' ); // To show profile page and edit it.
		Route::post( 'save-profile', 'UserCtrl@saveProfile' ); // to save changes.
		/** End For User Module **/
		
		/** Application Module **/
		Route::get( 'app/all/{user?}', 'AppCtrl@index' ); // To show all apps.
		Route::get( 'app/create', 'AppCtrl@create' ); // To create the application.
		Route::post( 'store-app', 'AppCtrl@store' ); // To store the created application.
		Route::get( 'app/edit/{app}', 'AppCtrl@edit' ); // To edit the appliaction.
		Route::post( 'save-app', 'AppCtrl@save' ); // To save edited application.
		Route::get( 'delete-app', 'AppCtrl@destroy' ); // To deactivate application.
		Route::post( 'get-keywords', 'AppCtrl@getKeywords' ); // To get the keywords that contain search words.
		/** End Application Module**/

		/** Ad Zones ( Ad Placement ) Module  **/
		Route::get( 'zone/all/{app?}', 'ZoneCtrl@index' ); // To show all ads's zones page.
		Route::get( 'zone/create', 'ZoneCtrl@create' ); // To show create ad's zone page.
		Route::post( 'store-zone', 'ZoneCtrl@store' ); // To store the created ad's zone.
		Route::get( 'zone/edit/{zone}', 'ZoneCtrl@edit' ); // To show edit ad's zone page.
		Route::post( 'save-zone', 'ZoneCtrl@save' ); // To save edited ad's zone.
		Route::get( 'delete-zone', 'ZoneCtrl@destroy' ); // To deactivate ad's zone.
		Route::get('zone/{zone}', 'ZoneCtrl@show');
		/** End Zone ( Ad Placement ) Module  **/

		/** Campaigns Module  **/
		Route::get( 'campaign/all', 'CampaignCtrl@index' ); // To show all campaigns page.
		Route::get( 'campaign/create', 'CampaignCtrl@create' ); // To show create campaign page.
		Route::post( 'store-campaign', 'CampaignCtrl@store' ); // To store the created campaign.
		Route::get( 'campaign/edit/{zone}', 'CampaignCtrl@edit' ); // To show edit campaign page.
		Route::post( 'save-campaign', 'CampaignCtrl@save' ); // To save edited campaign.
		Route::get( 'camp/change-status', 'CampaignCtrl@changeStatus' ); // To change campaign status.
		/** End Campaigns Module  **/

		/** Creative Ad Module  **/
		Route::get( 'ads/all/{camp?}', 'AdsCtrl@index' ); // To show all creative ads's page.
		Route::get( 'ads/create', 'AdsCtrl@create' ); // To show create creative ad's  page.
		Route::post( 'store-ads', 'AdsCtrl@store' ); // To store the created creative ad's.
		Route::get( 'ads/edit/{ads}', 'AdsCtrl@edit' ); // To show edit creative ad's page.
		Route::post( 'save-ads', 'AdsCtrl@save' ); // To save edited creative ad's zone.
		Route::get( 'delete-ads', 'AdsCtrl@destroy' ); // To deactivate creative ad's zone.
		Route::get( 'ads/change-status', 'AdsCtrl@changeStatus' ); // To change Ads status.
		Route::get('ads/{ads}', 'AdsCtrl@show');
		/** End Creative Ad Module  **/

		// Middleware for admin users
		Route::group([ 'middleware' => 'admin' ], function(){
			
			/** For User Module **/
			Route::get( 'user/all', 'UserCtrl@index' ); // show all users page.
			Route::get( 'user/edit/{user}', 'UserCtrl@edit' ); // Show edit user page.
			Route::post( 'save-user', "UserCtrl@save" ); // To save edited user.
			Route::get( 'user/delete', "UserCtrl@destroy" );// To delete the user.
			/** End User Module **/

			/** For Keywords Module **/
			Route::get( 'matching/matched-keywords', 'MatchingCtrl@showMatched' ); // To show all matched keywords with applications.
			Route::get( 'matching/unmatched-keywords', 'MatchingCtrl@showEmpty' ); // To show all empty keywords matching
			Route::get( 'delete-matching', 'MatchingCtrl@deleteMatching' ); // to delete keyword matching
			Route::get( 'change-priority', 'MatchingCtrl@changePriority' ); // to change priority for matching
			/** End Keywords Module **/
		});
	});
});

Route::get( '/', function(){
	return "Page after login";
});


