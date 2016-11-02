<?php

Route::get('test/{after}/{before}', function($after, $before){
	
	set_time_limit(10000);
	$rows  	= [];
	$sdks   = [];
	$request_id = $after + 1;

	$result = DB::table('sdk_action')->where('id', '<=', $before)->where('id', '>', $after)->get();
	foreach ($result as $key => $value) {
		if($key == 0){
			$lastValue = $value;
			continue;
		}

		if( !( $value->placement_id == $lastValue->placement_id && $value->creative_id == $lastValue->creative_id && $value->device_id == $lastValue->device_id ) ){
			$rows[] = [
					'id'			=> $request_id,
					'placement_id'	=> $value->placement_id,
					'creative_id'	=> $value->creative_id,
					'device_id'		=> $value->device_id,
					'created_at'	=> $value->created_at,
					'updated_at'	=> $value->updated_at
				];
			$sdks[] = [
				'request_id'	=> $request_id,
				'action'		=> 1,
				'created_at'	=> $value->created_at,
				'updated_at'	=> $value->updated_at
			];
			$request_id = $value->id;
		}else{
			$sdks[] = [
				'request_id'	=> $request_id,
				'action'		=> $value->action,
				'created_at'	=> $value->created_at,
				'updated_at'	=> $value->updated_at
			];		
		}

		$lastValue = $value;
	}

	DB::table('sdk_requests')->insert($rows);
	DB::table('sdk_actions')->insert($sdks);

});

Route::get('test/{after}/{before}', function($after, $before){
	set_time_limit(10000);
	$time   = strtotime('2016-10-09');
	$request_id = 96375;
	$sdk_id 	= 96372;

	for($i = 1; $i < 40; $i++ ){

		$requests 	= [];
		$sdks   = [];

		for($j = 0; $j< 3000; $j++){
			
			$time = strtotime('2016-10-09') + ( $i * 24 * 60 * 60 ) + mt_rand(10, 60 * 60 * 23);
			
			$device_id 	 = mt_rand(1, 9);
			$creative_id = mt_rand(1, 6);
			$placement_id = mt_rand(1, 4);
			$date = date('Y-m-d h:i:s', $time);

			$requests[] = [
					'id'			=> $request_id++,
					'placement_id'	=> $placement_id,
					'creative_id'	=> $creative_id,
					'device_id'		=> $device_id,
					'created_at'	=> $date,
					'updated_at'	=> $date
				];

			$rowSdk = [
					'action'		=> 1,
					'request_id'	=> $request_id,
					'created_at'	=> $date,
					'updated_at'	=> $date
				];

			$sdks[] = $rowSdk;

			$showAbility = mt_rand(0, 1);
			
			if($showAbility){

				$rowSdk['action'] = 2;
				$sdks[] = $rowSdk;
					
				$clickAbility = mt_rand(0, 3);
				
				if($clickAbility){
					$rowSdk['action'] = 3;
					$sdks[] = $rowSdk;	
					
					$installAbility = mt_rand(0, 10);
					
					if($installAbility){
						$rowSdk['action'] = 4;
						$sdks[] = $rowSdk;	
					}	
				}
			}
		}
		DB::table('sdk_requests')->insert($requests);
		DB::table('sdk_actions')->insert($sdks);

	}
}