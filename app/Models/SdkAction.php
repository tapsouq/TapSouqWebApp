<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class SdkAction extends Model
{
    protected $table = 'sdk_actions';

    private $initValues; 

    /**
     * __construct. To init the class
     *
     * @param void
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function __construct ( ){
        $initValues = [

            ];
    }

    /**
     * insertAction. To insert the into database.
     *
     * @param int $action. Action type
     * @param int $requestId
     * @return boolean
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public static function insertAction ( $action, $requestId ){
        
        $values = [
                'request_id'     => $requestId,
                'action'        => $action,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s')
            ];

        return DB::table('sdk_actions')->insert( $values );
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
    public static function getCreativeAds ( $placementId, $deviceId, $appPackage ){
        
        /** Constants **/
        $admin          = ADMIN_PRIV;
        $runningCamp    = RUNNING_CAMP;
        $runningAd      = RUNNING_AD;
        $activeUser     = ACTIVE_USER;
        $interstitial   = INTERSTITIAL;

        $query = "
            SELECT 
                    `ad_creative`.*,
                    (`camp_users`.`credit` * 1000 / (UNIX_TIMESTAMP(`campaigns`.`end_date`) - UNIX_TIMESTAMP(`campaigns`.`start_date`)) ) as `priority`
                FROM `ad_creative`
                    INNER JOIN `campaigns`                          ON `campaigns`.`id` = `ad_creative`.`camp_id`
                    INNER JOIN `ad_placement`                       ON `ad_placement`.`format` = `ad_creative`.`format`
                    INNER JOIN `applications` `app`                 ON `app`.`platform` = `campaigns`.`target_platform`
                    INNER JOIN `devices`                            ON `devices`.`id` = {$deviceId}
                    INNER JOIN `users` `camp_users`                 ON `camp_users`.`id` = `campaigns`.`user_id`
                    INNER JOIN `users` `app_users`                  ON `app_users`.`id` = `app`.`user_id`
                    LEFT  JOIN `campaign_keywords`                  ON `campaign_keywords`.`camp_id` = `campaigns`.`id`
                WHERE
                        `ad_placement`.`id` = {$placementId}
                    AND
                        `ad_placement`.`app_id` = `app`.`id`
                    AND
                    (
                        (  
                                `campaigns`.`scategory` IN (`app`.`fcategory`, `app`.`scategory` )
                        
                            OR
                                `campaigns`.`fcategory` IN ( `app`.`fcategory`, `app`.`scategory` )
                            OR
                            (
                                    `campaigns`.`fcategory` IS NULL
                                AND
                                    `campaigns`.`scategory` IS NULL
                            )
                        )
                        OR
                        (
                            CASE
                                WHEN `campaign_keywords`.`keyword_id` IS NULL 
                                    THEN 1
                                WHEN `campaign_keywords`.`keyword_id` IS NOT NULL 
                                    THEN `campaign_keywords`.`keyword_id` IN ( SELECT `keyword_id` FROM `application_keywords` WHERE `application_keywords`.`app_id` = `ad_placement`.`app_id` )
                            END
                        )
                    )
                    AND
                        `camp_users`.`id` != `app_users`.`id`
                    AND
                    (
                            ( SELECT COUNT(*) FROM `campaign_countries` WHERE `campaign_countries`.`camp_id` = `campaigns`.`id`) = 0
                        OR
                            `devices`.`country` IN ( SELECT `campaign_countries`.`country_id` FROM `campaign_countries` WHERE `campaign_countries`.`camp_id` = `campaigns`.`id` )
                    )
                    AND
                        ADDTIME(NOW(), '08:00:00') BETWEEN `campaigns`.`start_date` AND `campaigns`.`end_date`
                    AND
                    (
                        (
                                `camp_users`.`role` != {$admin} 
                            AND 
                                `camp_users`.`credit` >   1
                        )
                        OR
                        (
                                `camp_users`.`role`     = {$admin} 
                            AND
                                ROUND(`camp_users`.`credit`)   >= 1
                            AND
                                ROUND(`app_users`.`debit`, 1)     >= 0.9 
                        )
                    )
                    AND

                        `ad_creative`.`status` = {$runningAd}
                    AND
                        `campaigns`.`status`   = {$runningCamp}
                    AND
                        `camp_users`.`status`  = {$activeUser}
                    AND
                        `app_users`.`status`   = {$activeUser}
                    AND
                        `app`.`package_id`     = '{$appPackage}'
                    AND
                        CASE
                            WHEN `ad_placement`.`format` = {$interstitial}
                                THEN `ad_placement`.`layout` = `ad_creative`.`layout`
                        END
                    ORDER BY `priority` DESC";
        return \DB::select($query);

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
    public static function getRelevantAds ( $placementId, $countryId ){
        
        $admin         = ADMIN_PRIV;
        $runningCamp   = RUNNING_CAMP;
        $runningAd     = RUNNING_AD;
        $activeUser    = ACTIVE_USER;

        $query = "";
        return \DB::select($query);
    }

}
