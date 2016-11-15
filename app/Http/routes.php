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
Route::get('test', function(){

	$modelIds   = DB::table('ad_placement')->select(DB::raw("DISTINCT(app_id) AS `id`"))->lists('id');
	$logIds   	= DB::table('placement_log')->select(DB::raw("DISTINCT(ads_id) AS `id`"))->lists('id');
    $rows       = DB::table('applications')
    					->whereNotIn('id', $modelIds)
    					->select('applications.*')
                        ->union(
                        	DB::table('applications')
                        		->join('ad_placement', 'ad_placement.app_id', '=', 'applications.id')
                        		->select('applications.*')
                        		->whereNotIn('ad_placement.id', $logIds)
                        		->whereNotIn('applications.id', $modelIds)
                        )->get();
	dd($rows);


	dd($array[mt_rand(0, $max - 1 )] );
});

// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');
Route::get( 'verify-email', 'Auth\AuthController@verifyEmail' );

// Home Module
Route::get('/', 'HomeCtrl@index');


// Middlware fo authinticated active users
Route::group(['middleware' => 'auth'], function () {
	// Name space not to repeate admin per controller for every route
	Route::group(['namespace' => 'Admin'], function()
	{
		// Dashboard
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
		Route::get( 'campaign/all/{user?}', 'CampaignCtrl@index' ); // To show all campaigns page.
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
		//Route::get( 'delete-ads', 'AdsCtrl@destroy' ); // To deactivate creative ad's zone.
		Route::get( 'ads/change-status', 'AdsCtrl@changeStatus' ); // To change Ads status.
		Route::get('ads/{ads}', 'AdsCtrl@show');
		/** End Creative Ad Module  **/

		/** Middleware for admin users **/
		Route::group([ 'middleware' => 'admin' ], function(){
			
			/** For User Module **/
			Route::get( 'user/all', 'UserCtrl@index' ); // Show all users page.
			Route::get( 'user/edit/{user}', 'UserCtrl@edit' ); // Show edit user page.
			Route::post( 'save-user', "UserCtrl@save" ); // To save edited user.
			Route::get( 'user/delete', "UserCtrl@destroy" );// To delete the user.
			/** End User Module **/

			/** For Keywords Module **/
			Route::get( 'matching/matched-keywords', 'MatchingCtrl@showMatched' ); // To show all matched keywords with applications.
			Route::get( 'matching/unmatched-keywords', 'MatchingCtrl@showEmpty' ); // To show all empty keywords matching
			Route::get( 'delete-matching', 'MatchingCtrl@deleteMatching' ); // To delete keyword matching
			Route::get( 'change-priority', 'MatchingCtrl@changePriority' ); // To change priority for matching
			/** End Keywords Module **/
		});
	});
});

Route::get(
	"create_device/{platform}/{advertising_id}/{manefacturer}/{model}/{os_version}/{language}/{country}/{city}/{carrier}/{tap_souq_sdk_version}",
	"Admin\SdkCtrl@addDevice"
);

Route::get(
	"sdk-action/{device_id}/{action_name}/{request_id}/{ad_placement_id}/{ad_creative_id}",
	"Admin\SdkCtrl@setAction"
);

Route::get(
	"update_device/{device_id}/{language}/{country}/{os}/{model}/{manefacturer}",
	"Admin\SdkCtrl@updateDevice"
);
