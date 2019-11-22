<?php


function awe_ds_deg_to_compass( $num ) 
{
    $val = floor(($num / 22.5) + 0.5);
    $awe_wind_label = array ( __('N', 'awesome-weather'),__('NNE', 'awesome-weather'),__('NE', 'awesome-weather'),__('ENE', 'awesome-weather'),__('E', 'awesome-weather'),__('ESE', 'awesome-weather'),__('SE', 'awesome-weather'),__('SSE', 'awesome-weather'),__('S', 'awesome-weather'),__('SSW', 'awesome-weather'),__('SW', 'awesome-weather'),__('WSW', 'awesome-weather'),__('W', 'awesome-weather'),__('WNW', 'awesome-weather'),__('NW', 'awesome-weather'),__('NNW', 'awesome-weather') );
    return $awe_wind_label[($val % 16)];
}


function awesome_weather_get_weather_data_darksky( &$weather )
{
	// WE NEED AN OBJECT TO WORK WITH
	if( !is_object($weather) ) $weather = (object) $weather;
	
	// API KEY
	$key = awe_get_darksky_key();
	$units_query = 'auto';
	
	// WE NEED A LAT AND LONG
	if( $weather->latlng == '' AND isset($weather->location) AND $weather->location != '')
	{
		// CHECK FOR GEOCODER
		$weather->latlng = awe_get_latlng( $weather->location );
	}
	
	if( !$weather->latlng OR $weather->latlng == '' )
	{
		return array('error' => true, 'msg' => __('Weather Location Not Set', 'awesome-weather') );
	}
	
	// TRANSIENT NAME
	$transient_main = sanitize_title($weather->latlng);
	
	
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
	$transient_name = 'awe_ds_' . $transient_main . $units_query . $weather->locale . '-f' . $weather->forecast_days;	

		
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
	
	$latlng = awesome_weather_parse_lat_lon( $weather->latlng );
	
	// CURRENT WEATHER
	$now_ping 			= AWESOME_WEATHER_DARKSKY_API_URL . 'forecast/' . $key . '/' . $latlng->lat . ',' . $latlng->lng . '?lang=' . $weather->locale . '&units=' . $units_query . '&timezone=' . get_option('timezone_string');
	$now_ping_get 		= wp_remote_get( $now_ping );
	
	if( is_wp_error( $now_ping_get ) ) 
	{
		return array('error' => true, 'msg' => $now_ping_get->get_error_message() );
	}	
	
	$city_data = json_decode( $now_ping_get['body'] );
	
	
	// UNITS
	if( isset($city_data->flags) AND isset($city_data->flags->units) )
	{
		$weather->units = $city_data->flags->units;
	}
	
	// CURRENT
	if( isset($city_data->currently) )
	{
		$weather_data['current']['temp'] 		= round($city_data->currently->temperature);
		$weather_data['current']['humidity'] 	= $city_data->currently->humidity * 100;
		$weather_data['current']['pressure'] 	= round($city_data->currently->pressure);
		
		
		// TODAY
		if( isset($city_data->daily->data) AND isset($city_data->daily->data[0]) )
		{
			$today = $city_data->daily->data[0];
			$weather_data['current']['sunrise'] 			= $today->sunriseTime;
			$weather_data['current']['sunrise_time'] 		= get_date_from_gmt( date('Y-m-d H:i:s', $today->sunriseTime), get_option('time_format') );
			$weather_data['current']['sunset'] 				= $today->sunsetTime;
			$weather_data['current']['sunset_time'] 		= get_date_from_gmt( date('Y-m-d H:i:s', $today->sunsetTime), get_option('time_format') );

			if( $weather->units == 'ca' )
			{
				$weather_data['current']['wind_speed_text'] = apply_filters('awesome_weather_wind_speed_text', __('km/h', 'awesome-weather'));
			}
			else if ( $weather->units == 'uk2' OR $weather->units == 'us' )
			{
				$weather_data['current']['wind_speed_text'] = apply_filters('awesome_weather_wind_speed_text', __('mph', 'awesome-weather'));
			}
			else
			{
				$weather_data['current']['wind_speed_text'] = apply_filters('awesome_weather_wind_speed_text', __('m/s', 'awesome-weather'));
			}

			$weather_data['current']['wind_speed'] 					= round($today->windSpeed);
			$weather_data['current']['wind_direction'] 				= awe_ds_deg_to_compass( $today->windBearing );
			$weather_data['current']['wind_direction_number'] 		= $today->windBearing;
			
			$weather_data['current']['description'] 				= $city_data->currently->summary;
			$weather_data['current']['icon']						= 'wi wi-forecast-io-' . $today->icon;
			$weather_data['current']['condition_code']				= $today->icon;
			
			$weather_data['current']['low']							= round($today->temperatureLow);
			$weather_data['current']['high']						= round($today->temperatureHigh);
		}
		
		
		// FORECAST
		if( $weather->forecast_days > 0 OR $weather->forecast_days != 'hide' )
		{
			$forecast = array();
			foreach( $city_data->daily->data as $forecast_item )
			{
				$day = awesome_weather_build_daily_forecast_darksky( $weather, $forecast_item );
				if( $day ) $forecast[] = $day;
			}
			
			$forecast 						= array_slice( $forecast, 0, $weather->forecast_days );
			$weather_data['forecast'] 		= $forecast;
		}
		
		// TRANSIENT
		set_transient( $transient_name, $weather_data, apply_filters( 'awesome_weather_cache', 1800 ) );
	}

	return $weather_data;
}



function awesome_weather_build_daily_forecast_darksky( &$weather, $forecast_item )
{
	$dt_today 		= date( 'Ymd', current_time( 'timestamp', 0 ) );
	$day_daystamp 	= date('Ymd', $forecast_item->time);

	// IF DATE IS IN THE PAST, SKIP
	if( $dt_today >= $day_daystamp) return false; 
	
	// DAYS OF WEEK
	$days_of_week = apply_filters( 'awesome_weather_days_of_week', array( __('Sun' ,'awesome-weather'), __('Mon' ,'awesome-weather'), __('Tue' ,'awesome-weather'), __('Wed' ,'awesome-weather'), __('Thu' ,'awesome-weather'), __('Fri' ,'awesome-weather'), __('Sat' ,'awesome-weather') ) );

	$day = new stdclass;
	$day->timestamp 		= $forecast_item->time;
	$day->day_of_week 		= $days_of_week[ date('w', $forecast_item->time) ];
		
	// TEMPS
	$day->temp 				= round($forecast_item->temperatureHigh);
	$day->high 				= round($forecast_item->temperatureHigh);
	$day->low 				= round($forecast_item->temperatureLow);
	
	// EXTRAS
	$day->pressure 			= $forecast_item->pressure;
	$day->humidity 			= $forecast_item->humidity * 100;

	if( $weather->units == 'ca' )
	{
		$wind_speed_text = apply_filters('awesome_weather_wind_speed_text', __('km/h', 'awesome-weather'));
	}
	else if ( $weather->units == 'uk2' OR $weather->units == 'us' )
	{
		$wind_speed_text = apply_filters('awesome_weather_wind_speed_text', __('mph', 'awesome-weather'));
	}
	else
	{
		$wind_speed_text = apply_filters('awesome_weather_wind_speed_text', __('m/s', 'awesome-weather'));
	}

	$day->wind_speed 		= round($forecast_item->windSpeed);
	$day->wind_speed_text	= $wind_speed_text;
	$day->wind_direction 	= awe_ds_deg_to_compass( $today->windBearing );
	
	$day->icon 				= 'wi wi-forecast-io-' . $forecast_item->icon;
	$day->condition_code 	= $forecast_item->icon;
	$day->description		= $forecast_item->summary;

	return $day;	
}


function awesome_weather_preset_condition_names_darksky( $weather_icon )
{
	if( $weather_icon == 'clear-day' ) 					return 'sunny';
	else if( $weather_icon == 'clear-night' ) 			return 'sunny';
	else if( $weather_icon == 'rain' ) 					return 'rain';
	else if( $weather_icon == 'snow' ) 					return 'snow';
	else if( $weather_icon == 'sleet' ) 				return 'sleet';
	else if( $weather_icon == 'wind' ) 					return 'windy';
	else if( $weather_icon == 'fog' ) 					return 'atmosphere';
	else if( $weather_icon == 'cloudy' ) 				return 'cloudy';
	else if( $weather_icon == 'partly-cloudy-day' ) 	return 'cloudy';
	else if( $weather_icon == 'partly-cloudy-night' ) 	return 'cloudy';
	else if( $weather_icon == 'hail' ) 					return 'hail';
	else if( $weather_icon == 'thunderstorm' ) 			return 'thunderstorm';
	else if( $weather_icon == 'tornado' ) 				return 'tornado';
	return 'default';
}