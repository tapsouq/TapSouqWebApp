<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    //
    protected $table = 'applications'; 

    /**
     * getAppsInfo. To get applications information.
     *
     * @param int $status
     * @return array
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public static function getAppsInfo ( $status ){
    	
		return Self::join('users', 'users.id', '=', 'applications.user_id')
					->select(
								'users.fname',
								'users.lname',
								'applications.*'
							)
					->where('applications.status', '=', $status)
					->orderBy('applications.created_at', 'DESC');
    }
}
