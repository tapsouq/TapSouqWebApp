<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    //
    protected $table = 'devices';

    /**
     * validateDeviceLangAndCountryIds. To validate the country and language ids.
     *
     * @param  int $languageId
     * @param  int $countryId
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public static function validateDeviceLangAndCountryIds($languageId, $countryId)
    {
		return \DB::select(
				"SELECT `countries`.`name` as `country`, `languages`.`name` as `language` 
					FROM `countries` 
					LEFT JOIN  `languages` ON `languages`.`id` = ?
                    WHERE `countries`.`id` = ?", [ $languageId, $countryId ]);
    }
}
