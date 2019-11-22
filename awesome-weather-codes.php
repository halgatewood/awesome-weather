<?php

// GET ALL CONDITION CODES
function awesome_weather_condition_code_descriptions()
{
	return apply_filters('awesome_weather_codes', array(
		'tornado' 					=> __('tornado', 'awesome-weather'),
		'tropical-storm' 			=> __('tropical storm', 'awesome-weather'),
		'hurricane' 				=> __('hurricane', 'awesome-weather'),
		'severe-thunderstorms' 		=> __('severe thunderstorms', 'awesome-weather'),
		'thunderstorms'				=> __('thunderstorms', 'awesome-weather'),
		'thundershowers'			=> __('thundershowers', 'awesome-weather'),
		'mixed-rain-snow'			=> __('mixed rain and snow', 'awesome-weather'),
		'mixed-rain-sleet' 			=> __('mixed rain and sleet', 'awesome-weather'),
		'mixed-snow-sleet' 			=> __('mixed snow and sleet', 'awesome-weather'),
		'mixed-rain-hail' 			=> __('mixed rain and hail', 'awesome-weather'),
		'freezing-drizzle'			=> __('freezing drizzle', 'awesome-weather'),
		'drizzle'					=> __('drizzle', 'awesome-weather'),
		'freezing-rain' 			=> __('freezing rain', 'awesome-weather'),
		'showers' 					=> __('showers', 'awesome-weather'),
		'scattered-showers' 		=> __('scattered showers', 'awesome-weather'),
		'snow-flurries' 			=> __('snow flurries', 'awesome-weather'),
		'light-snow-showers' 		=> __('light snow showers', 'awesome-weather'),
		'blowing-snow' 				=> __('blowing snow', 'awesome-weather'),
		'snow' 						=> __('snow', 'awesome-weather'),
		'scattered-snow'			=> __('scattered snow showers', 'awesome-weather'),
		'heavy-snow' 				=> __('heavy snow', 'awesome-weather'),
		'snow-showers' 				=> __('snow showers', 'awesome-weather'),
		'hail' 						=> __('hail', 'awesome-weather'),
		'sleet' 					=> __('sleet', 'awesome-weather'),
		'dust' 						=> __('dust', 'awesome-weather'),
		'foggy' 					=> __('foggy', 'awesome-weather'),
		'haze' 						=> __('haze', 'awesome-weather'),
		'windy' 					=> __('windy', 'awesome-weather'),
		'cold' 						=> __('cold', 'awesome-weather'),
		'hot' 						=> __('hot', 'awesome-weather'),
		'cloudy' 					=> __('cloudy', 'awesome-weather'),
		'smoky' 					=> __('smoky', 'awesome-weather'),
		'mostly-cloudy' 			=> __('mostly cloudy', 'awesome-weather'),
		'partly-cloudy' 			=> __('partly cloudy', 'awesome-weather'),
		'clear' 					=> __('clear', 'awesome-weather'),
		'sunny' 					=> __('sunny', 'awesome-weather'),
		'fair' 						=> __('fair', 'awesome-weather'),
		'isolated-thunderstorms' 	=> __('isolated thunderstorms', 'awesome-weather'),
		'scattered-thunderstorms' 	=> __('scattered thunderstorms', 'awesome-weather'),
		'calm' 						=> __('calm', 'awesome-weather'),
		'breeze' 					=> __('breezy', 'awesome-weather')
	));
}