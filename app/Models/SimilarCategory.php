<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimilarCategory extends Model
{
    //
    protected $table = "similar_cats";

    protected $fillable = ['cat_id', 'simi_cats'];

    /**
     * getAppsSimiCats. To get the application similar categories.
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public static function getAppsSimiCats()
    {
    	return \DB::select(
    			"SELECT `apps`.`name`, `apps`.`fcategory`, `apps`.`scategory`, `simi_fcats`.`simi_cats` AS `simi_fcats`, `simi_scats`.`simi_cats` AS `simi_scats` 
    				FROM `applications` `apps` 
    				LEFT JOIN `similar_cats` `simi_fcats` ON `simi_fcats`.`cat_id` = `apps`.`fcategory` 
    				LEFT JOIN `similar_cats` `simi_scats` ON `simi_scats`.`cat_id` = `apps`.`scategory` 
    			"
    		);
    }
}
