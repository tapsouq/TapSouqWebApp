<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class HelpTestCtrl extends Controller
{
    //
    /**
     * printAdservingQuery. To print ad serving query.
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function printAdservingQuery(Request $request)
    {
    	$deviceId = $request->input('deviceId');
    	$placementId = $request->input('placementId');
    	$creativeId = $request->input('creativeId');
    	$requestId = $request->input('requestId');
    	$action = $request->input('action');
    	$appPackage = $request->input('appPackage');

    	$adServingQuery = new AdServingQueryCtrl($placementId, $appPackage);
    	$adServingQuery->getCreativeAds ($deviceId);
    	echo $adServingQuery->getQuery();
    }
}
