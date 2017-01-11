<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class SdkAction extends Model
{
    protected $table = 'sdk_actions';

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

}
