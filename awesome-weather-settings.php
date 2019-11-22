<?php

// CREATE THE SETTINGS PAGE
function awesome_weather_setting_page_menu()
{
	add_options_page( 'Awesome Weather', 'Awesome Weather', 'manage_options', 'awesome-weather', 'awesome_weather_page' );
}

function awesome_weather_page()
{
	global $wpdb;
	
	// QUERY ALL CACHE
	$sql = "SELECT * FROM $wpdb->options WHERE option_name LIKE '%_transient_awe_%'";
	$potential_caches = (int) $wpdb->query( $sql );
?>
<div class="wrap">
    <h2><?php _e('Awesome Weather Widget', 'awesome-weather'); ?></h2>
    
    <?php if( isset($_GET['awesome-weather-cached-cleared']) ) { ?>
    <div id="setting-error-settings_updated" class="updated settings-error"> 
		<p><strong><?php _e('Weather Widget Cache Cleared', 'awesome-weather'); ?></strong></p>
	</div>
	<?php } ?>
    
    <form action="options.php" method="POST">
        <?php settings_fields( 'awe-basic-settings-group' ); ?>
        <?php do_settings_sections( 'awesome-weather' ); ?>
        <?php submit_button(); ?>
    </form>
	<hr>
	<p>
		<a href="options-general.php?page=awesome-weather&action=awesome-weather-clear-transients" class="button"><?php _e('Clear all Awesome Weather Widget Cache', 'awesome-weather'); ?> (<?php echo $potential_caches; ?>)</a>
	</p> 
</div>
<?php
}


// SET SETTINGS LINK ON PLUGIN PAGE
function awesome_weather_plugin_action_links( $links, $file ) 
{
	$settings_link = '<a href="' . admin_url( 'options-general.php?page=awesome-weather' ) . '">' . esc_html__( 'Settings', 'awesome-weather' ) . '</a>';
	if( $file == 'awesome-weather/awesome-weather.php' ) array_unshift( $links, $settings_link );

	$donate_link = '<a href="https://halgatewood.com/donate" target="_blank">' . esc_html__( 'Donate', 'awesome-weather' ) . '</a>';
	if( $file == 'awesome-weather/awesome-weather.php' ) array_unshift( $links, $donate_link );
	
	return $links;
}
add_filter( 'plugin_action_links', 'awesome_weather_plugin_action_links', 10, 2 );


add_action( 'admin_init', 'awesome_weather_setting_init' );
function awesome_weather_setting_init()
{
    register_setting( 'awe-basic-settings-group', 'awe-weather-provider' );
    register_setting( 'awe-basic-settings-group', 'open-weather-key' );
    register_setting( 'awe-basic-settings-group', 'darksky-secret-key' );
    register_setting( 'awe-basic-settings-group', 'location-iq-token' );
    register_setting( 'awe-basic-settings-group', 'aw-error-handling' );
    register_setting( 'awe-basic-settings-group', 'ipinfo-token' );

    add_settings_section( 'awe-basic-settings', '', 'awesome_weather_api_keys_description', 'awesome-weather' );
	add_settings_field( 'awe-weather-provider', __('Default Weather Provider', 'awesome-weather'), 'awesome_weather_weather_provider', 'awesome-weather', 'awe-basic-settings' );
	
	add_settings_field( 'open-weather-key', __('OpenWeatherMaps APPID', 'awesome-weather'), 'awesome_weather_openweather_key', 'awesome-weather', 'awe-basic-settings' );
	add_settings_field( 'darksky-secret-key', __('Dark Sky Secret Key', 'awesome-weather'), 'awesome_weather_darksky_secret_key', 'awesome-weather', 'awe-basic-settings' );
	add_settings_field( 'location-iq-token', __('LocationIQ Token', 'awesome-weather'), 'awesome_weather_location_iq_token', 'awesome-weather', 'awe-basic-settings' );
	add_settings_field( 'ipinfo-token', __('ipinfo.io Token', 'awesome-weather'), 'awesome_weather_ipinfo_token', 'awesome-weather', 'awe-basic-settings' );
	add_settings_field( 'aw-error-handling', __('Error Handling', 'awesome-weather'), 'awesome_weather_error_handling_setting', 'awesome-weather', 'awe-basic-settings' );

	if( isset($_GET['action']) AND $_GET['action'] == "awesome-weather-clear-transients")
	{
		awesome_weather_delete_all_transients();
		wp_redirect( "options-general.php?page=awesome-weather&awesome-weather-cached-cleared=true" );
		die;
	}

}




// DELETE ALL AWESOME WEATHER WIDGET TRANSIENTS
function awesome_weather_delete_all_transients_save( $value )
{
	awesome_weather_delete_all_transients();
	return $value;
}

function awesome_weather_delete_all_transients()
{
	global $wpdb;
	
	@setcookie('awe_city_id', 0 , time() - 3600, '/');
	@setcookie('awe_openweathermaps_city_id', 0 , time() - 3600, '/');
	@setcookie('awe_latlng', '' , time() - 3600, '/');
	
	// DELETE TRANSIENTS
	$sql = "DELETE FROM $wpdb->options WHERE option_name LIKE '%_transient_awe_%'";
	$clean = $wpdb->query( $sql );
	return true;
}

function awesome_weather_api_keys_description() { }


function awesome_weather_weather_provider()
{
	if( defined('AWESOME_WEATHER_PROVIDER') )
	{
		echo __('Defined in wp-config', 'awesome-weather') . ": <code>" . ucwords(AWESOME_WEATHER_PROVIDER) . "</code>";
	}
	else 
	{
		$setting = esc_attr( get_option( 'awe-weather-provider' ) );
		if( !$setting ) $setting = 'openweathermaps';
	
		echo "<input type='radio' id='awe-settings-provider-owm' name='awe-weather-provider' value='openweathermaps' " . checked( $setting, 'openweathermaps', false ) . " /> <label for='awe-settings-provider-owm'>OpenWeatherMap</label> &nbsp; &nbsp; ";
		echo "<input type='radio' id='awe-settings-provider-darksky' name='awe-weather-provider' value='darksky' " . checked( $setting, 'darksky', false ) . " /> <label for='awe-settings-provider-darksky'>Dark Sky</label> ";
		echo "<p>";
		echo __("Where do you want your weather data to be provided from. If you change this, it's a good idea to clear the cache below.", 'awesome-weather');
		echo "</p>";
	}
}


function awesome_weather_openweather_key()
{
	if( isset($_GET['highlight']) AND $_GET['highlight'] == 'openweathermaps' ) echo '<div style="background: #f3f39b; padding: 20px;">';
	
	if( defined('AWESOME_WEATHER_APPID') )
	{
		echo __('Defined in wp-config', 'awesome-weather') . ": <code>" . substr(AWESOME_WEATHER_APPID,0,7) . "..." . substr(AWESOME_WEATHER_APPID, strlen(AWESOME_WEATHER_APPID) - 7) . "</code>";
	}
	else 
	{
		$setting = esc_attr( get_option( 'open-weather-key' ) );
		echo "<input type='text' name='open-weather-key' value='$setting' style='width:70%;' />";
		echo "<p>OpenWeatherMap is a weather provider that users city IDs or latitude and longitude to calculate weather. ";
		echo "<br><a href='https://openweathermap.org/appid' target='_blank' class='button'>" . __('Get an APPID', 'awesome-weather') . "</a>";
		echo "</p>";
	}
	
	if( isset($_GET['highlight']) AND $_GET['highlight'] == 'openweathermaps' ) echo '</div>';
}

function awesome_weather_darksky_secret_key()
{
	if( isset($_GET['highlight']) AND $_GET['highlight'] == 'darksky' ) echo '<div style="background: #f3f39b; padding: 20px;">';
	
	if( defined('AWESOME_WEATHER_DARKSKY_KEY') )
	{
		echo __('Defined in wp-config', 'awesome-weather') . ": <code>" . substr(AWESOME_WEATHER_DARKSKY_KEY,0,7) . "..." . substr(AWESOME_WEATHER_DARKSKY_KEY, strlen(AWESOME_WEATHER_DARKSKY_KEY) - 7) . "</code>";
	}
	else 
	{
		$setting = esc_attr( get_option( 'darksky-secret-key' ) );
		echo "<input type='text' name='darksky-secret-key' value='$setting' style='width:70%;' />";
		echo "<p>Dark Sky is a weather provider that users latitude and longitude to calculate weather. ";
		echo "<br><a href='https://darksky.net/dev/account' target='_blank' class='button'>" . __('Get a Secret Key', 'awesome-weather') . "</a>";
		echo "</p>";
	}
	
	if( isset($_GET['highlight']) AND $_GET['highlight'] == 'darksky' ) echo '</div>';
}

function awesome_weather_location_iq_token()
{
	if( defined('AWESOME_WEATHER_LOCATIONIQ_TOKEN') )
	{
		echo __('Defined in wp-config', 'awesome-weather') . ": <code>" . substr(AWESOME_WEATHER_LOCATIONIQ_TOKEN,0,7) . "..." . substr(AWESOME_WEATHER_LOCATIONIQ_TOKEN, strlen(AWESOME_WEATHER_LOCATIONIQ_TOKEN) - 7) . "</code>";
	}
	else 
	{
		$setting = esc_attr( get_option( 'location-iq-token' ) );
		echo "<input type='text' name='location-iq-token' value='$setting' style='width:70%;' />";
		echo "<p>Location IQ is a service that converts a location text to a latitude and longitude. ";
		echo "<br><a href='https://locationiq.com/' target='_blank' class='button'>" . __('Get a Token', 'awesome-weather') . "</a>";
		echo "</p>";
	}
}

function awesome_weather_ipinfo_token()
{
	if( defined('IPINFO_TOKEN') )
	{
		echo __('Defined in wp-config', 'awesome-weather') . ": <code>" . substr(IPINFO_TOKEN,0,7) . "...</code>";
	}
	else 
	{
		$setting = esc_attr( get_option( 'ipinfo-token' ) );
		echo "<input type='text' name='ipinfo-token' value='$setting' style='width:70%;' />";
		echo "<p>";
		echo __("ipinfo.io attempts to convert IP addresses to latitude and longitude. Without a token you can do 1,000 requests a day. If you need more, you will need a token. ", 'awesome-weather');
		echo "<br><a href='http://ipinfo.io/pricing' target='_blank' class='button'>" . __('Get a Token', 'awesome-weather') . "</a>";
		echo "</p>";
	}
}

function awesome_weather_error_handling_setting()
{
	$setting = esc_attr( get_option( 'aw-error-handling' ) );
	if(!$setting) $setting = "source";
	
	echo "<input type='radio' name='aw-error-handling' id='awe-settings-error-source' value='source' " . checked( $setting, 'source', false ) . " /> <label for='awe-settings-error-source'>" . __('Hidden in Source', 'awesome-weather') . "</label> &nbsp; &nbsp; ";
	echo "<input type='radio' name='aw-error-handling' id='awe-settings-error-admin' value='display-admin' " . checked( $setting, 'display-admin', false ) . " /> <label for='awe-settings-error-admin'>" . __('Display if Admin', 'awesome-weather') . "</label> &nbsp; &nbsp; ";
	echo "<input type='radio' name='aw-error-handling' id='awe-settings-error-all' value='display-all' " . checked( $setting, 'display-all', false ) . " /> <label for='awe-settings-error-all'>" . __('Display for Anyone', 'awesome-weather') . "</label> &nbsp; &nbsp; ";
	
	echo "<p>";
	echo __("What should the plugin do when there is an error?", 'awesome-weather');
	echo "</p>";
}
