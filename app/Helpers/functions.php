<?php
if( ! function_exists( 'getToken' ) ){
	/**
	 * getToken
	 *
	 * @param void
	 * @return string. Random token. 
	 * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
	 * @copyright Sapps Company <>
	 */
	  function getToken ( ){
		return str_random(80);
	}
}

if( ! function_exists( 'getSiteInfo' ) ){
	/**
	 * getSiteInfo. To get themain info about the website.
	 *
	 * @param void
	 * @return Object.
	 * @todo to get these data from DB.
	 * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
	 * @copyright Smart Applications Co. <www.smartapps-ye.com>
	 */
	function getSiteInfo (  ){
		$array = [
			'site_title'  		=> trans( 'lang.tabsouq' ),
			'site_email'		=> "info@tapsouq.smart-apps-ar.com"
		];

		return json_decode( json_encode( $array ) );
	}
}

if(! function_exists('syncPivot')) {
    /**
     * syncPivot. To syncronize pivot table with these values.
     * 
     * @param string $table.
     * @param string $col.
     * @param string $key.
     * @param string $syncCol.
     * @param array $values.
     * @return boolean.
	 * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
	 * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */

    function syncPivot( $table, $col, $key, $syncCol, $values )
    {
        if ( ! \Schema::hasTable($table)) {
            return false;
        }
        $result = \DB::table( $table )
                    ->where( $col, '=', $key )
                    ->get();

        // get the present values on the database
        $presentValues =  array_pluck( $result, $syncCol );
        // delete the values not on the sync array of values
        $d_result = \DB::table( $table )
                        ->where( $col, '=', $key )
                        ->whereNotIn( $syncCol, $values )
                        ->delete();
        $newValues = array_diff($values, $presentValues);

        if( count( $newValues ) > 0 ){
            foreach ( $newValues as $value ) {
                $insertArray[] = [ $col => $key, $syncCol => $value ];
            }
            return \DB::table( $table )->insert( $insertArray );
        }
    }
}

if(! function_exists('uploadFile')) {
    /**
      * Upload File
      * 
      * @param $file ($request->file()).
      * @param $path (Optional default is public/uploads) .
	  * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
	  * @copyright Smart Applications Co. <www.smartapps-ye.com>
      */
    function uploadFile($file = null , $path = null)
    {
        $path = (isset($path) && $path != null) ? $path : 'public/uploads'; # check for path ...
        if ($file->isValid()) {
            $extension = $file->getClientOriginalExtension();
            $file_name = getToken() . '.' . $extension;
            $file->move($path , $file_name);
            //
            return $file_name;
        }
        return '';
    }
}
if(! function_exists('customUploadFile')) {
    /**
      * Custom Upload File
      * 
      * @param $file ($request->file()).
      * @param $path (Optional default is public/uploads) .
	  * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
	  * @copyright Smart Applications Co. <www.smartapps-ye.com>
      */
    function customUploadFile($file = null , $path = null)
    {
        $path = (isset($path) && $path != null) ? $path : 'public/uploads'; # check for path ...
        if ($file->isValid()) {
            $extension = $file->getClientOriginalExtension();
            $realname   = $file->getClientOriginalName();
            $size       = $file->getClientSize();
            $file_name = getToken() . '.' . $extension;
            $file->move($path , $file_name);
            // 
            $fileupload = new \App\Models\File();
            $fileupload->name       = $file_name;
            $fileupload->realname   = $realname;
            $fileupload->token      = getToken();
            $fileupload->size       = $size;
            $fileupload->extension  = $extension;
            $fileupload->type       = 'g'; // mean general file.
            $fileupload->save();
            //
            return $file_name;
        }
        return '';
    }
}

if( ! function_exists( 'getAppAds' ) ){
    /**
     * getAppAds. To get all activated ads connected to that application
     *
     * @param int $app_id
     * @return array.
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
     function getAppAds ( $app_id ){
        $ads = App\Models\Zone::where( 'app_id', '=', $app_id );
        $ads = Auth::user()->role == ADMIN_PRIV ? $ads : $ads->where( 'status', '!=', DELETED_ZONE );
        return $ads->get();
    }   
}

if( ! function_exists( 'getCampAds' ) ){
    /**
     * getCampAds. To get all activated ads connected to that Campaign
     *
     * @param int $camp_id
     * @return array.
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
     function getCampAds ( $camp_id ){
        $ads = App\Models\Ads::where( 'camp_id', '=', $camp_id );
        $ads = Auth::user()->role == ADMIN_PRIV ? $ads : $ads->where( 'status', '!=', DELETED_AD );
        return $ads->get();
    }   
}

if( ! function_exists('getAppAdsCount') ){
    /**
     * getAppAdsCount. to get the count of ads in the specific application. 
     *
     * @param int $app_id
     * @return int
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
     function getAppAdsCount ( $app_id ){
        $zones =  \App\Models\Zone::where('app_id', '=', $app_id);
        $user  = \Auth::user();

        if($user->role != ADMIN_PRIV ){
            $zones->where('ad_placement.status', '!=', DELETED_ZONE);
        }
        return $zones->count();
    }
}

if( ! function_exists('getCampAdsCount') ){
    /**
     * getCampAdsCount. to get the count of ads in the specific campaign. 
     *
     * @param int $camp_id
     * @return int
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
     function getCampAdsCount ( $camp_id ){
        $ads =  \App\Models\Ads::where('camp_id', '=', $camp_id);
        $user  = \Auth::user();

        if($user->role != ADMIN_PRIV ){
            $ads->where('ad_creative.status', '!=', DELETED_AD);
        }
        return $ads->count();
    }
}

if( ! function_exists('adaptChartData') ){
    /**
     * adaptChartData. To adapt the array that will be used in charts
     *
     * @param array $items
     * @param string $tableName
     * @param boolean $notCamp
     * @param boolean $dashboard
     * @return array
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    function adaptChartData ( $items, $tableName, $notCamp = true, $dashboard = false ){
        $items = $items->groupBy('date')
                        ->orderBy( "{$tableName}.created_at", 'ASC')
                        ->get();

        $array = [];
        foreach ($items as $key => $item) {
            if( $item->date ){
                if( $notCamp ){
                    $fillRate   = $item->requests != 0 ? round( $item->impressions/$item->requests, 2 ) * 100 : 0;
                    $array['requests'][] = [ strtotime($item->date) * 1000, (int)$item->requests ];
                    $array['fill_rate'][] = [ strtotime($item->date) * 1000, $fillRate ];
                }
                if($dashboard){
                    $array['credit'][]    = [ strtotime($item->date) * 1000, (int)$item->credit ];
                }

                $ctr        = $item->impressions != 0 ? round( $item->clicks/$item->impressions, 2) * 100 : 0;
                $array['impressions'][] = [ strtotime($item->date) * 1000, (int)$item->impressions ];
                $array['clicks'][] = [ strtotime($item->date) * 1000, (int)$item->clicks ];
                $array['ctr'][] = [ strtotime($item->date) * 1000, $ctr ];
            }
        }
        return $array;
    }
}

if( ! function_exists('filterByTimeperiod')){
    /**
     * filterByTimeperiod. To filter the results within time period
     *
     * @param \Illuminate\Database\Query\Builder $object
     * @param \Illuminate\Http\Request $request
     * @param string $table
     * @param array $forNewRows
     * @param boolean $sevenDays. To make sure that the default value isn't 7 days.
     * @return \Illuminate\Database\Query\Builder
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    function filterByTimeperiod($object, $request, $table, $sevenDays = false ){
        
        if( $request->has('from') && $request->has('to') ){
            $from       = $request->input("from");
            $to         = $request->input("to");
            $object->whereDate("{$table}.created_at", ">=", $from)
                    ->whereDate("{$table}.created_at", "<=", $to);
        }else{
            $interval = $sevenDays ? '6 days' : '30 days';
            $object->where( "{$table}.created_at", '<=', date('Y-m-d') . " 23:59:59")
                    ->where( "{$table}.created_at", '>=', date_create()->sub(date_interval_create_from_date_string($interval))->format("Y-m-d 00:00:00") );
        }
    }
}

if( ! function_exists('newRows') ){
    /**
     * newRows. To get the rows.
     *
     * @param string $table.
     * @param string $table.
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
     function newRows ( $table ){
        $array = [
                'applications'  => [
                        'status'    => true,
                        'model'     => 'applications',
                        'ad'        => 'ad_placement',
                        'log'       => 'placement_log',
                        'id'        => 'app_id'
                    ],
                'campaigns'     => [
                        'status'    => true,
                        'model'     => 'campaigns',
                        'ad'        => 'ad_creative',
                        'log'       => 'creative_log',  
                        'id'        => 'camp_id'
                    ],
                'ad_placement'  => [
                        'status'    => false,
                        'ad'        => 'ad_placement',
                        'log'       => 'placement_log'
                    ],
                'ad_creative'   => [
                        'status'    => false,
                        'ad'        => 'ad_creative',
                        'log'       => 'creative_log'
                    ]
            ];

        $object = (object) $array[ $table ];
        if( $object->status ){

            $modelIds   = DB::table('ad_placement')->select(DB::raw("DISTINCT(app_id) AS `id`"))->lists('id');
            $rows       = DB::table('applications')->whereNotIn('id', $modelIds)
                                      ->get();
            $modelIds   = DB::table($object->ad)->select(DB::raw("DISTINCT({$object->id}) AS `id`"))->lists('id');
            $rows       = DB::table($object->model)
                                ->select('*')
                                ->whereNotIn('id', $modelIds)
                                ->union(
                                        
                                    );
        }
    }
}