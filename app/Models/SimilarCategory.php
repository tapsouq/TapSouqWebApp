<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimilarCategory extends Model
{
    //
    protected $table = "similar_cats";

    protected $fillable = ['cat_id', 'simi_cats'];

    /**
     * getTableSimiCats. To get the application similar categories.
     *
     * @param  string $tableName
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public static function getTableSimiCats( $tableName )
    {
    	return \DB::select(
    			"SELECT `{$tableName}`.`id`, `{$tableName}`.`name`, `{$tableName}`.`fcategory`, `{$tableName}`.`scategory`, `simi_fcats`.`simi_cats` AS `simi_fcats`, `simi_scats`.`simi_cats` AS `simi_scats` 
    				FROM `{$tableName}` 
    				LEFT JOIN `similar_cats` `simi_fcats` ON `simi_fcats`.`cat_id` = `{$tableName}`.`fcategory` 
    				LEFT JOIN `similar_cats` `simi_scats` ON `simi_scats`.`cat_id` = `{$tableName}`.`scategory` 
    			"
    		);
    }

    /**
     * getSimiCats. To get the simi categories for the appication or the campaign categories.
     *
     * @param  int $fcategory first category
     * @param  int $scategory second category
     * @return string similart categories
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public static function getSimiCats($fcategory, $scategory)
    {
        $results = self::select('simi_cats')
                        ->whereIn('cat_id', [$fcategory, $scategory] )
                        ->lists('simi_cats')
                        ->toArray();

        $simiFirstCats  = isset($results[0]) ? explode(",", $results[0]) : [];
        $simiSecondCats = isset($results[1]) ? explode(",", $results[1]) : [];
        
        return implode(',', array_unique( array_merge($simiFirstCats, $simiSecondCats) ));
    }
}
