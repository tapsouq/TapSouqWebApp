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
	$camps = DB::table('campaigns')->get();
	
	foreach ($camps as $key => $camp) {

		$coun = explode(',', $camp->countries);
		print_r($coun); 
		echo "<br>";
	}	                            
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
Route::get('terms-of-service', 'HomeCtrl@showTermOfService');
Route::get('resources-page', 'HomeCtrl@showResourcesPage');

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
		Route::get( 'app/all/{id?}', 'AppCtrl@index' ); // To show all apps.
		Route::get( 'app/create', 'AppCtrl@create' ); // To create the application.
		Route::post( 'store-app', 'AppCtrl@store' ); // To store the created application.
		Route::get( 'app/edit/{app}', 'AppCtrl@edit' ); // To edit the appliaction.
		Route::post( 'save-app', 'AppCtrl@save' ); // To save edited application.
		Route::get( 'delete-app', 'AppCtrl@destroy' ); // To deactivate application.
		Route::post( 'get-keywords', 'AppCtrl@getKeywords' ); // To get the keywords that contain search words.
		/** End Application Module**/

		/** Ad Zones ( Ad Placement ) Module  **/
		Route::get( 'zone/all/{id?}', 'ZoneCtrl@index' ); // To show all ads's zones page.
		Route::get( 'zone/create', 'ZoneCtrl@create' ); // To show create ad's zone page.
		Route::post( 'store-zone', 'ZoneCtrl@store' ); // To store the created ad's zone.
		Route::get( 'zone/edit/{id}', 'ZoneCtrl@edit' ); // To show edit ad's zone page.
		Route::post( 'save-zone', 'ZoneCtrl@save' ); // To save edited ad's zone.
		Route::get( 'delete-zone', 'ZoneCtrl@destroy' ); // To deactivate ad's zone.
		Route::get('zone/{id}', 'ZoneCtrl@show');
		/** End Zone ( Ad Placement ) Module  **/

		/** Campaigns Module  **/
		Route::get( 'campaign/all/{id?}', 'CampaignCtrl@index' ); // To show all campaigns page.
		Route::get( 'campaign/create', 'CampaignCtrl@create' ); // To show create campaign page.
		Route::post( 'store-campaign', 'CampaignCtrl@store' ); // To store the created campaign.
		Route::get( 'campaign/edit/{id}', 'CampaignCtrl@edit' ); // To show edit campaign page.
		Route::post( 'save-campaign', 'CampaignCtrl@save' ); // To save edited campaign.
		Route::get( 'camp/change-status', 'CampaignCtrl@changeStatus' ); // To change campaign status.
		/** End Campaigns Module  **/

		/** Creative Ad Module  **/
		Route::get( 'ads/all/{id?}', 'AdsCtrl@index' ); // To show all creative ads's page.
		Route::get( 'ads/create', 'AdsCtrl@create' ); // To show create creative ad's  page.
		Route::post( 'store-ads', 'AdsCtrl@store' ); // To store the created creative ad's.
		Route::get( 'ads/edit/{id}', 'AdsCtrl@edit' ); // To show edit creative ad's page.
		Route::post( 'save-ads', 'AdsCtrl@save' ); // To save edited creative ad's zone.
		Route::get( 'ads/change-status', 'AdsCtrl@changeStatus' ); // To change Ads status.
		Route::get('ads/{id}', 'AdsCtrl@show');
		/** End Creative Ad Module  **/

		/** Middleware for admin users **/
		Route::group([ 'middleware' => 'admin' ], function(){
			
			/** For User Module **/
			Route::get( 'user/all', 'UserCtrl@index' ); // Show all users page.
			Route::get( 'user/edit/{id}', 'UserCtrl@edit' ); // Show edit user page.
			Route::post( 'save-user', "UserCtrl@save" ); // To save edited user.
			Route::get( 'user/delete', "UserCtrl@destroy" );// To delete the user.
			/** End User Module **/

			/** For Keywords Module **/
			Route::get( 'matching/matched-keywords', 'MatchingCtrl@showMatched' ); // To show all matched keywords with applications.
			Route::get( 'matching/unmatched-keywords', 'MatchingCtrl@showEmpty' ); // To show all empty keywords matching
			Route::get( 'delete-matching', 'MatchingCtrl@deleteMatching' ); // To delete keyword matching
			Route::get( 'change-priority', 'MatchingCtrl@changePriority' ); // To change priority for matching
			/** End Keywords Module **/

			/** Admin Reports **/
				Route::get('reports/relevant-ads/{id}', 'ReportCtrl@showRelevant'); // To show all relevant ads
				Route::get('reports/shown-ads/{id}', 'ReportCtrl@showShownAds'); // To show all relevant ads
				Route::get('reports/campaigns-and-creatives', 'ReportCtrl@showCampAndAds');
				Route::get('reports/show-all-apps', 'ReportCtrl@showAllApps');
				Route::get('device-reports/{report}', 'ReportCtrl@showDeviceReports');
/*				Route::get('device-reports/languages', 'ReportCtrl@showDeviceLanguages');
				Route::get('device-reports/manufacturers', 'ReportCtrl@showDevicemanufacturers');
				Route::get('device-reports/models', 'ReportCtrl@showDeviceModels');
				Route::get('device-reports/os-versions', 'ReportCtrl@showDeviceOsVersions');
				Route::get('device-reports/carriers', 'ReportCtrl@showDeviceCarriers');
			/** End Admin Reports**/
			
			/* Test routes */
			Route::get('test-algorithm', 'HelpTestCtrl@printAdservingQuery');
		});
	});
});

Route::get(
	"create_device/{platform}/{advertising_id}/{manufacturer}/{model}/{os_api_number}/{os_version}/{language}/{country}/{carrier}/{tap_souq_sdk_version}",
	"Admin\SdkCtrl@addDevice"
);

Route::get(
	"sdk-action/{device_id}/{action}/{request_id}/{ad_placement_id}/{ad_creative_id}/{app_package}",
	"Admin\SdkCtrl@setAction"
);

Route::get(
	"update_device/{device_id}/{manufacturer}/{model}/{os_api_number}/{os_version}/{language}/{country}/{carrier}/{tap_souq_sdk_version}",
	"Admin\SdkCtrl@updateDevice"
);
