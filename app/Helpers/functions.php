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
		return str_random(40) . mt_rand();
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
			'site_email'		=> "a.esawy.sapps@gmail.com",
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