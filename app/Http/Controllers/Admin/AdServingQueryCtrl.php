<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AdServingQueryCtrl extends Controller
{
	// sdk_requests table id. The identifier of sdk_requests table.
	private $_requestId;
	// status of the ad serving. relavant or retrieve.
	private $_status;

	private $_placementId;
	private $_countryId;
	private $_deviceId;


	/** Constants **/      
    private $_admin          = ADMIN_PRIV;
    private $_runningCamp    = RUNNING_CAMP;
    private $_runningAd      = RUNNING_AD;
    private $_activeUser     = ACTIVE_USER;
    private $_interstitial   = INTERSTITIAL;

    // The parts that the ad serving algorithm query is depending on.
    private $_queryParts;

    private $_query;
	/**
	 * __construct. To init the class.
	 *
	 * @param  int $placementId
	 * @param  int $appPackage
	 * @param  int $requestId
	 * @return return
	 * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
	 * @copyright Smart Applications Co. <www.smartapps-ye.com>
	 */
	public function __construct($placementId, $appPackage)
	{
		$this->_placementId = $placementId;
		$this->_appPackage = $appPackage;
	}

    /**
     * getCreativeAds. To get the most suitable creative ads for placement zone.
     *
     * @param int $placementId.
     * @param int $deviceId.
     * @param string $appPackage.
     * @return array
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function getCreativeAds ($deviceId){
    	$this->_status = 'retrieve';
    	$this->_deviceId = $deviceId;
        return $this->_setQuery();
    }

    /**
     * getRelevantAds. To get the most suitable relevant ads for placement zone.
     *
     * @param int $placementId.
     * @param int $countryId.
     * @return array
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function getRelevantAds ($countryId){
    	$this->_status = 'relevant';
    	$this->_countryId = $countryId;
        return $this->_setQuery();
    }

    /**
     * _setQuery. To set the query for retrieve relevant ads to be shown or creative ads as sdk response.
     *
     * @param void
     * @return array
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _setQuery (){

    	$object     = $this->_adaptStatusDiffInAlgorithm();
        $conditions = $this->_adServingQueryParts();
        $queryParts = $this->_adServingQueryParts();

        $this->_query = "
                SELECT 
                    DISTINCT `ad_creative`.`id`, `ad_creative`.`name`, `ad_creative`.`format`, `ad_creative`.`layout`, `ad_creative`.`type`, `ad_creative`.`camp_id`,
                    `ad_creative`.`click_url`, `ad_creative`.`image_file`, `ad_creative`.`status`, `ad_creative`.`title`, `ad_creative`.`description`,   
                    ( ROUND( (`camp_users`.`credit` * 1000000 / (UNIX_TIMESTAMP(`campaigns`.`end_date`) - UNIX_TIMESTAMP(`campaigns`.`start_date`)) )) + 1 ) as `priority`,
                    `ad_placement`.refresh_interval as refreshInterval
                    {$object->select->{$this->_status}}
                FROM `ad_creative`
                    INNER JOIN `campaigns`                          ON `campaigns`.`id` = `ad_creative`.`camp_id`
                    INNER JOIN `ad_placement`                       ON `ad_placement`.`format` = `ad_creative`.`format`
                    INNER JOIN `applications` `app`                 ON `app`.`platform` = `campaigns`.`target_platform`
                    {$object->joinDevice->{$this->_status}}
                    INNER JOIN `users` `camp_users`                 ON `camp_users`.`id` = `campaigns`.`user_id`
                    INNER JOIN `users` `app_users`                  ON `app_users`.`id`  = `app`.`user_id`
                    {$object->joinCountries->{$this->_status}}
                    LEFT  JOIN `campaign_keywords`                  ON `campaign_keywords`.`camp_id` = `campaigns`.`id`
                WHERE
                        `ad_placement`.`id` = {$this->_placementId}
                    AND
                        `ad_placement`.`app_id` = `app`.`id`
                    AND
                    (
                        /* Category */
                        {$queryParts->categoryConditions}
                        OR
                        /* Keywords */
                        {$queryParts->keywordConditions}
                    )
                    AND
                        /* App users not camp users */
                        {$queryParts->diffUserConditions}
                    AND
                    (
                        {$object->countrySelect->{$this->_status}}
                    )
                    AND
                        /* Campaign Time */
                        {$queryParts->campTimeConditions}
                    AND
                        /* Credit */
                        {$queryParts->creditConditions}
                    AND
                        /* Imps per day */
                        {$queryParts->impPerDayConditions}
                    AND
                        `ad_creative`.`status` = {$this->_runningAd}
                    AND
                        `campaigns`.`status`   = {$this->_runningCamp}
                    AND
                        `camp_users`.`status`  = {$this->_activeUser}
                    AND
                        `app_users`.`status`   = {$this->_activeUser}
                    AND
                        `app`.`package_id`     = '{$this->_appPackage}'
                    AND
                        /* Layout */
                        {$queryParts->deviceLayoutConditions}
                    ORDER BY `priority` DESC";
        return \DB::select($this->_query);
    }

    /**
     * getQuery. To get the query for test reasons.
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function getQuery()
    {
        return $this->_query;
    }
    /**
     * _adaptStatusDiffInAlgorithm. To adapt the algorithm changes due to the status.
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _adaptStatusDiffInAlgorithm()
    {
    	$var = [];
    	$relevantAdSelect       = ", `campaigns`.`name` as `campName`, `camp_users`.`fname`, `camp_users`.`lname`, `campaigns`.`fcategory`, `campaigns`.`scategory`";
    	$retrieveJoinDevice     = "INNER JOIN `devices` ON `devices`.`id` = {$this->_deviceId}";
    	$retrieveCountrySelect  = " ( SELECT COUNT(*) FROM `campaign_countries` WHERE `campaign_countries`.`camp_id` = `campaigns`.`id`) = 0
    	                                OR
    	                            `devices`.`country` IN ( SELECT `campaign_countries`.`country_id` FROM `campaign_countries` WHERE `campaign_countries`.`camp_id` = `campaigns`.`id` )";
    	
    	$relevantCountrySelect  = " ( SELECT COUNT(*) FROM `campaign_countries` WHERE `campaign_countries`.`camp_id` = `campaigns`.`id`) = 0
    	                                OR
    	                            {$this->_countryId} IN ( SELECT `campaign_countries`.`country_id` FROM `campaign_countries` WHERE `campaign_countries`.`camp_id` = `campaigns`.`id` )";

    	$relevantJoinCountry    = "INNER JOIN `countries` ON `countries`.`id` = {$this->_countryId}";
    	$retrieveJoinCountry    = "INNER JOIN `countries` ON `countries`.`id` = `devices`.`country`";

    	$var    = [
    	        'select'        => [ 'relevant' => $relevantAdSelect,       'retrieve' => '' ],
    	        'joinDevice'    => [ 'relevant' => '',                      'retrieve' => $retrieveJoinDevice ],
    	        'countrySelect' => [ 'relevant' => $relevantCountrySelect,  'retrieve' => $retrieveCountrySelect ],
    	        'joinCountries' => [ 'relevant' => $relevantJoinCountry,    'retrieve' => $retrieveJoinCountry]
    	    ];
    	return json_decode(json_encode($var));
    }

    /**
     * _adServingQueryParts. It's the parts of the algorithm query.
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _adServingQueryParts()
    {
    	$_category = 
    			"(  
                        `campaigns`.`scategory` IN (`app`.`fcategory`, `app`.`scategory` )
                
                    OR
                        `campaigns`.`fcategory` IN ( `app`.`fcategory`, `app`.`scategory` )
                    OR
                    (
                            `campaigns`.`fcategory` IS NULL
                        AND
                            `campaigns`.`scategory` IS NULL
                    )
                )";

        $_keywords =
        		"(
                    CASE
                        WHEN `campaign_keywords`.`keyword_id` IS NULL 
                            THEN 1
                        WHEN `campaign_keywords`.`keyword_id` IS NOT NULL 
                            THEN `campaign_keywords`.`keyword_id` IN ( SELECT `keyword_id` FROM `application_keywords` WHERE `application_keywords`.`app_id` = `ad_placement`.`app_id` )
                    END
                )";

        $_diffUser = "`camp_users`.`id` != `app_users`.`id`";

        $_campTime = "NOW() BETWEEN `campaigns`.`start_date` AND `campaigns`.`end_date`";

        $_credit = 
        		"(
                    (
                            `camp_users`.`role` != {$this->_admin} 
                        AND 
                            `camp_users`.`credit` >   `countries`.`tier`
                    )
                    OR
                    (
                            `camp_users`.`role`     = {$this->_admin} 
                        AND
                            ROUND(`camp_users`.`credit`)   >= `countries`.`tier`
                        AND
                            ROUND(`app_users`.`debit`, 1)     >= ( 0.9 * `countries`.`tier`) 
                    )
                )";

        $_impPerDay = 
        		"(
                    CASE
                        WHEN `campaigns`.`imp_per_day` IS NULL 
                            THEN 1
                        WHEN `campaigns`.`imp_per_day` IS NOT NULL
                            THEN `campaigns`.`imp_per_day` > `campaigns`.`imp_in_today` 
                    END
                )";

        $_deviceLayout = 
        		"CASE
                    WHEN `ad_placement`.`format` = {$this->_interstitial}
                        THEN `ad_placement`.`layout` = `ad_creative`.`layout`
                        ELSE 1
                END";
                
    	$this->_queryParts = [
                "categoryConditions"        => $_category,
                "keywordConditions"         => $_keywords,
                "diffUserConditions"        => $_diffUser,
                "campTimeConditions"        => $_diffUser,
                "creditConditions"          => $_credit,
                "impPerDayConditions"       => $_impPerDay,
                "deviceLayoutConditions"    => $_deviceLayout
    		];
        return json_decode(json_encode($this->_queryParts));
    }
}
