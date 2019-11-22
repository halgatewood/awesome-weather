<?php
/*
	Required attributes of the $weather object:
		$weather->owm_city_id, $weather->location OR $weather->latlng
*/
function awesome_weather_get_weather_data_openweathermaps( &$weather )
{
	// WE NEED AN OBJECT TO WORK WITH
	if( !is_object($weather) ) $weather = (object) $weather;
	
	// PROVIDER
	if( !isset($weather->provider) ) $weather->provider = 'openweathermaps';

	// UNITS
	$units_query = strtolower($weather->units) == 'c' ? 'metric' : 'imperial';
	
	// WIND LABELS
	$awe_wind_label = array ( __('N', 'awesome-weather'),__('NNE', 'awesome-weather'),__('NE', 'awesome-weather'),__('ENE', 'awesome-weather'),__('E', 'awesome-weather'),__('ESE', 'awesome-weather'),__('SE', 'awesome-weather'),__('SSE', 'awesome-weather'),__('S', 'awesome-weather'),__('SSW', 'awesome-weather'),__('SW', 'awesome-weather'),__('WSW', 'awesome-weather'),__('W', 'awesome-weather'),__('WNW', 'awesome-weather'),__('NW', 'awesome-weather'),__('NNW', 'awesome-weather') );
	
	// NO CITY ID YET
	if( !isset($weather->owm_city_id) ) $weather->owm_city_id = false;
	
	// SANITIZE
	if( isset($weather->location) ) $weather->location = trim($weather->location);
	
	// LAT/LONG SETTINGS
	if( isset($weather->latlng) AND $weather->latlng )
	{
		$latlng = awesome_weather_parse_lat_lon( $weather->latlng );
		if( !$latlng->lat AND !$latlng->lng )
		{
			return array('error' => true, 'msg' => __('Weather Geolocation Not Found', 'awesome-weather') );
		}
	}
	else if( !$weather->owm_city_id AND isset($weather->location) AND $weather->location != '' ) 
	{
		// GET LOCATION WITHOUT CITY ID
		$city_id_from_location = awesome_weather_get_owm_city_id( $weather->location, $units_query );
		
		if( $city_id_from_location )
		{
			$weather->owm_city_id = $$city_id_from_location;
		}
		else
		{
			return array('error' => true, 'msg' => __('Weather Location Not Found', 'awesome-weather') . ': <a href="https://openweathermap.org/find?q=' . $weather->location . '" target="_blank" style="color: #fff;">' . $weather->location . '</a>' );
		}
	}
	
	// AUTO UNITS
	if( $weather->units == 'auto' )
	{
		awe_get_units( $weather );
		$units_query = strtolower($weather->units) == 'c' ? 'metric' : 'imperial';
	}
	
	// CHECK FORECAST DAYS
	$add_to_transient = '';
	if( !isset($weather->forecast_days) ) $weather->forecast_days = 5;
	if( is_numeric($weather->forecast_days) )  $add_to_transient = 'f' . $weather->forecast_days;
	
	
	// FIND AND CACHE CITY ID
	if( $weather->owm_city_id AND is_numeric($weather->owm_city_id) )
	{
		$transient_main 		= $weather->owm_city_id;
		$api_query 				= 'id=' . $weather->owm_city_id;
	}
	else if( is_numeric($weather->location) )
	{
		$transient_main 		= $weather->location;
		$api_query				= 'id=' . $weather->location;
	}
	else if ( isset($weather->latlng) AND !empty($weather->latlng) )
	{
		$transient_main 		= sanitize_title($weather->latlng);
		$api_query				= 'lat=' . $latlng->lat . '&lon=' . $latlng->lng;
	}
	else if( trim($weather->location) != '')
	{
		$transient_main 		= sanitize_title($weather->location);
		$api_query				= 'q=' . $weather->location;
	}
	else
	{
		return array('error' => true, 'msg' => __('Weather Location Not Set', 'awesome-weather') );
	}
	
	// LOCALE
	if( !isset($weather->locale) )
	{
		$weather->locale = 'en';
		
		$sytem_locale = get_locale();
		$available_locales = awesome_weather_get_locales();
	
	
	    // CHECK FOR LOCALE
	    if( in_array( $sytem_locale, $available_locales ) ) $weather->locale = $sytem_locale;
	    
	    
	    // CHECK FOR LOCALE BY FIRST TWO DIGITS
	    if( in_array(substr($sytem_locale, 0, 2), $available_locales ) ) $weather->locale = substr($sytem_locale, 0, 2);
	}
	
	// IF NOT TRANSIENT NAME
	$transient_name = 'awe_owm_' . $transient_main . $units_query . $weather->locale . $add_to_transient;	

		
	// CLEAR THE TRANSIENT
	if( isset($_GET['clear_awesome_widget']) ) delete_transient( $transient_name );
	

	// GET WEATHER DATA FROM CACHE
	if( get_transient( $transient_name ) ) 
	{
		$weather->data_type = 'cache';
		return get_transient( $transient_name );
	}
	else
	{
		$weather->data_type = 'new';
	}

	// APPID
	$appid_string = '';
	$appid = awe_get_appid();
	if( $appid ) $appid_string = '&APPID=' . $appid;

	$weather_data['current'] 	= array();
	$weather_data['forecast']	= array();
		
	// CURRENT WEATHER
	$now_ping 			= AWESOME_WEATHER_OWM_API_URL . 'weather?' . $api_query . '&lang=' . $weather->locale . '&units=' . $units_query . $appid_string;
	$now_ping_get 		= wp_remote_get( $now_ping );
	
	if( is_wp_error( $now_ping_get ) ) 
	{
		return array('error' => true, 'msg' => $now_ping_get->get_error_message() );
	}	
		
	$city_data = json_decode( $now_ping_get['body'] );
		
	if( isset($city_data->cod) AND $city_data->cod == 404 )
	{
		return array('error' => true, 'msg' => $city_data->message ); 
	}
	else
	{
		// SET NAME
		if( isset($city_data->name) ) $weather_data['name'] = $city_data->name;
		
		
		// MAIN
		if( isset($city_data->main) AND $city_data->main )
		{
			$weather_data['current']['temp'] 		= round($city_data->main->temp);
			$weather_data['current']['high'] 		= round($city_data->main->temp_max);
			$weather_data['current']['low'] 		= round($city_data->main->temp_min);
			$weather_data['current']['humidity'] 	= round($city_data->main->humidity);
			$weather_data['current']['pressure'] 	= round($city_data->main->pressure);
			
			// CITY ID
			if( $city_data->id ) $weather->owm_city_id = $city_data->id;
		}
		
		// SYS
		if( isset($city_data->sys) AND $city_data->sys )
		{
			$weather_data['current']['sunrise'] = $city_data->sys->sunrise;
			$weather_data['current']['sunrise_time'] = get_date_from_gmt( date('Y-m-d H:i:s', $city_data->sys->sunrise), get_option('time_format') );
			
			$weather_data['current']['sunset'] = $city_data->sys->sunset;
			$weather_data['current']['sunset_time'] = get_date_from_gmt( date('Y-m-d H:i:s', $city_data->sys->sunset), get_option('time_format') );
		}

		// WIND
		if( isset($city_data->wind) AND $city_data->wind )
		{
			if(!isset($city_data->wind->speed)) $city_data->wind->speed = 0;
			if(!isset($city_data->wind->deg)) $city_data->wind->deg = 0;
			
			$wind_speed 		= apply_filters('awesome_weather_wind_speed', round($city_data->wind->speed));
			$wind_direction 	= apply_filters('awesome_weather_wind_direction', fmod((($city_data->wind->deg + 11) / 22.5),16));
			$wind_speed_text 	= __('m/s', 'awesome-weather');
			
			$weather_data['current']['wind_speed'] 					= $wind_speed;		
			$weather_data['current']['wind_direction'] 				= $awe_wind_label[ $wind_direction ];
			$weather_data['current']['wind_direction_number'] 		= $wind_direction;
			$weather_data['current']['wind_speed_text'] 			= apply_filters('awesome_weather_wind_speed_text', $wind_speed_text);
		}
		
		// WEATHER
		if( isset($city_data->weather[0]) AND $city_data->weather[0] )
		{
			$current_weather_details 					= $city_data->weather[0];
			$weather_data['current']['condition_code'] 	= $current_weather_details->id;
			$weather_data['current']['icon'] 			= awesome_weather_get_icon_from_id_openweathermaps($current_weather_details->id);
			
			if( isset($weather->use_custom_translation) AND $weather->use_custom_translation )
			{
				$weather_data['current']['description'] = awesome_weather_get_desc_from_id_openweathermaps($current_weather_details->id);
			}
			else
			{
				$weather_data['current']['description']	= $current_weather_details->description;
			}
		}
	}
		
		
	// FORECAST
	if( $weather->forecast_days > 0 OR $weather->forecast_days != 'hide' )
	{
		if( $weather->forecast_days > 5 )
		{
			$forecast_ping = AWESOME_WEATHER_OWM_API_URL . 'forecast/daily?' . $api_query . '&lang=' . $weather->locale . '&units=' . $units_query . '&cnt=' . ($weather->forecast_days + 2) . $appid_string;
		}
		else
		{
			$forecast_ping = AWESOME_WEATHER_OWM_API_URL . 'forecast?' . $api_query . '&lang=' . $weather->locale . '&units=' . $units_query . $appid_string;
		}
		
		$forecast_ping_get = wp_remote_get( $forecast_ping );
		if( is_wp_error( $forecast_ping_get ) ) 
		{
			return array('error' => true, 'msg' => $forecast_ping_get->get_error_message() );
		}	
		
		$forecast_data = json_decode( $forecast_ping_get['body'] );
		
		if( isset($forecast_data->cod) AND $forecast_data->cod == 404 )
		{
			return array('error' => true, 'msg' => $forecast_data->message ); 
		}
		else if( isset($forecast_data->list) )
		{
			$forecast = array();
			$forecast_items = (array) $forecast_data->list;
			
			// HOURLY FORECAST DATA
			if( $weather->forecast_days < 6 )
			{
				foreach( $forecast_items as $forecast_item ) 
				{
					awesome_weather_build_hourly_forecast( $weather, $forecast, $forecast_item, $forecast_items );
				}
			}
			else
			{
				// DAILY FORECAST DATA
				foreach( $forecast_items as $forecast_item ) 
				{
					$day = awesome_weather_build_daily_forecast( $weather, $forecast_item );
					if( $day ) $forecast[] = $day;
				}	
			}

			$forecast = array_slice( $forecast, 0, $weather->forecast_days );
			$weather_data['forecast'] = $forecast;
		}
	}	
	
	// SET THE TRANSIENT, CACHE FOR 30 MINUTES
	if( $weather_data['current'] AND $weather_data['forecast'] )
	{
		set_transient( $transient_name, $weather_data, apply_filters( 'awesome_weather_cache', 1800 ) ); 
	}
	
	return $weather_data;
}


function awesome_weather_build_hourly_forecast( &$weather, &$forecast, $forecast_item, $forecast_items )
{
	$dt_today 		= date( 'Ymd', current_time( 'timestamp', 0 ) );
	$day_daystamp 	= date( 'Ymd', $forecast_item->dt );
	
	// NO MAIN TEMP, SKIP
	if( !isset($forecast_item->main->temp) ) return false; 
	
	// DAYS OF WEEK
	$days_of_week = apply_filters( 'awesome_weather_days_of_week', array( __('Sun' ,'awesome-weather'), __('Mon' ,'awesome-weather'), __('Tue' ,'awesome-weather'), __('Wed' ,'awesome-weather'), __('Thu' ,'awesome-weather'), __('Fri' ,'awesome-weather'), __('Sat' ,'awesome-weather') ) );
	
	// WIND LABELS
	$awe_wind_label = array ( __('N', 'awesome-weather'),__('NNE', 'awesome-weather'),__('NE', 'awesome-weather'),__('ENE', 'awesome-weather'),__('E', 'awesome-weather'),__('ESE', 'awesome-weather'),__('SE', 'awesome-weather'),__('SSE', 'awesome-weather'),__('S', 'awesome-weather'),__('SSW', 'awesome-weather'),__('SW', 'awesome-weather'),__('WSW', 'awesome-weather'),__('W', 'awesome-weather'),__('WNW', 'awesome-weather'),__('NW', 'awesome-weather'),__('NNW', 'awesome-weather') );			
	
	
	// COMPARE WITH PREVIOUS HOURS
	if( isset($forecast[$day_daystamp]) )
	{
		// UPDATE LOWS AND HIGHS BASED ON WHAT IS GREATER
		if( $forecast[$day_daystamp]->temp < $forecast_item->main->temp ) $forecast[$day_daystamp]->temp = round($forecast_item->main->temp);
		if( $forecast[$day_daystamp]->high < $forecast_item->main->temp_max ) $forecast[$day_daystamp]->high = round($forecast_item->main->temp_max);
		if( $forecast[$day_daystamp]->low > $forecast_item->main->temp_min ) $forecast[$day_daystamp]->low = round($forecast_item->main->temp_min);
		if( $forecast[$day_daystamp]->pressure < $forecast_item->main->pressure ) $forecast[$day_daystamp]->pressure = round($forecast_item->main->pressure);
		if( $forecast[$day_daystamp]->humidity < $forecast_item->main->humidity ) $forecast[$day_daystamp]->humidity = round($forecast_item->main->humidity);
	}
	else
	{
		
		$day = new stdclass;
		$day->timestamp 		= $forecast_item->dt;
		$day->day_of_week 		= $days_of_week[ date('w', $forecast_item->dt) ];
		
		// TEMPS
		$day->temp 				= round($forecast_item->main->temp);
		$day->high 				= round($forecast_item->main->temp_max);
		$day->low 				= round($forecast_item->main->temp_min);
		
		// EXTRAS
		$day->pressure 			= isset($forecast_item->main->pressure) ? round($forecast_item->main->pressure) : false;
		$day->humidity 			= isset($forecast_item->main->humidity) ? round($forecast_item->main->humidity) : false;
		$day->wind_speed 		= isset($forecast_item->wind->speed) ?  $forecast_item->wind->speed : false;
		$day->wind_direction 	= isset($forecast_day->wind->deg) ? $awe_wind_label[ fmod((($forecast_day->wind->deg + 11) / 22.5),16) ]  : false;
		
		// WEATHER DESCRIPTIONS
		if( isset($forecast_item->weather[0]) )
		{
			$w = $forecast_item->weather[0];
			$day->condition_code = $w->id;
			if( isset($weather->use_custom_translation) AND $weather->use_custom_translation )
			{
				$day->description = awesome_weather_get_desc_from_id_openweathermaps($w->id);
			}
			else
			{
				$day->description = $w->description;
			}	
							
			$day->icon = awesome_weather_get_icon_from_id_openweathermaps($w->id);
		}
		
		$forecast[$day_daystamp] = $day;
	}
}


function awesome_weather_build_daily_forecast( &$weather, $forecast_item )
{
	$dt_today 		= date( 'Ymd', current_time( 'timestamp', 0 ) );
	$day_daystamp 	= date('Ymd', $forecast_item->dt);

	// IF DATE IS IN THE PAST, SKIP
	if( $dt_today >= $day_daystamp) return false; 
	
	// NO MAIN TEMP, SKIP
	if( !isset($forecast_item->temp) ) return false; 
	
	// DAYS OF WEEK
	$days_of_week = apply_filters( 'awesome_weather_days_of_week', array( __('Sun' ,'awesome-weather'), __('Mon' ,'awesome-weather'), __('Tue' ,'awesome-weather'), __('Wed' ,'awesome-weather'), __('Thu' ,'awesome-weather'), __('Fri' ,'awesome-weather'), __('Sat' ,'awesome-weather') ) );
	
	// WIND LABELS
	$awe_wind_label = array ( __('N', 'awesome-weather'),__('NNE', 'awesome-weather'),__('NE', 'awesome-weather'),__('ENE', 'awesome-weather'),__('E', 'awesome-weather'),__('ESE', 'awesome-weather'),__('SE', 'awesome-weather'),__('SSE', 'awesome-weather'),__('S', 'awesome-weather'),__('SSW', 'awesome-weather'),__('SW', 'awesome-weather'),__('WSW', 'awesome-weather'),__('W', 'awesome-weather'),__('WNW', 'awesome-weather'),__('NW', 'awesome-weather'),__('NNW', 'awesome-weather') );			
	
	$day = new stdclass;
	$day->timestamp 		= $forecast_item->dt;
	$day->day_of_week 		= $days_of_week[ date('w', $forecast_item->dt) ];
		
	// TEMPS
	$day->temp 				= round($forecast_item->temp->day);
	$day->high 				= round($forecast_item->temp->max);
	$day->low 				= round($forecast_item->temp->min);
	$day->night 			= round($forecast_item->temp->night);
	$day->evening 			= round($forecast_item->temp->eve);
	$day->morning 			= round($forecast_item->temp->morn);
	
	// EXTRAS
	$day->pressure 			= isset($forecast_item->pressure) ? round($forecast_item->pressure) : false;
	$day->humidity 			= isset($forecast_item->humidity) ? round($forecast_item->humidity) : false;
	$day->wind_speed 		= isset($forecast_item->speed) ? round($forecast_item->speed) : false;
	$day->wind_direction 	= isset($forecast_item->deg) ? $awe_wind_label[ fmod((($forecast_item->deg + 11) / 22.5),16) ]  : false;
	
	// WEATHER DESCRIPTIONS
	$day->condition_code = $day->description = $day->icon = false;
	if( isset($forecast_item->weather[0]) )
	{
		$w = $forecast_item->weather[0];
		$day->condition_code = $w->id;
		if( isset($weather->use_custom_translation) AND $weather->use_custom_translation )
		{
			$day->description = awesome_weather_get_desc_from_id_openweathermaps($w->id);
		}
		else
		{
			$day->description = $w->description;
		}	
						
		$day->icon = awesome_weather_get_icon_from_id_openweathermaps($w->id);
	}
				
	return $day;				
}




// WEATHER DESCRIPTION MAPPING
function awesome_weather_get_desc_from_id_openweathermaps($c)
{
	$codes = awesome_weather_condition_code_descriptions();
	
	// THUNDERSTORMS
	if($c == 210) { return $codes['isolated-thunderstorms']; }
	if($c == 212) { return $codes['severe-thunderstorms']; }
	if($c >= 200 AND $c < 300)  { return $codes['thunderstorms']; }
	
	// DRIZZLE
	if($c >= 300 AND $c < 400) { return $codes['drizzle']; }
	
	// RAIN
	if($c == 501) { return $codes['scattered-showers']; }
	if($c == 511) { return $codes['freezing-rain']; }
	if($c >= 500 AND $c < 600) { return $codes['showers']; }
	
	// SNOW
	if($c == 600) { return $codes['light-snow-showers']; }
	if($c == 602) { return $codes['heavy-snow']; }
	if($c == 611) { return $codes['sleet']; }
	if($c == 621) { return $codes['snow-showers']; }
	if($c >= 600 AND $c < 700) { return $codes['snow']; }
	
	// ATMOSPHERE
	if($c == 701) { return __('mist', 'awesome-weather'); }
	if($c == 711) { return $codes['smoky']; }
	if($c == 721) { return $codes['haze']; }
	if($c == 731) { return $codes['dust']; }
	if($c == 741) { return $codes['foggy']; }
	if($c == 751) { return __('sand', 'awesome-weather'); }
	if($c == 761) { return __('dust', 'awesome-weather'); }
	if($c == 762) { return __('volcanic ash', 'awesome-weather'); }
	if($c == 771) { return __('squalls', 'awesome-weather'); }
	if($c == 781) { return $codes['tornado']; }
	
	// CLOUDS
	if($c == 800) { return $codes['clear']; }
	if($c == 801 OR $c == 802 OR $c == 803) { return $codes['partly-cloudy']; }
	if($c == 804) { return $codes['mostly-cloudy']; }
	
	// EXTREME
	if($c == 900) { return $codes['tornado']; }
	if($c == 901) { return $codes['tropical-storm']; }
	if($c == 902) { return $codes['hurricane']; }
	if($c == 903) { return $codes['cold']; }
	if($c == 904) { return $codes['hot']; }
	if($c == 905) { return $codes['windy']; }
	if($c == 906) { return $codes['hail']; }
	
	// ADDITIONAL
	if($c == 951) { return $codes['calm']; }
	if($c == 952 OR $c == 953 OR $c == 954 OR $c == 955 OR $c == 956) { return $codes['breeze']; }
	if($c == 957 OR $c == 958 OR $c == 959) { return $codes['windy']; }
	if($c == 960) { return __('storm', 'awesome-weather'); }
	if($c == 961) { return __('violent storm', 'awesome-weather'); }
	if($c == 962) { return $codes['hurricane']; }
	
	return '';
}


// WEATHER ICONS MAPPING
function awesome_weather_get_icon_from_id_openweathermaps($c)
{
	return "wi wi-owm-$c";
}


// PRESET WEATHER BACKGROUND NAMES
function awesome_weather_preset_condition_names_openweathermaps( $weather_code )
{
	if( substr($weather_code,0,1) == '2' ) 										return 'thunderstorm';
	else if( substr($weather_code,0,1) == '3' ) 								return 'drizzle';
	else if( substr($weather_code,0,1) == '5' ) 								return 'rain';
	else if( $weather_code == 611 OR $weather_code == 612 ) 					return 'sleet';
	else if( substr($weather_code,0,1) == '6' OR $weather_code == 903 ) 		return 'snow';
	else if( $weather_code == 781 OR $weather_code == 900 ) 					return 'tornado';
	else if( $weather_code == 800 OR $weather_code == 904 ) 					return 'sunny';
	else if( substr($weather_code,0,1) == '7' ) 								return 'atmosphere';
	else if( substr($weather_code,0,1) == '8' ) 								return 'cloudy';
	else if( $weather_code == 901 ) 											return 'tropical-storm';
	else if( $weather_code == 902 OR $weather_code == 962 ) 					return 'hurricane';
	else if( $weather_code == 905 ) 											return 'windy';
	else if( $weather_code == 906 ) 											return 'hail';
	else if( $weather_code == 951 ) 											return 'calm';
	else if( $weather_code > 951 ) 												return 'breeze';
	return "default";
}


// GET WOEID BY LOCATION, CACHE IF FOUND
function awesome_weather_get_owm_city_id( $location, $units_query = 'metric' )
{
	// TRANSIENT NAME
	$transient_name = 'awe_local_owm_' . sanitize_title( $location );
	
	// CLEAR TRANSIENT
	if( isset($_GET['clear_awesome_widget']) ) delete_transient( $transient_name );
	
	if( get_transient( $transient_name ) )
	{
		return get_transient( $transient_name );
	}
	else
	{
		// PING FOR FIRST RESULT
		$owm_city_data = awe_ping_owm_first_results( $location, $units_query );
		if( $owm_city_data AND isset($owm_city_data->id) AND $owm_city_data->id )
		{
			set_transient( $transient_name, $owm_city_data->id, apply_filters( 'awesome_weather_ip_cache', 15552000 ) ); 
			return $owm_city_data->id;
		}
	}
	return false;
}


// GET WEATHER DATA FROM OPENWEAHTHERMAPS
function get_awesome_weather_openweathermaps( $where )
{
	// IF NUMERIC
	if( is_numeric( $where ) )
	{
		$w = new stdclass;
		$w->owm_city_id = $where;
		$data = awesome_weather_get_weather_data_openweathermaps( $w );
		if( !isset($data['error']) ) 
		{
			$w->data = $data;
			return $w;
		}
		return false;
	}
	
	// IF STRING
	if( is_string( $where ) )
	{
		$w = new stdclass;
		$w->location = $where;
		$data = awesome_weather_get_weather_data_openweathermaps( $w );
		if( !isset($data['error']) ) 
		{
			$w->data = $data;
			return $w;
		}
		return false;
	}
	
	
	// IF OBJECT
	if( is_object( $where ) )
	{
		$data = awesome_weather_get_weather_data_openweathermaps( $where );
		if( !isset($data['error']) ) 
		{
			$where->data = $data;
			return $where;
		}
		return false;
	}
	
	
	// IF ARRAY
	if( is_array( $where ) )
	{
		$locals = array();
		
		foreach( $where as $local )
		{
			// NUMBER
			if( is_numeric( $local ) )
			{
				$w = new stdclass;
				$w->owm_city_id = $local;
				$data = awesome_weather_get_weather_data_openweathermaps( $w );
				if( !isset($data['error']) ) 
				{
					$w->data = $data;
					$locals[] = $w;
					continue;
				}
			}
			
			// STRING
			if( is_string( $local ) )
			{
				$w = new stdclass;
				$w->location = $local;
				$data = awesome_weather_get_weather_data_openweathermaps( $w );
				if( !isset($data['error']) ) 
				{
					$w->data = $data;
					$locals[] = $w;
					continue;
				}
			}

			// OBJECT
			if( is_object( $local ) )
			{
				$data = awesome_weather_get_weather_data_openweathermaps( $local );
				if( !isset($data['error']) ) 
				{
					$local->data = $data;
					$locals[] = $local;
					continue;
				}
			}
		}
		return $locals;
	}

	return false;
}

function awesome_weather_widget_by_latlong_openweathermaps( $lat, $lon, $template = 'wide', $options = array() )
{
	return get_awesome_weather_widget(  array_merge( array( 'provider' => 'openweathermaps', 'latlng' => $lat . ',' . $lon, 'template' => $template), $options) );
}

function awesome_weather_by_latlong_openweathermaps( $lat, $lon, $options = array() )
{
	$l = new stdclass;
	$l->provider 		= 'openweathermaps';
	$l->latlng 			= $lat . ',' . $lon;
	$obj = (object) array_merge((array) $l, (array) $options);
	return get_awesome_weather_openweathermaps( $obj );
}
