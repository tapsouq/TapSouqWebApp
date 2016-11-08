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
     * @return array
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public static function getCreativeAds ( $placementId, $deviceId ){
        $admin = ADMIN_PRIV;
        $query = "
            SELECT `ad_creative`.*
                FROM `ad_creative`
                    INNER JOIN `campaigns`                          ON `campaigns`.`id` = `ad_creative`.`camp_id`
                    INNER JOIN `ad_placement`                       ON `ad_placement`.`format` = `ad_creative`.`format`
                    INNER JOIN `campaign_categories`                ON `campaign_categories`.`camp_id` = `campaigns`.`id`
                    INNER JOIN `applications` `app`                 ON `app`.`platform` = `campaigns`.`target_platform`
                    INNER JOIN `application_categories`             ON `application_categories`.`app_id` = `app`.`id`
                    INNER JOIN `devices`                            ON `devices`.`id` = {$deviceId}
                    INNER JOIN `users` `camp_users`                 ON `camp_users`.`id` = `campaigns`.`user_id`
                    INNER JOIN `users` `app_users`                  ON `app_users`.`id` = `app`.`user_id`
                    LEFT  JOIN `campaign_keywords`                  ON `campaign_keywords`.`camp_id` = `campaigns`.`id`
                WHERE
                        `ad_placement`.`id` = {$placementId}
                    AND
                        `ad_placement`.`app_id` = `app`.`id`
                    AND
                        `campaign_categories`.`cat_id` = `application_categories`.`cat_id`
                    AND
                        CASE
                            WHEN `campaign_keywords`.`keyword_id` IS NULL THEN 1
                            WHEN `campaign_keywords`.`keyword_id` IS NOT NULL THEN `campaign_keywords`.`keyword_id` IN ( SELECT `keyword_id` FROM `application_keywords` WHERE `application_keywords`.`app_id` = `ad_placement`.`app_id` )
                        END
                    AND
                        `camp_users`.`id` != `app_users`.`id`
                    AND
                        (
                                ( SELECT COUNT(*) FROM `campaign_countries` WHERE `campaign_countries`.`camp_id` = `campaigns`.`id`) = 0
                            OR
                                `devices`.`country` IN ( SELECT `campaign_countries`.`country_id` FROM `campaign_countries` WHERE `campaign_countries`.`camp_id` = `campaigns`.`id` )
                        )
                    AND
                        CURDATE() BETWEEN `campaigns`.`start_date` AND `campaigns`.`end_date`
                    AND
                        `campaigns`.`language` = `devices`.`language`
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
                        ";
        return \DB::select($query);

    }

    /**
     * _calcCredits. To calculate credits.
     *
     * @param int $requestId
     * @param boolean $toAdmin. To check if the ads for admin or usual user
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _calcCredits ( $requestId, $toAdmin = false){
        // Edit uses
        DB::statement(
                "UPDATE `users` INNER JOIN `applications` ON `applications`.`user_id` = `users`.`id`
                                INNER JOIN `ad_placement` ON `ad_placement`.`app_id` = `applications`.`id`
                                INNER JOIN `sdk_requests` ON `sdk_requests`.`placement_id` = `ad_placement`.`id`
                                SET `users`.`credit` = `users`.`credit` + 0.9 
                            WHERE
                                `sdk_requests`.`id` = {$requestId}
                                ");
        DB::statement(
                "UPDATE `users` INNER JOIN `campaigns` ON `campaigns`.`user_id` = `users`.`id`
                                INNER JOIN `ad_creative` ON `ad_creative`.`camp_id` = `campaigns`.`id`
                                INNER JOIN `sdk_requests` ON `sdk_requests`.`creative_id` = `ad_creative`.`id`
                                SET `users`.`credit` = `users`.`credit` - 1 , `users`.`dept` = `users`.`dept` + 0.1 
                            WHERE
                                `sdk_requests`.`id` = {$requestId}
                                ");

    }
}
