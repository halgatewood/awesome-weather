<?php
	
// AWESOME WEATHER WIDGET, WIDGET CLASS, SO MANY WIDGETS
class AwesomeWeatherWidget extends WP_Widget 
{
	function __construct() { parent::__construct(false, $name = 'Awesome Weather Widget'); }

    function widget($args, $instance) 
    {	
        extract( $args );
        
        // GET WIDGET ID, USED FOR USER LOCATION
        $widget_id 					= isset($instance['id']) ? $instance['id'] : false;
        $locale 					= isset($instance['locale']) ? $instance['locale'] : false;
        $location 					= isset($instance['location']) ? $instance['location'] : false;
        $owm_city_id 				= isset($instance['owm_city_id']) ? $instance['owm_city_id'] : false;
        $override_title 			= isset($instance['override_title']) ? $instance['override_title'] : false;
        $widget_title 				= isset($instance['widget_title']) ? $instance['widget_title'] : false;
        $units 						= isset($instance['units']) ? $instance['units'] : false;
        $size 						= isset($instance['size']) ? $instance['size'] : false;
        $forecast_days 				= isset($instance['forecast_days']) ? $instance['forecast_days'] : false;
        $hide_stats 				= (isset($instance['hide_stats']) AND $instance['hide_stats'] == 1) ? 1 : 0;
        $show_link 					= (isset($instance['show_link']) AND $instance['show_link'] == 1) ? 1 : 0;
        $use_user_location			= (isset($instance['use_user_location']) AND $instance['use_user_location'] == 1) ? 1 : 0;
        $allow_user_to_change		= (isset($instance['allow_user_to_change']) AND $instance['allow_user_to_change'] == 1) ? 1 : 0;
        $show_icons					= (isset($instance['show_icons']) AND $instance['show_icons'] == 1) ? 1 : 0;
        $background					= isset($instance['background']) ? $instance['background'] : false;
        $custom_bg_color			= isset($instance['custom_bg_color']) ? $instance['custom_bg_color'] : false;
        $custom_template_name		= isset($instance['custom_template_name']) ? $instance['custom_template_name'] : false;
        $extended_url				= isset($instance['extended_url']) ? $instance['extended_url'] : false;
        $extended_text				= isset($instance['extended_text']) ? $instance['extended_text'] : false;
        $background_by_weather 		= (isset($instance['background_by_weather']) AND $instance['background_by_weather'] == 1) ? 1 : 0;
		$text_color					= isset($instance['text_color']) ? $instance['text_color'] : '#ffffff';
		$hide_attribution 			= (isset($instance['hide_attribution']) AND $instance['hide_attribution'] == 1) ? 1 : 0;
		$skip_geolocate 			= (isset($instance['skip_geolocate']) AND $instance['skip_geolocate'] == 1) ? 1 : 0;
		$latlng 					= (isset($instance['latlng']) AND $instance['latlng'] != '') ? $instance['latlng'] : '';


		// SET CUSTOM TEMPLATE NAME
		if( $size == 'custom' AND $custom_template_name ) $size = $custom_template_name;


		echo $before_widget;
		if($widget_title != '') echo $before_title . $widget_title . $after_title;
		echo awesome_weather_shortcode( array( 
									'id' => $widget_id,
									'locale' => $locale, 
									'location' => $location, 
									'owm_city_id' => $owm_city_id,
									'override_title' => $override_title, 
									'size' => $size, 
									'units' => $units, 
									'forecast_days' => $forecast_days, 
									'hide_stats' => $hide_stats, 
									'show_link' => $show_link, 
									'background' => $background, 
									'custom_bg_color' => $custom_bg_color,
									'use_user_location' => $use_user_location,
									'allow_user_to_change' => $allow_user_to_change,
									'show_icons' => $show_icons,
									'extended_url' => $extended_url,
									'extended_text' => $extended_text,
									'background_by_weather' => $background_by_weather,
									'text_color' => $text_color,
									'hide_attribution' => $hide_attribution,
									'skip_geolocate' => $skip_geolocate,
									'latlng' => $latlng
								));
		echo $after_widget;
    }
 
    function update($new_instance, $old_instance) 
    {		
		$instance = $old_instance;
		$instance['locale'] 					= strip_tags($new_instance['locale']);
		$instance['location'] 					= strip_tags($new_instance['location']);
		$instance['owm_city_id'] 				= strip_tags($new_instance['owm_city_id']);
		$instance['override_title'] 			= strip_tags($new_instance['override_title']);
		$instance['widget_title'] 				= strip_tags($new_instance['widget_title']);
		$instance['units'] 						= strip_tags($new_instance['units']);
		$instance['size'] 						= strip_tags($new_instance['size']);
		$instance['forecast_days'] 				= strip_tags($new_instance['forecast_days']);
		$instance['background'] 				= strip_tags($new_instance['background']);
		$instance['custom_bg_color'] 			= strip_tags($new_instance['custom_bg_color']);
		$instance['text_color'] 				= strip_tags($new_instance['text_color']);
		$instance['custom_template_name'] 		= strip_tags($new_instance['custom_template_name']);
		$instance['extended_url'] 				= strip_tags($new_instance['extended_url']);
		$instance['extended_text'] 				= strip_tags($new_instance['extended_text']);
		$instance['id'] 						= sanitize_title($new_instance['id']);
		$instance['hide_stats'] 				= (isset($new_instance['hide_stats']) AND $new_instance['hide_stats'] == 1) ? 1 : 0;
		$instance['hide_attribution'] 			= (isset($new_instance['hide_attribution']) AND $new_instance['hide_attribution'] == 1) ? 1 : 0;
		$instance['show_link'] 					= (isset($new_instance['show_link']) AND $new_instance['show_link'] == 1) ? 1 : 0;
		$instance['use_user_location'] 			= (isset($new_instance['use_user_location']) AND $new_instance['use_user_location'] == 1) ? 1 : 0;
		$instance['allow_user_to_change'] 		= (isset($new_instance['allow_user_to_change']) AND $new_instance['allow_user_to_change'] == 1) ? 1 : 0;
		$instance['show_icons'] 				= (isset($new_instance['show_icons']) AND $new_instance['show_icons'] == 1) ? 1 : 0;
		$instance['background_by_weather'] 		= (isset($new_instance['background_by_weather']) AND $new_instance['background_by_weather'] == 1) ? 1 : 0;
		$instance['skip_geolocate'] 			= (isset($new_instance['skip_geolocate']) AND $new_instance['skip_geolocate'] == 1) ? 1 : 0;
		$instance['latlng'] 					= (isset($new_instance['latlng']) AND $new_instance['latlng'] != '') ? $new_instance['latlng'] : '';
        return $instance;
    }
 
    function form($instance) 
    {	
    	global $awesome_weather_sizes;
    	
        $locale 				= isset($instance['locale']) ? esc_attr($instance['locale']) : '';
        $location 				= isset($instance['location']) ? esc_attr($instance['location']) : '';
        $owm_city_id 			= isset($instance['owm_city_id']) ? esc_attr($instance['owm_city_id']) : '';
        $override_title 		= isset($instance['override_title']) ? esc_attr($instance['override_title']) : '';
        $widget_title 			= isset($instance['widget_title']) ? esc_attr($instance['widget_title']) : '';
        $selected_size 			= isset($instance['size']) ? esc_attr($instance['size']) : "wide";
        $units 					= (isset($instance['units']) AND strtoupper($instance['units']) == "C") ? "C" : "F";
        $forecast_days 			= isset($instance['forecast_days']) ? esc_attr($instance['forecast_days']) : 5;
        $hide_stats 			= (isset($instance['hide_stats']) AND $instance['hide_stats'] == 1) ? 1 : 0;
        $hide_attribution 		= (isset($instance['hide_attribution']) AND $instance['hide_attribution'] == 1) ? 1 : 0;
        $show_link 				= (isset($instance['show_link']) AND $instance['show_link'] == 1) ? 1 : 0;
        $use_user_location 		= (isset($instance['use_user_location']) AND $instance['use_user_location'] == 1) ? 1 : 0;
        $allow_user_to_change 	= (isset($instance['allow_user_to_change']) AND $instance['allow_user_to_change'] == 1) ? 1 : 0;
        $show_icons 			= (isset($instance['show_icons']) AND $instance['show_icons'] == 1) ? 1 : 0;
        $background				= isset($instance['background']) ? esc_attr($instance['background']) : '';
        $custom_bg_color		= isset($instance['custom_bg_color']) ? esc_attr($instance['custom_bg_color']) : '';
        $custom_template_name	= isset($instance['custom_template_name']) ? esc_attr($instance['custom_template_name']) : '';
        $extended_url			= isset($instance['extended_url']) ? esc_attr($instance['extended_url']) : '';
        $extended_text			= isset($instance['extended_text']) ? esc_attr($instance['extended_text']) : '';
        $background_by_weather 		= (isset($instance['background_by_weather']) AND $instance['background_by_weather'] == 1) ? 1 : 0;
        $skip_geolocate 			= (isset($instance['skip_geolocate']) AND $instance['skip_geolocate'] == 1) ? 1 : 0;
		$text_color					= isset($instance['text_color']) ? esc_attr($instance['text_color']) : "#ffffff";
		$id							= isset($instance['id']) ? esc_attr($instance['id']) : '';
		$latlng						= isset($instance['latlng']) ? esc_attr($instance['latlng']) : '';
		
		
		$darksky_key 		= awe_get_darksky_key();
		$appid 				= apply_filters( 'awesome_weather_appid', awe_get_appid() );
		
		if( isset($instance['units']) AND 	$instance['units'] == 'auto' ) $units = 'auto';
	
		$theme_folder = substr(strrchr(get_stylesheet_directory(),'/'),1);
		
		// GET DEFAULT PROVIDER
		$provider = awesome_weather_get_default_provider();
		
		if( $provider == 'darksky' )
		{
			$awe_field_id = $this->get_field_id('latlng');
			$forecasts_days_available = 7;
		}
		else
		{
			$awe_field_id = $this->get_field_id('owm_city_id');
			$forecasts_days_available = 15;
		}
	?>
	
	<div id="awesome-weather-fields-<?php echo $this->id; ?>">

		<?php if( $provider == 'openweathermaps' AND !$appid ) { ?>
		<div style="background: #e85959; color: #fff; padding: 10px; margin-bottom: 10px;">
			<?php
				echo __("OpenWeatherMap requires an APP ID key to access their weather data.", 'awesome-weather');
				echo " <a href='https://openweathermap.org/appid' target='_blank' style='color: #fff;'>";
				echo __('Get your APPID', 'awesome-weather');
				echo "</a> ";
				echo __("and", 'awesome-weather');
				echo " <a href='options-general.php?page=awesome-weather&highlight=openweathermaps' target='_blank' style='color: #fff;'>";
				echo __("add it to the settings page.", 'awesome-weather');
				echo "</a> ";
			?>
		</div>
		<?php } ?>
		
		<?php if( $provider == 'darksky' AND !$darksky_key ) { ?>
		<div style="background: #e85959; color: #fff; padding: 10px; margin-bottom: 10px;">
			<?php
				echo __("Dark Sky requires a Secret Key to access their weather data.", 'awesome-weather');
				echo " <a href='https://darksky.net/dev' target='_blank' style='color: #fff;'>";
				echo __('Get your Key', 'awesome-weather');
				echo "</a> ";
				echo __("and", 'awesome-weather');
				echo " <a href='options-general.php?page=awesome-weather&highlight=darksky' target='_blank' style='color: #fff;'>";
				echo __("add it to the settings page.", 'awesome-weather');
				echo "</a> ";
			?>
		</div>
		<?php } ?>
		
		<p>
	    	<label for="<?php echo $this->get_field_id('location'); ?>"><?php _e('Template:', 'awesome-weather'); ?></label><br>
	    	<select id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>" class="awesome-weather-size-select" data-widgetid="<?php echo $this->id; ?>">
	      	<?php foreach($awesome_weather_sizes as $size => $text) { ?>
	      		<option value="<?php echo $size; ?>"<?php if($selected_size == $size) echo " selected=\"selected\""; ?>><?php echo $text; ?></option>
	      	<?php } ?>
	    	</select>
        </p>
        
		<div id="custom-template-<?php echo $this->id; ?>-field"<?php if($selected_size != "custom") echo " style='display:none;'"; ?>>
        	<label for="<?php echo $this->get_field_id('custom_template_name'); ?>"><?php _e('Custom Template Filename:', 'awesome-weather'); ?></label> <small>(<?php _e('found in theme folder', 'awesome-weather'); ?>)</small><br>
        	<?php echo $theme_folder; ?>/awe-&nbsp;<input id="<?php echo $this->get_field_id('custom_template_name'); ?>" name="<?php echo $this->get_field_name('custom_template_name'); ?>" type="text" value="<?php echo $custom_template_name; ?>" style="width: 60px; font-size: 11px;" />&nbsp;.php
		</div>
		
		<hr>
		<?php if( $provider == 'darksky') { ?>
		<p>
        	<label for="<?php echo $this->get_field_id('location'); ?>"><?php _e('Search for Your Location:', 'awesome-weather'); ?> <span id="awe-latlng-spinner-<?php echo $this->get_field_id('location'); ?>" class="hidden"><img src="/wp-admin/images/spinner.gif" width="15" height="15"></span></label> 
        	<input data-cityidfield="<?php echo $awe_field_id; ?>" data-unitsfield="<?php echo $this->get_field_id('units'); ?>" class="widefat awe-location-search-field-darksky" style="margin-top: 4px;" id="<?php echo $this->get_field_id('location'); ?>" name="<?php echo $this->get_field_name('location'); ?>" type="text" value="<?php echo $location; ?>">
		</p>
		<div id="latlng-error-<?php echo $this->get_field_id('location'); ?>"></div>
		<p>
			<label for="<?php echo $this->get_field_id('owm_city_id'); ?>">
				<?php _e('Latitude,Longitude:', 'awesome-weather'); ?><br>
				<small style="line-height: 1em;">(<?php _e('use the location field above to geolocate.', 'awesome-weather'); ?>)</small>
			</label>
			<input class="widefat" style="margin-top: 4px; line-height: 1.5em;" id="<?php echo $this->get_field_id('latlng'); ?>" name="<?php echo $this->get_field_name('latlng'); ?>" type="text" value="<?php echo $latlng; ?>">		
		</p>
		
		<?php } else { ?>
		
		<p>
        	<label for="<?php echo $this->get_field_id('location'); ?>"><?php _e('Search for Your Location:', 'awesome-weather'); ?> <span id="awe-owm-spinner-<?php echo $this->get_field_id('location'); ?>" class="hidden"><img src="/wp-admin/images/spinner.gif" width="15" height="15"></span></label> 
        	<input data-cityidfield="<?php echo $awe_field_id; ?>" data-unitsfield="<?php echo $this->get_field_id('units'); ?>" class="widefat awe-location-search-field" style="margin-top: 4px;" id="<?php echo $this->get_field_id('location'); ?>" name="<?php echo $this->get_field_name('location'); ?>" type="text" value="<?php echo $location; ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('owm_city_id'); ?>">
				<?php _e('OpenWeatherMap City ID:', 'awesome-weather'); ?><br>
				<small>(<?php _e('use the location field above to find the ID for your city', 'awesome-weather'); ?>)</small>
			</label>
			<input class="widefat" style="margin-top: 4px; line-height: 1.5em;" id="<?php echo $this->get_field_id('owm_city_id'); ?>" name="<?php echo $this->get_field_name('owm_city_id'); ?>" type="text" value="<?php echo $owm_city_id; ?>">
		</p>
		<div id="owmid-selector-<?php echo $this->get_field_id('location'); ?>"></div>
		<script type="text/javascript">
			<?php if( !$owm_city_id ) { ?>
			jQuery('#awesome-weather-fields-<?php echo $this->id; ?> #<?php echo $this->get_field_id('location'); ?>').trigger('keyup');
			<?php } ?>
		</script>
		<?php } ?>
		
		<hr>
                
		<p>
        	<label for="<?php echo $this->get_field_id('override_title'); ?>"><?php _e('Banner Title:', 'awesome-weather'); ?></label> 
        	<input class="widefat" id="<?php echo $this->get_field_id('override_title'); ?>" name="<?php echo $this->get_field_name('override_title'); ?>" type="text" value="<?php echo $override_title; ?>">
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('widget_title'); ?>"><?php _e('Widget Title: (optional)', 'awesome-weather'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('widget_title'); ?>" name="<?php echo $this->get_field_name('widget_title'); ?>" type="text" value="<?php echo $widget_title; ?>">
		</p>
		
		<hr>
        
        <p>
			<label for="<?php echo $this->get_field_id('forecast_days'); ?>"><?php _e('Forecast:', 'awesome-weather'); ?></label> &nbsp;
			<select id="<?php echo $this->get_field_id('forecast_days'); ?>" name="<?php echo $this->get_field_name('forecast_days'); ?>">
				<?php for( $d = $forecasts_days_available; $d > 0; $d-- ) { ?>
					<option value="<?php echo $d; ?>"<?php if($forecast_days == $d) echo " selected=\"selected\""; ?>> <?php echo sprintf( _n( '%s Day', '%s Days', $d, 'awesome-weather' ), $d ); ?></option>
				<?php } ?>
				<option value="hide"<?php if($forecast_days == 'hide') echo " selected=\"selected\""; ?>><?php _e("Don't Show", 'awesome-weather'); ?></option>
			</select>
		</p>
		
		<p>
        	<label for="<?php echo $this->get_field_id('background'); ?>"><?php _e('Background Image:', 'awesome-weather'); ?></label> 
        	<input class="widefat" id="<?php echo $this->get_field_id('background'); ?>" name="<?php echo $this->get_field_name('background'); ?>" type="text" value="<?php echo $background; ?>">
		</p>
        
        <p>
          <input id="<?php echo $this->get_field_id('background_by_weather'); ?>" name="<?php echo $this->get_field_name('background_by_weather'); ?>" type="checkbox" value="1" <?php if($background_by_weather) echo ' checked="checked"'; ?>>
          <label for="<?php echo $this->get_field_id('background_by_weather'); ?>"><?php _e('Use Different Background Images Based on Weather', 'awesome-weather'); ?></label>  <a href="https://halgatewood.com/docs/plugins/awesome-weather-widget/creating-different-backgrounds-for-different-weather" target="_blank">(?)</a> &nbsp;
        </p>
        
		<p>
        	<label for="<?php echo $this->get_field_id('custom_bg_color'); ?>"><?php _e('Custom Background Color:', 'awesome-weather'); ?></label><br />
        	<small><?php _e('overrides color changing', 'awesome-weather'); ?>: #7fb761 / rgba(0,0,0,0.5)</small>
        	<input class="widefat" id="<?php echo $this->get_field_id('custom_bg_color'); ?>" name="<?php echo $this->get_field_name('custom_bg_color'); ?>" type="text" value="<?php echo $custom_bg_color; ?>" />
		</p>
		
		<p>
		    <label for="<?php echo $this->get_field_id( 'text_color' ); ?>" style="display:block;"><?php _e( 'Text Color', 'awesome-weather' ); ?></label> 
		    <input class="widefat color-picker" id="<?php echo $this->get_field_id( 'text_color' ); ?>" name="<?php echo $this->get_field_name( 'text_color' ); ?>" type="text" value="<?php echo esc_attr( $text_color ); ?>" />
		</p>
		
		<script type="text/javascript">
		    jQuery(document).ready(function($) 
		    { 
		            jQuery('#awesome-weather-fields-<?php echo $this->id; ?> #<?php echo $this->get_field_id( 'text_color' ); ?>').on('focus', function(){
		                var parent = jQuery(this).parent();
		                jQuery(this).wpColorPicker()
		                parent.find('.wp-color-result').click();
		            }); 
		            
		            jQuery('#awesome-weather-fields-<?php echo $this->id; ?> #<?php echo $this->get_field_id( 'text_color' ); ?>').wpColorPicker()
		    }); 
		</script>
		      
		<p>
        	<label for="<?php echo $this->get_field_id('units'); ?>"><?php _e('Units:', 'awesome-weather'); ?></label> &nbsp;
        	<input id="c-<?php echo $this->get_field_id('units'); ?>" name="<?php echo $this->get_field_name('units'); ?>" type="radio" value="F" <?php if($units == "F") echo ' checked="checked"'; ?> /> F &nbsp; &nbsp;
        	<input id="f-<?php echo $this->get_field_id('units'); ?>" name="<?php echo $this->get_field_name('units'); ?>" type="radio" value="C" <?php if($units == "C") echo ' checked="checked"'; ?> /> C &nbsp; &nbsp;
        	<span id="<?php echo $this->get_field_id('units'); ?>-span" <?php if(!$use_user_location) echo "class=\"hidden\""; ?>><input id="auto-<?php echo $this->get_field_id('units'); ?>" name="<?php echo $this->get_field_name('units'); ?>" type="radio" value="auto" <?php if($units == "auto") echo ' checked="checked"'; ?> /> Auto</span>
		</p>
		
		<p>
        	<input id="<?php echo $this->get_field_id('use_user_location'); ?>" name="<?php echo $this->get_field_name('use_user_location'); ?>" type="checkbox" value="1" <?php if($use_user_location) echo ' checked="checked"'; ?> />
			<label for="<?php echo $this->get_field_id('use_user_location'); ?>"><?php _e('Use User Location', 'awesome-weather'); ?></label>
		</p>
		<p id="<?php echo $this->get_field_id('allow_user_to_change'); ?>-wrap"<?php if(!$use_user_location) echo " class=\"hidden\""; ?>>
        	<input id="<?php echo $this->get_field_id('allow_user_to_change'); ?>" name="<?php echo $this->get_field_name('allow_user_to_change'); ?>" type="checkbox" value="1" <?php if($allow_user_to_change) echo ' checked="checked"'; ?> />
			<label for="<?php echo $this->get_field_id('allow_user_to_change'); ?>"><?php _e('Allow User to Change the Location', 'awesome-weather'); ?></label>
		</p>
		<p id="<?php echo $this->get_field_id('skip_geolocate'); ?>-wrap"<?php if(!$allow_user_to_change) echo " class=\"hidden\""; ?>>
        	<input id="<?php echo $this->get_field_id('skip_geolocate'); ?>" name="<?php echo $this->get_field_name('skip_geolocate'); ?>" type="checkbox" value="1" <?php if($skip_geolocate) echo ' checked="checked"'; ?> />
			<label for="<?php echo $this->get_field_id('skip_geolocate'); ?>"><?php _e('Skip HTML5 Geolocation', 'awesome-weather'); ?></label>
		</p>
		
		
		<script type="text/javascript">
			jQuery('#awesome-weather-fields-<?php echo $this->id; ?> #<?php echo $this->get_field_id('use_user_location'); ?>').change(function() 
			{
				if( !this.checked )
				{
					jQuery('#awesome-weather-fields-<?php echo $this->id; ?> span#<?php echo $this->get_field_id('units'); ?>-span').addClass('hidden');
					jQuery('#awesome-weather-fields-<?php echo $this->id; ?> p#<?php echo $this->get_field_id('allow_user_to_change'); ?>-wrap').addClass('hidden');
					jQuery('#awesome-weather-fields-<?php echo $this->id; ?> p#<?php echo $this->get_field_id('skip_geolocate'); ?>-wrap').addClass('hidden');
				}
				else
				{
					jQuery('#awesome-weather-fields-<?php echo $this->id; ?> span#<?php echo $this->get_field_id('units'); ?>-span').removeClass('hidden');
					jQuery('#awesome-weather-fields-<?php echo $this->id; ?> p#<?php echo $this->get_field_id('allow_user_to_change'); ?>-wrap').removeClass('hidden');
					
					if ( document.getElementById('<?php echo $this->get_field_id('allow_user_to_change'); ?>').checked )
					{
						jQuery('#awesome-weather-fields-<?php echo $this->id; ?> p#<?php echo $this->get_field_id('skip_geolocate'); ?>-wrap').removeClass('hidden');
					}
				}
			});
			
			jQuery('#awesome-weather-fields-<?php echo $this->id; ?> #<?php echo $this->get_field_id('allow_user_to_change'); ?>').change(function() 
			{
				if( this.checked )
				{
					jQuery('#awesome-weather-fields-<?php echo $this->id; ?> p#<?php echo $this->get_field_id('skip_geolocate'); ?>-wrap').removeClass('hidden');
				}
				else
				{
					jQuery('#awesome-weather-fields-<?php echo $this->id; ?> p#<?php echo $this->get_field_id('skip_geolocate'); ?>-wrap').addClass('hidden');
				}
			});
		</script>
		
		<p>
        	<input id="<?php echo $this->get_field_id('show_icons'); ?>" name="<?php echo $this->get_field_name('show_icons'); ?>" type="checkbox" value="1" <?php if($show_icons) echo ' checked="checked"'; ?> />
			<label for="<?php echo $this->get_field_id('show_icons'); ?>"><?php _e('Show Weather Icons', 'awesome-weather'); ?></label>
		</p>
		
		<p>
        	<input id="<?php echo $this->get_field_id('hide_stats'); ?>" name="<?php echo $this->get_field_name('hide_stats'); ?>" type="checkbox" value="1" <?php if($hide_stats) echo ' checked="checked"'; ?> />
			<label for="<?php echo $this->get_field_id('hide_stats'); ?>"><?php _e('Hide Stats', 'awesome-weather'); ?></label>
		</p>
		
		<p>
        	<input id="<?php echo $this->get_field_id('hide_attribution'); ?>" name="<?php echo $this->get_field_name('hide_attribution'); ?>" type="checkbox" value="1" <?php if($hide_attribution) echo ' checked="checked"'; ?> />
			<label for="<?php echo $this->get_field_id('hide_attribution'); ?>"><?php _e('Hide Weather Attribution', 'awesome-weather'); ?></label>
		</p>
		
		<hr>
		
		<p>
        	<input id="<?php echo $this->get_field_id('show_link'); ?>" name="<?php echo $this->get_field_name('show_link'); ?>" type="checkbox" value="1" <?php if($show_link) echo ' checked="checked"'; ?> />
			<label for="<?php echo $this->get_field_id('show_link'); ?>"><?php _e('Link to Extended Forecast', 'awesome-weather'); ?></label>
		</p>
		
		<p>
        	<label for="<?php echo $this->get_field_id('extended_url'); ?>"><?php _e('Custom Extended Forecast URL:', 'awesome-weather'); ?></label> 
        	<input class="widefat" id="<?php echo $this->get_field_id('extended_url'); ?>" name="<?php echo $this->get_field_name('extended_url'); ?>" type="text" value="<?php echo $extended_url; ?>" />
		</p>
		
		<p>
        	<label for="<?php echo $this->get_field_id('extended_text'); ?>"><?php _e('Custom Extended Forecast Text:', 'awesome-weather'); ?></label> 
        	<input class="widefat" id="<?php echo $this->get_field_id('extended_text'); ?>" name="<?php echo $this->get_field_name('extended_text'); ?>" type="text" value="<?php echo $extended_text; ?>" />
		</p>
		
		<hr>
		
		<p style="text-transform: uppercase; font-size: 0.8em; font-weight: bold;"><?php _e('Advanced Options', 'awesome-weather'); ?></p>
		
		<p>
        	<label for="<?php echo $this->get_field_id('locale'); ?>"><?php _e('Locale:', 'awesome-weather'); ?></label> 
        	<input class="widefat" id="<?php echo $this->get_field_id('locale'); ?>" name="<?php echo $this->get_field_name('locale'); ?>" type="text" value="<?php echo $locale; ?>" />
		</p>
		
		<p>
        	<label for="<?php echo $this->get_field_id('id'); ?>"><?php _e('Widget ID:', 'awesome-weather'); ?></label> 
        	<input class="widefat" id="<?php echo $this->get_field_id('id'); ?>" name="<?php echo $this->get_field_name('id'); ?>" type="text" value="<?php echo $id; ?>" />
		</p>
		
		<hr>
		
	</div>

        <?php 
    }
}

function awe_widgets_register()
{
	register_widget( 'AwesomeWeatherWidget' );
}
add_action( 'widgets_init', 'awe_widgets_register' );