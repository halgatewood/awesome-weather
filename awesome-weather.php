<?php
/*
Plugin Name: Awesome Weather Widget
Plugin URI: https://halgatewood.com/awesome-weather
Description: A weather widget that actually looks cool
Author: Hal Gatewood
Author URI: https://www.halgatewood.com
Version: 3.0
Text Domain: awesome-weather
Domain Path: /languages

Hi DEVS!
FILTERS AVAILABLE:
https://halgatewood.com/docs/plugins/awesome-weather-widget/available-filters
*/


define( 'AWESOME_WEATHER_LOOKUP_URL', 'https://ipinfo.io/[[IP]]/json' );
define( 'AWESOME_WEATHER_OWM_API_URL', 'https://api.openweathermap.org/data/2.5/' );
define( 'AWESOME_WEATHER_DARKSKY_API_URL', 'https://api.darksky.net/' );
define( 'AWESOME_WEATHER_LOCATIONIQ_API_URL', apply_filters('awesome_weather_locationiq_endpoint', 'https://us1.locationiq.com/v1/') );
define( 'AWESOME_WEATHER_PLUGIN_BASE', plugin_dir_url( __FILE__ ) );

// GLOBAL SETTINGS
$awesome_weather_sizes = apply_filters( 'awesome_weather_sizes' , array( 
																			'tall' => __('Tall', 'awesome-weather'), 
																			'wide' => __('Wide', 'awesome-weather'), 
																			'micro' => __('Micro', 'awesome-weather'), 
																			'showcase' => __('Showcase', 'awesome-weather'), 
																			'long' => __('Long', 'awesome-weather'), 
																			'boxed' => __('Boxed', 'awesome-weather'), 
																			'material' => __('Material', 'awesome-weather'), 
																			'basic' => __('Basic', 'awesome-weather'), 
																			'custom' => __('Custom', 'awesome-weather') ) );


// INCLUDES
require_once( dirname(__FILE__) . '/providers/openweathermaps.php' );
require_once( dirname(__FILE__) . '/providers/darksky.php' );
include_once( dirname(__FILE__) . '/awesome-weather-codes.php');
include_once( dirname(__FILE__) . '/awesome-weather-mediabox.php');



// SETUP
function awesome_weather_setup()
{
	$locale = apply_filters('plugin_locale', get_locale(), 'awesome-weather');	
    $mofile = WP_LANG_DIR . '/awesome-weather/awesome-weather-' . $locale . '.mo';
 
    if( file_exists( $mofile ) )
    {
        load_textdomain( 'awesome-weather', $mofile );
    }
    else
    {
        load_plugin_textdomain( 'awesome-weather', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }
	
	if( is_admin() )
	{
		add_action(	'admin_menu', 'awesome_weather_setting_page_menu' );
	}
}
add_action('plugins_loaded', 'awesome_weather_setup', 99999, 0);



// ENQUEUE CSS
function awesome_weather_wp_head() 
{
	wp_enqueue_script( 'awesome_weather', plugin_dir_url( __FILE__ ) . 'js/awesome-weather-widget-frontend.js', array('jquery'), '1.1', add_filter('awesome_weather_js_in_footer', false) );
	wp_enqueue_style( 'awesome-weather', plugins_url( '/awesome-weather.css', __FILE__ ) );
	
	$use_google_font 			= apply_filters('awesome_weather_use_google_font', true);
	$google_font_queuename 		= apply_filters('awesome_weather_google_font_queue_name', 'opensans-googlefont');
	
	if( $use_google_font )
	{
		wp_enqueue_style( $google_font_queuename, '//fonts.googleapis.com/css?family=Open+Sans:400' );
		wp_add_inline_style( 'awesome-weather', ".awesome-weather-wrap { font-family: 'Open Sans', sans-serif; font-weight: 400; font-size: 14px; line-height: 14px; }" );
	}
}
add_action('wp_enqueue_scripts', 'awesome_weather_wp_head');


// THE SHORTCODE
add_shortcode( 'awesome-weather', 'awesome_weather_shortcode' );
add_shortcode( 'awesome-weather-ajax', 'awesome_weather_ajax' );
function awesome_weather_shortcode( $atts )
{
	$run_as_ajax = true;
	if( isset($atts['ajax']) )
	{
		if( !$atts['ajax'] ) $run_as_ajax = false;
		else if( strtolower($atts['ajax']) == 'false') $run_as_ajax = false;
		else if( $atts['ajax'] == "0") $run_as_ajax = false;
	}
	
	if( $run_as_ajax )
	{
		return awesome_weather_ajax( $atts );
	}
	else
	{
		return awesome_weather_logic( $atts );
	}	
}

function awesome_weather_ajax( $atts )
{
	$atts = (object) $atts;
	
	$inline_style = isset($atts->inline_style_wrap) ? $atts->inline_style_wrap : '';
	
	// HIDE LOADER
	$show_loader = true;
	if( isset($atts->loader) AND (!$atts->loader OR $atts->loader == 'false') ) $show_loader = false;
	
	// GET AN ID, SO WE KNOW WHERE TO LOAD
	if( !isset($atts->id) OR !$atts->id ) $atts->id= awe_widget_id( $atts, true );
	
	// AJAX TO PING
	$atts->ajaxurl = admin_url('admin-ajax.php');
	$atts->via_ajax = 1;
	
	// OUTPUT A WRAPPER
	ob_start();
	
	echo '<div id="awe-ajax-' . $atts->id .'" class="awe-ajax-' . $atts->id .' awe-weather-ajax-wrap" style="' . $inline_style .'">';
	if( $show_loader ) echo '<div class="awe-loading"><i class="wi ' . apply_filters('awesome_weather_loader', 'wi-day-sunny') . '"></i></div>';
	echo '</div>';
	echo '<script type="text/javascript"> jQuery(document).ready(function() { awe_ajax_load(' . json_encode($atts) . '); }); </script>';
	
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

// RETURN WEATHER WIDGET, ACF STYLE
function get_awesome_weather_widget( $atts )
{
	if( is_string($atts) )
	{
		$local = $atts;
		$atts = array();
		$atts['location'] = $local;
	}
	else if ( is_object($atts) )
	{
		$atts = (array) $atts;
	}
	else
	{
		if( !is_array($atts)) $atts = (array) $atts;
	}

	return awesome_weather_logic( $atts );	
}


// DISPLAY WEATHER WIDGET, ACF STYLE
function the_awesome_weather_widget( $atts )
{
	if( is_string($atts) )
	{
		$local = $atts;
		$atts = array();
		$atts['location'] = $local;
	}
	else if ( is_object($atts) )
	{
		$atts = (array) $atts;
	}
	else
	{
		if( !is_array($atts)) $atts = (array) $atts;
	}
	
	echo awesome_weather_logic( $atts );	
}


// THE LOGIC
function awesome_weather_logic( $atts )
{
	global $awesome_weather_sizes;
	
	$add_to_transient					= '';
	$weather 							= new stdclass;
	
	// DEFAULT SETTINGS
	$weather->id 						= isset($atts['id']) ? $atts['id'] : awe_widget_id( $atts, true );
	$weather->error 					= false;
	$weather->location					= isset($atts['location']) ? awesome_weather_prep_location($atts['location']) : '';
	$weather->owm_city_id				= isset($atts['owm_city_id']) ? $atts['owm_city_id'] : 0;
	
	$weather->user_location				= isset($atts['user_location']) ? $atts['user_location'] : '';
	$weather->latlng					= isset($atts['latlng']) ? $atts['latlng'] : '';

	$weather->template 					= isset($atts['size']) ? $atts['size'] : 'wide';
	$weather->template 					= isset($atts['template']) ? $atts['template'] : $weather->template;
	$weather->custom_template_name 		= isset($atts['custom_template_name']) ? $atts['custom_template_name'] : '';
	$weather->inline_style				= isset($atts['inline_style']) ? $atts['inline_style'] : '';

	$weather->units 					= (isset($atts['units']) AND $atts['units'] != '') ? $atts['units'] : '';
	$weather->override_title 			= isset($atts['override_title']) ? $atts['override_title'] : '';
	$weather->forecast_days 			= isset($atts['forecast_days']) ? $atts['forecast_days'] : 5;
	
	$weather->show_stats 				= (isset($atts['hide_stats']) AND $atts['hide_stats'] == 1) ? 0 : 1;
	$weather->hide_stats				= (isset($atts['hide_stats']) AND $atts['hide_stats'] == 1) ? 1 : 0;
	$weather->show_link 				= (isset($atts['show_link']) AND $atts['show_link'] == 1) ? 1 : 0;
	$weather->show_icons 				= (isset($atts['show_icons']) AND $atts['show_icons'] == 1) ? 1 : 0;
	$weather->use_user_location 		= (isset($atts['use_user_location']) AND $atts['use_user_location'] == 1) ? 1 : 0;
	$weather->allow_user_to_change 		= (isset($atts['allow_user_to_change']) AND $atts['allow_user_to_change'] == 1) ? 1 : 0;
	$weather->skip_geolocate 			= (isset($atts['skip_geolocate']) AND $atts['skip_geolocate'] == 1) ? 1 : 0;
	
	$weather->extended_url 				= isset($atts['extended_url']) ? $atts['extended_url'] : '';
	$weather->extended_text 			= isset($atts['extended_text']) ? $atts['extended_text'] : '';
	$weather->show_attribution 			= (isset($atts['hide_attribution']) AND $atts['hide_attribution'] == 1) ? 0 : 1;
	if(isset($atts['hide_attribution']) AND $atts['hide_attribution'] ) $weather->hide_attribution = $atts['hide_attribution'];

	$weather->background_classes 		= isset($atts['background_classes']) ? $atts['background_classes'] : array();
	$weather->background_image 			= isset($atts['background']) ? $atts['background'] : 0;
	$weather->background_image 			= isset($atts['background_image']) ? $atts['background_image'] : $weather->background_image;
	$weather->background_color 			= isset($atts['custom_bg_color']) ? $atts['custom_bg_color'] : 0;
	$weather->background_color 			= isset($atts['background_color']) ? $atts['background_color'] : $weather->background_color;
	$weather->background_by_weather 	= (isset($atts['background_by_weather']) AND $atts['background_by_weather'] == 1) ? 1 : 0;
	$weather->background_by_weather_ext = isset($atts['background_by_weather_ext']) ? $atts['background_by_weather_ext'] : apply_filters('awesome_weather_bg_ext', 'jpg' );
	$weather->text_color				= isset($atts['text_color']) ? $atts['text_color'] : '#ffffff';
	$weather->skip_geolocate 			= isset($atts['skip_geolocate']) ? $atts['skip_geolocate'] : apply_filters('awesome_weather_skip_geolocate', 0 );
	
	
	// PROVIDERS
	$providers							= array('openweathermaps', 'darksky');
	$weather->provider					= in_array( get_option( 'awe-weather-provider' ), $providers ) ? get_option( 'awe-weather-provider' ) : 'openweathermaps';
	if( isset($atts['provider']) AND in_array($atts['provider'], $providers) ) $weather->provider = $atts['provider'];
	
	$weather->data 						= array();
	$weather->locale					= 'en';
	$weather->use_custom_translation	= apply_filters('awesome_weather_use_custom_translation', 0 );
	

	// LOCALE SETTINGS
	$sytem_locale = get_locale();
	$available_locales = awesome_weather_get_locales(); 

    // CHECK FOR LOCALE
    if( in_array( $sytem_locale, $available_locales ) ) $weather->locale = $sytem_locale;
    
    
    // CHECK FOR LOCALE BY FIRST TWO DIGITS
    if( in_array(substr($sytem_locale, 0, 2), $available_locales ) ) $weather->locale = substr($sytem_locale, 0, 2);

	
	// OVERRIDE LOCALE PARAMETER
	if( isset($atts['locale']) ) $weather->locale = $atts['locale'];
	
	
	// A LAST CHANCE TO OVERRIDE THE LOCALE
	$weather->locale = apply_filters('awesome_weather_locale', $weather->locale );
	
	
	// BASIC TEMPLATE TRANSLATIONS
	$weather->t = new stdclass;
	$weather->t->humidity 				= __('humidity', 'awesome-weather');
	$weather->t->high 					= __('H', 'awesome-weather');
	$weather->t->low 					= __('L', 'awesome-weather');
	$weather->t->now 					= __('Now', 'awesome-weather');
	$weather->t->wind 					= __('wind:', 'awesome-weather');
	$weather->t->weather_from 			= __('Weather from', 'awesome-weather');
	$weather->t->set_location 			= __('Set Your Location', 'awesome-weather');
	$weather->t->search_placeholder 	= __('Search: City, State or Country', 'awesome-weather');
	$weather->t->city_not_found 		= __('City not found, please try again.', 'awesome-weather');
	$weather->t 						= apply_filters('awesome_weather_basic_translations', $weather->t, $weather->id);
	
	
	// DEFAULTS WEATHER UNITS
	if( $weather->provider == 'openweathermaps' AND !$weather->units ) 
	{
		$weather->units = 'F';
	}
	else if( $weather->provider == 'darksky' AND !$weather->units )
	{
		$weather->units = 'us';
	}
	
	
	// USE CHANGE LOCATION CODE
	$weather->user_provided 	= false;
	$weather->show_bubble 		= true;
	
	if( $weather->allow_user_to_change )
	{
		// IF GEO TRIGGERED
		if( isset($atts['geotriggered']) AND $weather->latlng )
		{
			setcookie('awe_latlng', $weather->latlng, time() + apply_filters( 'awesome_weather_latlon_cache', 31104000 ), '/', $_SERVER['HTTP_HOST'], is_ssl() ?  true: false );
			
			$weather->units 			= 'auto';
			$weather->show_bubble 		= false;
			$weather->user_provided 	= true; 
	
			unset($atts['geotriggered']);
		}
		else if( $weather->user_location )
		{
			// IF USER SUBMITTED
			
			$weather->location = $weather->user_location;
			$weather->latlng = awe_get_latlng( $weather->user_location );

			if( $weather->latlng )
			{
				$weather->owm_city_id		= 0;
				setcookie('awe_latlng', $weather->latlng, time() + apply_filters( 'awesome_weather_latlon_cache', 31104000 ), '/', $_SERVER['HTTP_HOST'], is_ssl() ?  true: false );
				
				$weather->units 			= 'auto';
				$weather->show_bubble 		= false;
				$weather->user_provided 	= true; 
			}
		}
		else if( isset($_COOKIE['awe_latlng']) AND $_COOKIE['awe_latlng'] )
		{
			$weather->units 			= 'auto';
			$weather->owm_city_id		= 0;
			$weather->latlng 			= $_COOKIE['awe_latlng'];
			$weather->show_bubble 		= false;
			$weather->user_provided 	= true;
		}
	}
			
	// IF NO WEATHER FROM USER, AND WITH CAN USE THE IP
	if( !$weather->user_provided AND $weather->use_user_location )
	{
		$ll_from_ip = awe_latlng_from_ip();
		
		if( $ll_from_ip )
		{
			$weather->units 			= 'auto';
			$weather->owm_city_id		= 0;
			$weather->latlng			= $ll_from_ip;	
			$weather->location 			= false;
			$weather->user_provided 	= true;
		}	
	}
	
	
	// FILTER TO SHOW OR HIDE THE CHANGE YOUR WEATHER BUBBLE
	$weather->show_bubble = apply_filters('awesome_weather_show_bubble', $weather->show_bubble, $weather );
	
	// DEFAULT SETTINGS
	if( !$weather->id ) awe_widget_id( $weather, true );

	// WEATHER DATA
	$weather->data = apply_filters( 'awesome_weather_data', $weather->data, $weather);
	if( !$weather->data )
	{
		// SET THE WEATHER DATA
		if( $weather->provider == 'darksky' )
		{
			$weather->data = awesome_weather_get_weather_data_darksky( $weather );
		}
		else
		{
			$weather->data = awesome_weather_get_weather_data_openweathermaps( $weather );
		}

		// IF ERROR, DISPLAY
		if( isset($weather->data['error']) AND $weather->data['error'] )
		{
			$weather->error = $weather->data['msg'];
		}
	}
	
	// IF USER INITIATED THE WEATHER, GET NEW OVERRIDE TITLE
	if( $weather->user_provided AND isset($weather->data['name']) ) $weather->override_title = $weather->data['name'];


	// BACKGROUND COLORS
	if( $weather->background_color )
	{
		if( substr(trim($weather->background_color), 0, 1) != '#' AND substr(trim(strtolower($weather->background_color)), 0, 3) != 'rgb' AND $weather->background_color != 'transparent' ) { $weather->background_color = '#' . $weather->background_color; }
		$weather->inline_style .= " background-color: {$weather->background_color};";
		$weather->background_classes[] = 'custom-bg-color';
	}
	else if( isset($weather->data['current']) )
	{
		// COLOR OF WIDGET
		$today_temp = $weather->data['current']['temp'];
		if( strtolower($weather->units) == 'f' OR strtolower($weather->units) == 'us' )
		{
			if($today_temp < 31) $weather->background_classes[] = 'temp1';
			if($today_temp > 31 AND $today_temp < 40) $weather->background_classes[] = 'temp2';
			if($today_temp >= 40 AND $today_temp < 50) $weather->background_classes[] = 'temp3';
			if($today_temp >= 50 AND $today_temp < 60) $weather->background_classes[] = 'temp4';
			if($today_temp >= 60 AND $today_temp < 80) $weather->background_classes[] = 'temp5';
			if($today_temp >= 80 AND $today_temp < 90) $weather->background_classes[] = 'temp6';
			if($today_temp >= 90) $weather->background_classes[] = 'temp7';
		}
		else
		{
			if($today_temp < 1) $weather->background_classes[] = 'temp1';
			if($today_temp > 1 AND $today_temp < 4) $weather->background_classes[] = 'temp2';
			if($today_temp >= 4 AND $today_temp < 10) $weather->background_classes[] = 'temp3';
			if($today_temp >= 10 AND $today_temp < 15) $weather->background_classes[] = 'temp4';
			if($today_temp >= 15 AND $today_temp < 26) $weather->background_classes[] = 'temp5';
			if($today_temp >= 26 AND $today_temp < 32) $weather->background_classes[] = 'temp6';
			if($today_temp >= 32) $weather->background_classes[] = 'temp7';
		}
	}

	// HEADER TITLE
	$header_title = $weather->location;

		// IF NOT LOCATION, GET FROM WEATHER DATA
		if( !$weather->location AND isset($weather->data['name']) ) $header_title = $weather->data['name'];

		// OVERRIDE TITLE TAKES THE CAKE
		if( $weather->override_title ) $header_title = $weather->override_title;

	// BACKGROUND CLASSES
	$weather->background_classes[] = 'awesome-weather-wrap';
	$weather->background_classes[] = 'awecf';
	$weather->background_classes[] = ($weather->show_stats) ? 'awe_with_stats' : 'awe_without_stats';
	$weather->background_classes[] = ($weather->show_icons) ? 'awe_with_icons' : 'awe_without_icons';
	$weather->background_classes[] = ($weather->forecast_days == 'hide') ? 'awe_without_forecast' : 'awe_with_forecast';
	$weather->background_classes[] = ($weather->extended_url) ? 'awe_extended' : '';
	$weather->background_classes[] = 'awe_' . $weather->template;
	if( $weather->owm_city_id ) $weather->background_classes[] = 'awe-cityid-' . $weather->owm_city_id;
	
	
	// BACKGROUND IMAGE, ADD DARKEN CLASS
	if( $weather->background_image ) $weather->background_classes[] = 'darken';
	if( $weather->allow_user_to_change ) $weather->background_classes[] = 'awe_changeable';

	
	// WEATHER CONDITION CSS
	$weather_code = $weather_description_slug = ''; 
	if( isset($weather->data['current']) )
	{
		$weather_code = $weather->data['current']['condition_code'];
		$weather_description_slug = sanitize_title( $weather->data['current']['description'] );
		
		$weather->background_classes[] = 'awe-code-' . $weather_code;
		$weather->background_classes[] = 'awe-desc-' . sanitize_title( $weather_description_slug );
	}
	
	
	// CHECK FOR BACKGROUND BY WEATHER
	if( $weather->background_by_weather AND ( $weather_code OR $weather_description_slug ) )
	{
		// DEFAULTS
		$bg_img_names 			= array();
		$bg_ext 				= $weather->background_by_weather_ext;
		$bg_img_location 		= new stdclass;
		$bg_img_location->dir 	= get_stylesheet_directory() . '/awe-backgrounds/';
		$bg_img_location->uri 	= trailingslashit(get_stylesheet_directory_uri()) . 'awe-backgrounds/';
		
		// NAMES TO CHECK FOR
		if( $weather_code ) 				$bg_img_names[] = $weather_code . '.' . $bg_ext;
		if( $weather_description_slug ) 	$bg_img_names[] = $weather_description_slug . '.' . $bg_ext;
		
		// CUSTOM LOCATION
		$custom_template_location = apply_filters('awesome_weather_background_images_location', false );	
		if( $custom_template_location ) $bg_img_location = $custom_template_location;


		// CHECK FOR awesome-weather-bgs
		if( file_exists( dirname(__FILE__) . '/../awesome-weather-bgs/awe-backgrounds/') )
		{
			foreach( $bg_img_names as $bg_img_name )
			{
				if( file_exists( dirname(__FILE__) . '/../awesome-weather-bgs/awe-backgrounds/' . $bg_img_name) )
				{
					$weather->background_image = trailingslashit(plugins_url()) . 'awesome-weather-bgs/awe-backgrounds/' . $bg_img_name;
					break;
				}
			}
		}
		else if( file_exists($bg_img_location->dir) )
		{
			foreach( $bg_img_names as $bg_img_name )
			{
				if( file_exists( $bg_img_location->dir . $bg_img_name) )
				{
					$weather->background_image = $bg_img_location->uri . $bg_img_name;
					break;
				}
			}
		}

		// USE DEFAULT BACKGROUND IMAGES
		if( !$weather->background_image )
		{
			if( $weather->provider == 'darksky' )
			{
				$preset_background_img_name = awesome_weather_preset_condition_names_darksky($weather_code);
			}
			else
			{
				$preset_background_img_name = awesome_weather_preset_condition_names_openweathermaps($weather_code);
			}
			
			if( $preset_background_img_name )
			{
				$weather->background_classes[] = 'awe-preset-' . $preset_background_img_name;
				if( file_exists( trailingslashit(dirname(__FILE__)) . 'img/awe-backgrounds/' . $preset_background_img_name . '.' . $bg_ext) ) $weather->background_image = trailingslashit(AWESOME_WEATHER_PLUGIN_BASE) . 'img/awe-backgrounds/' . $preset_background_img_name . '.' . $bg_ext;
			}
		}
	}
	
	// TEXT COLOR
	if( substr(trim($weather->text_color), 0, 1) != '#' ) $weather->text_color = '#' . $weather->text_color;
	if( $weather->text_color ) $weather->inline_style .= " color: {$weather->text_color}; ";
	
	
	// PREP INLINE STYLE
	$inline_style = "";
	if($weather->inline_style != '') { $inline_style = " style=\"{$weather->inline_style}\""; }
	
	
	// PREP BACKGROUND CLASSES
	$background_classes = @implode( ' ', apply_filters( 'awesome_weather_background_classes', $weather->background_classes ));
	
	
	// CREATE SHORT VARIABLES TO WORK WITH IN TEMPLATES
	$weather_forecast = array();
	if( isset($weather->data['forecast']) ) $weather_forecast = (array) $weather->data['forecast'];
	
	
	// GET TEMPLATE 
	ob_start();
	
	// IF WE HAVE AN ERROR
	if( $weather->error ) return awesome_weather_error($weather->error);
	
	
	// IF USER CAN CHANGE, SET JSON OBJECT OF WEATHER
	if( $weather->allow_user_to_change AND !isset($atts['via_ajax']) )
	{
		$json = clone $weather;
		$json->action = 'awesome_weather_refresh';
		$json->user_location = '0';
		$json->via_ajax = '1';
		$json->data = false; 
		$json->ajaxurl = admin_url('admin-ajax.php');
		echo "<script type=\"text/javascript\">
					if( typeof awe == 'undefined') { var awe = []; }
					awe['awe_weather_widget_json_{$weather->id}'] = " . json_encode($json) . ";
			</script>";
	}
	
	// GET TEMPLATE
	if( $weather->template == 'custom' OR !isset($awesome_weather_sizes[$weather->template]))
	{
		// GET CUSTOM TEMPLATE
		$template = locate_template( array( 'awe-' . $weather->template . '.php' ) );
		
		$user_defined_template_location = apply_filters('awesome_weather_custom_template_location', false);

		if( $user_defined_template_location )
		{
			include( trailingslashit($user_defined_template_location) .  'awe-' . $weather->template . '.php' );
		}
		else if( $template )
		{
			include( $template );
		}
		else
		{
			echo awesome_weather_error( __('Custom template file not found. Please add a file to your theme folder with this name:', 'awesome-weather' ) . ' <span style="text-transform: lowercase">awe-' . $weather->template . '.php</span>' ); 
		}
	}
	else
	{
		$awe_weather_template = dirname(__FILE__) . '/templates/' . $weather->template . '.php';
		
		if( file_exists( $awe_weather_template ) )
		{
			include( $awe_weather_template );
		}
		else
		{
			echo awesome_weather_error( __('Weather template not found:', 'awesome-weather') . ' ' . $weather->template );
		}
	}
	
	// END 
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}


// RETURN ERROR
function awesome_weather_error( $msg = false )
{
	$error_handling = get_option( 'aw-error-handling' );
	if(!$error_handling) $error_handling = 'source';
	if(!$msg) $msg = __('No weather information available', 'awesome-weather');
	
	if( $error_handling == 'display-admin')
	{
		// DISPLAY ADMIN
		if ( current_user_can( 'manage_options' ) ) 
		{
			return "<div class='awesome-weather-error'>" . $msg . "</div>";
		}
	}
	else if( $error_handling == 'display-all')
	{
		// DISPLAY ALL
		return "<div class='awesome-weather-error'>" . $msg . "</div>";
	}
	else
	{
		return apply_filters( 'awesome_weather_error', "<!-- awesome-weather-error: " . $msg . " -->" );
	}
}


// ENQUEUE ADMIN SCRIPTS
function awesome_weather_admin_scripts( $hook )
{
	if( 'widgets.php' != $hook ) return;
	
	wp_enqueue_style('jquery');
	wp_enqueue_style('underscore');
    wp_enqueue_script('awesome_weather_admin_script', plugin_dir_url( __FILE__ ) . '/js/awesome-weather-widget-admin.js', array('jquery', 'underscore') );
	wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    
	wp_localize_script( 'awesome_weather_admin_script', 'awe_script', array(
			'no_owm_city'				=> esc_attr(__('No city found in OpenWeatherMap.', 'awesome-weather')),
			'one_city_found'			=> esc_attr(__('Only one location found. The ID has been set automatically above.', 'awesome-weather')),
			'confirm_city'				=> esc_attr(__('Please confirm your city:', 'awesome-weather'))
		)
	);
}
add_action( 'admin_enqueue_scripts', 'awesome_weather_admin_scripts' );



// WIDGET CODE
require_once(dirname(__FILE__) . '/widget.php');



// CREATE WIDGET ID
function awe_widget_id( &$weather, $rtn = false )
{
	// SANITIZE
	if( !is_object($weather) ) $weather = (object) $weather;
	if( !isset($weather->id) ) $weather->id = false;
	
	// IF WE HAVE A LATLNG, USE IT
	if( !$weather->id AND isset($weather->latlng) AND $weather->latlng != '' ) $weather->id = 'awesome-weather-' . sanitize_title( $weather->latlng );


	// USE LOCATION
	if( !$weather->id AND isset($weather->location) AND $weather->location != '' ) $weather->id = 'awesome-weather-' .  sanitize_title( $weather->location );


	// USE owm_city_id
	if( !$weather->id AND isset($weather->owm_city_id) AND $weather->owm_city_id != '' ) $weather->id = 'awesome-weather-' . $weather->owm_city_id;


	// CREATE RANDOM
	if( !$weather->id ) $weather->id = 'awesome-weather-' .  uniqid();
	

	// FILTER TO DO WHATEVER
	$weather->id = apply_filters('awesome_weather_widget_id', $weather->id, $weather);
	
	
	// RETURN DATA OR ECHO
	if( $rtn ) return $weather->id;
	else echo $weather->id;
}


// GET APPID
function awe_get_appid()
{
	return trim(defined('AWESOME_WEATHER_APPID') ? AWESOME_WEATHER_APPID : get_option( 'open-weather-key' ));
}

// GET DARKSKY KEY
function awe_get_darksky_key()
{
	return trim(defined('AWESOME_WEATHER_DARKSKY_KEY') ? AWESOME_WEATHER_DARKSKY_KEY : get_option( 'darksky-secret-key' ));
}

// GET IPINFO TOKEN
function awe_get_ipinfo_token()
{
	return trim(defined('IPINFO_TOKEN') ? IPINFO_TOKEN : get_option( 'ipinfo-token' ));
}

// GET LOCATIONIQ TOKEN
function awe_get_locationiq_token()
{
	return trim(defined('AWESOME_WEATHER_LOCATIONIQ_TOKEN') ? AWESOME_WEATHER_LOCATIONIQ_TOKEN : get_option( 'location-iq-token' ));
}



// PING OPENWEATHER FOR OWMID
add_action( 'wp_ajax_awe_ping_owm_for_id', 'awe_ping_owm_for_id');
add_action( 'wp_ajax_nopriv_awe_ping_owm_for_id', 'awe_ping_owm_for_id');
function awe_ping_owm_for_id( )
{
	$appid_string = '';
	$appid = awe_get_appid();
	if( $appid ) $appid_string = '&APPID=' . $appid;

	$location = urlencode($_GET['location']);
	$units = strtoupper($_GET['location']) == 'C' ? 'metric' : 'imperial';
	$owm_ping = AWESOME_WEATHER_OWM_API_URL . 'find?q=' . $location . '&units=' . $units . '&mode=json' . $appid_string;
	$owm_ping_get = wp_remote_get( $owm_ping );
	echo $owm_ping_get['body'];
	die;
}

function awe_ping_owm_first_results( $location, $units )
{
	$appid_string = '';
	$appid = awe_get_appid();
	if( $appid ) $appid_string = '&APPID=' . $appid;	
		
	$owm_ping = AWESOME_WEATHER_OWM_API_URL . 'find?q=' . urlencode($location) . '&units=' . $units . '&mode=json' . $appid_string;
	$owm_ping_get = wp_remote_get( $owm_ping );
	$body = json_decode($owm_ping_get['body']);

	if( isset($body->list) AND isset($body->list[0]) )
	{
		return $body->list[0];
	}
	
	return false;
}

add_action( 'wp_ajax_awe_get_latlng_ajax', 'awe_get_latlng_ajax');
add_action( 'wp_ajax_nopriv_awe_get_latlng_ajax', 'awe_get_latlng_ajax' );
function awe_get_latlng_ajax()
{
	$location_iq_token 		= awe_get_locationiq_token();
	$owm_appid 				= awe_get_appid();
	
	header("Content-Type: application/json");

	// NO METHOD OF LOOKUP
	if( !$location_iq_token AND !$owm_appid )
	{
		echo json_encode(array( 'error' => __('No method of lookup available. Please enter an OpenWeatherMap APPID or LocationIQ Token in the Settings.', 'awesome-weather'), 'latlng' => '' ));
		die;
	}
	
	if( isset($_GET['location']) )
	{
		$latlng = awe_get_latlng( $_GET['location'] );
		if( $latlng )
		{
			echo json_encode(array( 'error' => '', 'latlng' => $latlng ));
			die;
		}
		else
		{
			echo json_encode(array( 'error' => __('Location could not be geocoded.', 'awesome-weather'), 'latlng' => '' ));
			die;
		}
	}
	
	echo json_encode(array( 'error' => '', 'latlng' => 0 ));
	die;
}


// GET LAT LONG FROM TEXT
function awe_get_latlng( $text )
{
	$location_iq_token 		= awe_get_locationiq_token();
	$owm_appid 				= awe_get_appid();
	$location_transient 	= 'awe_location_' . sanitize_title($text);
	
	// USER LOCATION IQ
	if( $location_iq_token ) 
	{
		// CHECK FOR CLEAR
		if( isset($_GET['clear_awesome_widget']) ) delete_transient( $location_transient );
		
		// GET CACHED FIRST
		if( get_transient($location_transient) )
		{
			return get_transient($location_transient);
		}
		else
		{
			// PING FOR NEW
			$ping_url = AWESOME_WEATHER_LOCATIONIQ_API_URL . 'search.php?key=' . $location_iq_token . '&q=' . urlencode($text) . '&format=json';
			$lq_ping = wp_remote_get( $ping_url );
	
			if( is_wp_error( $lq_ping ) ) return false;
			$places = json_decode( $lq_ping['body'] );
			if( $places AND is_array($places) )
			{
				$l = reset($places);
				if( isset($l->lat) AND isset($l->lon) )
				{
					set_transient( $location_transient, $l->lat . ',' . $l->lon, apply_filters( 'awesome_weather_latlon_cache', 31104000 ) );
					return $l->lat . ',' . $l->lon;
				}
			}			
		}
	}
	else if ( $owm_appid )
	{
		// CHECK FOR CLEAR
		if( isset($_GET['clear_awesome_widget']) ) delete_transient( $location_transient );
		
		// GET CACHED FIRST
		if( get_transient($location_transient) )
		{
			return get_transient($location_transient);
		}
		else
		{
			// USE OPEN WEATHER MAP
			$first_city = awe_ping_owm_first_results( $text, $units );
			if( $first_city AND isset($first_city->coord) )
			{
				if( isset($first_city->coord->lat) AND isset($first_city->coord->lon) )
				{
					set_transient( $location_transient, $first_city->coord->lat . ',' . $first_city->coord->lon, apply_filters( 'awesome_weather_latlon_cache', 31104000 ) );
					return $first_city->coord->lat . ',' . $first_city->coord->lon;
				}
			}
		}
	}
	
	return false;
}



// GET CITY ID BY LOCATION
function awe_get_location_city_id( $weather, $return = 'id' )
{
	$local = awe_ping_owm_first_results( $weather->location, $weather->units );
	if( isset($local->id) ) return (int) $local->id;
	return false;
}


// GET CITY ID BY LONG LAT
function awe_get_long_lat_city_id( $weather, $what_to_get = 'id' )
{
	$transient_name = 'awe_ll_owm' . $what_to_get . str_replace('-', '', sanitize_title( $weather->latlng ));
	
	// CLEAR IF URL
	if( isset($_GET['clear_awesome_widget']) ) delete_transient( $transient_name );
	if( get_transient( $transient_name ) )
	{
		return get_transient( $transient_name );
	}
	
	$appid_string = '';
	$appid = awe_get_appid();
	if($appid) $appid_string = '&APPID=' . $appid;	
	
	$lat_lon 				= explode(',', $weather->latlng);
	$api_query				= 'lat=' . $lat_lon[0] . '&lon=' . $lat_lon[1];
	$longlat_ping_url 		= AWESOME_WEATHER_OWM_API_URL . 'weather?' . $api_query . $appid_string;
	$longlat_ping_get 		= wp_remote_get( $longlat_ping_url );
	
	if( !is_wp_error( $longlat_ping_get ) AND isset($longlat_ping_get['body']) AND $longlat_ping_get['body'] != '' ) 
	{
		$ping_data = json_decode( $longlat_ping_get['body'] );
		if( $ping_data AND isset($ping_data->id) AND $ping_data->id )
		{
			if( $what_to_get == 'id' )
			{
				set_transient( $transient_name, $ping_data->id, apply_filters( 'awesome_weather_latlon_cache', 31104000 ) );
				return $ping_data->id;
			}
			else
			{
				set_transient( $transient_name, $ping_data, apply_filters( 'awesome_weather_latlon_cache', 31104000 ) );
				return $ping_data;
			}
		}
	}

	return false;
}

function awe_ip_check() 
{
	// USE EXACTLY ONE
	if( isset($weather->use_real_ip) )
	{
		return $_SERVER['HTTP_X_REAL_IP'];
	}
	if( isset($weather->use_x_forwarded) )
	{
		return $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	
	// USE OTHER STUFF	
	if( getenv('HTTP_X_FORWARDED_FOR') ) 
	{
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	}
	else if( getenv('HTTP_X_REAL_IP') ) 
	{
		$ip = getenv('HTTP_X_REAL_IP');
	}
	else 
	{
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	
	// TESTING
	//$ip = "68.12.196.42"; 		// OKC
	//$ip = "185.18.188.0"; 		// NYC
	//$ip = "192.99.128.170"; 		// MTL
	
	return $ip;
}

// GET CITY ID FROM IP
function awe_latlng_from_ip()
{
	$ip 					= awe_ip_check();
	$ip_hash 				= str_replace('.', '', $ip);
	$ip_transient_name 		= 'awe_ip_ll' . $ip_hash;

	// CLEAR CACHE
    if( isset($_GET['clear_awesome_widget']) ) delete_transient( $ip_transient_name );
	if( get_transient( $ip_transient_name ) )
	{
		return get_transient( $ip_transient_name );
	}
	
	$location_ping_url = str_replace('[[IP]]', $ip, apply_filters( 'awesome_weather_location_lookup_url', AWESOME_WEATHER_LOOKUP_URL ) );
	
	// CHECK FOR IPINFO TOKEN
	$ipinfo_token = awe_get_ipinfo_token();
	if( $ipinfo_token ) $location_ping_url .= '/?token=' . $ipinfo_token;	
	
	// GET LOCATION
	$location_ping_get = wp_remote_get( $location_ping_url );
	if( !is_wp_error( $location_ping_get ) AND isset($location_ping_get['body']) AND $location_ping_get['body'] != '' ) 
	{	
		$ping_data = json_decode( apply_filters('awesome_weather_ip_ping_data', $location_ping_get['body']) );
		
		if( isset($ping_data->loc) AND $ping_data->loc != '' )
		{
			set_transient( $ip_transient_name, $ping_data->loc, apply_filters( 'awesome_weather_ip_cache', 15552000 ) );
			return $ping_data->loc;
		}
	}
	
	return false;
}


// GET UNITS FROM CITY ID
function awe_get_units( &$weather )
{
	// COUNTRIES THAT DEFAULT TO fahrenheit
	$f_countries_abbr 	= apply_filters( 'awesome_weather_f_countries_abbr', array('US','BZ','BS','CY','PR','GU') );
	$f_countries_names 	= apply_filters( 'awesome_weather_f_countries_names', array( 'United States', 'Belize', 'Bahamas', 'Cayman Islands', 'Puerto Rico', 'Guam') );
	
	
	// TRANSIENT NAME
	if( isset($weather->owm_city_id) AND $weather->owm_city_id )
	{
		// CITY ID
		$transient_name 		= 'awe_cu_' . $weather->provider . '_' . $weather->owm_city_id;
		$api_query				= 'id=' . $weather->owm_city_id;
	}
	else if( isset($weather->latlng) )
	{
		// LATLONG
		$latlng = awesome_weather_parse_lat_lon( $weather->latlng );
		
		$transient_name 		= 'awe_cu_' . $weather->provider . '_' . sanitize_title($weather->latlng);
		$api_query				= 'lat=' . $latlng->lat . '&lon=' . $latlng->lng;
	}
	else
	{
		return false;
	}


	// CLEAR CACHE
    if( isset($_GET['clear_awesome_widget']) ) delete_transient( $transient_name );


	// CHECK TRANSIENT
	if( get_transient( $transient_name ) ) 
	{
		$cached_units = get_transient( $transient_name );
		$weather->units	= $cached_units;
		return $cached_units;
	}
	
	// GET UNITS
	$cached_obj = new stdclass;
	$appid_string = '';
	$appid = awe_get_appid();
	if($appid) $appid_string = '&APPID=' . $appid;	
	
	$ping = AWESOME_WEATHER_OWM_API_URL . 'weather?' . $api_query . '&lang=en' . $appid_string;
	$ping_get = wp_remote_get( $ping );

	if( !is_wp_error( $ping_get ) AND isset($ping_get['body']) AND $ping_get['body'] != '' ) 
	{
		$ping_data = json_decode( $ping_get['body'] );
		if( isset($ping_data->sys) AND isset($ping_data->sys->country) )
		{
			if( in_array( $ping_data->sys->country, $f_countries_abbr ) )
			{
				$weather->units				= $cached_obj->units 			= 'F';
			}
			else
			{
				$weather->units				= $cached_obj->units 			= 'C';
			}
		}
	}
	
	if( isset($cached_obj->units) )
	{
		set_transient( $transient_name, $cached_obj->units, apply_filters( 'awesome_weather_result_auto_units_cache', 31104000 ) );
		return $cached_obj->units;
	}

	return false;
}



// CONVERSIONS
function awe_c_to_f( $c )
{
	return round( ( $c * 1.8 ) + 32);
}

function awe_f_to_c( $f )
{
	return round(($f- 32) / 1.8);
}


// CHANGE WEATHER FORM
function awe_change_weather_form( $weather )
{
	if( ! $weather->allow_user_to_change ) return '';
	
	$template = locate_template( array( 'awesome-weather-form.php' ) );
	if( $template )
	{
		include( $template );
	}
	else
	{
		include( dirname(__FILE__) . '/awesome-weather-form.php' );
	}
}

function awe_change_weather_trigger( $weather )
{
	if( !$weather->allow_user_to_change ) return '';
	
	$template = locate_template( array( 'awesome-weather-trigger.php' ) );
	if($template)
	{
		include( $template );
	}
	else
	{
		include(dirname(__FILE__) . '/awesome-weather-trigger.php' );
	}
}


function awe_attribution( $weather )
{
	if( $weather->show_attribution === 'false' ) return '';
	else if( !$weather->show_attribution ) return '';
	
	if( $weather->provider == 'darksky' )
	{
		$attr_text = '<a href="https://darksky.net/poweredby/" target="_blank">';
		$attr_text .= __('Powered by Dark Sky', 'awesome-weather');
		$attr_text .= '</a>';
	}
	else
	{
		$attr_text = $weather->t->weather_from . ' OpenWeatherMap';
	}

	echo apply_filters( 'awesome_weather_attribution', '<div class="awesome-weather-attribution">' . $attr_text . '</div>');
}


// EXTENDED LINK, CUSTOM TEMPLATE AVAILABLE
function awe_extended_link( &$weather, $do_before = '', $do_after = '' )
{
	if( $weather->show_link AND !$weather->extended_url AND isset($weather->owm_city_id) AND $weather->owm_city_id != '' )
	{
		$weather->extended_url = 'https://openweathermap.org/city/' . $weather->owm_city_id;
	}

	if( !$weather->show_link ) return '';
	
	// IF NOT EXTENDED FORECAST TEXT, SET DEFAULT
	if( !$weather->extended_text ) $weather->extended_text = apply_filters('awesome_weather_extended_forecast_text' , __('extended forecast', 'awesome-weather'));
	
	// ESCAPE THE WEATHER URL IF WE HAVE IT
	if( $weather->extended_url ) $weather->extended_url = esc_url($weather->extended_url);
	
	
	$template = locate_template( array( 'awesome-weather-extended.php' ) );
	if( $template )
	{
		echo $do_before;
		include( $template );
		echo $do_after;
	}
	else if( $weather->extended_url OR $weather->extended_text ) 
	{ 
		echo $do_before;
		$extended_url_target = apply_filters('awesome_weather_extended_url_target', '_blank');
		echo '<div class="awesome-weather-more-weather-link">';
		if( $weather->extended_url ) echo '<a href="' . $weather->extended_url . '" target="' . $extended_url_target . '">';
		echo $weather->extended_text;
		if( $weather->extended_url ) echo '</a>';
		echo '</div>';
		echo $do_after;
	}
}

function awesome_weather_refresh()
{
	echo awesome_weather_logic( $_POST );
	exit;
}
add_action( 'wp_ajax_awesome_weather_refresh', 'awesome_weather_refresh' );
add_action( 'wp_ajax_nopriv_awesome_weather_refresh', 'awesome_weather_refresh' );



// SETTINGS
require_once(dirname(__FILE__) . '/awesome-weather-settings.php');


// GET CUSTOM WETHER TEMPLATES
function awesome_weather_use_template( $template_name, $weather )
{
	// GET THE TEMPLATE
	if( $weather->template != 'custom' )
	{
		$awe_weather_template = dirname(__FILE__) . '/templates/' . $template_name . '.php';
		
		if( file_exists( $awe_weather_template ) )
		{
			include( $awe_weather_template );
		}
		else
		{
			echo awesome_weather_error( __('Weather template not found:', 'awesome-weather') . ' ' . $weather->template );
		}
	}
	else
	{
		// GET CUSTOM TEMPLATE
		$template = locate_template( array( "awe-{$template_name}.php" ) );
		
		$user_defined_template_location = apply_filters('awesome_weather_custom_template_location', false);

		if( $user_defined_template_location )
		{
			include( trailingslashit($user_defined_template_location) .  "awe-{$template_name}.php" );
		}
		else if( $template )
		{
			include( $template );
		}
		else
		{
			echo awesome_weather_error( __('Custom template file not found. Please add a file to your theme folder with this name:', 'awesome-weather' ) . " awe-" . $template_name . ".php" ); 
		}
	}
}

// LOCALES
function awesome_weather_get_locales()
{
	return apply_filters('awesome_weather_available_locales', array( 'en', 'es', 'sp', 'fr', 'it', 'de', 'pt', 'ro', 'pl', 'ru', 'uk', 'ua', 'fi', 'nl', 'bg', 'sv', 'se', 'sk', 'ca', 'tr', 'hr', 'zh', 'zh_tw', 'zh_cn', 'hu' ) ); 
}


function awesome_weather_prep_location($text) 
{ 
	$text = stripslashes($text);
    $text = str_replace(array("\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"), array("'", "'", '', '', '-', '--', '...'), $text);
    $text = str_replace(array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)), array("'", "'", '', '', '-', '--', '...'), $text);
    return $text;
} 

function awesome_weather_parse_lat_lon( $latlng )
{
	$p = explode(',', $latlng);
	$rtn = new stdclass;
	$rtn->lat = isset($p[0]) ? $p[0] : false;
	$rtn->lng = isset($p[1]) ? $p[1] : false;
	$rtn->text = $latlng;
	return $rtn;
}


function awesome_weather_get_default_provider()
{
	if( defined('AWESOME_WEATHER_PROVIDER') )
	{
		return AWESOME_WEATHER_PROVIDER;
	}
	else if ( get_option( 'awe-weather-provider' ) )
	{
		return  get_option( 'awe-weather-provider' );
	}
	else 
	{
		return 'openweathermaps';
	}
}