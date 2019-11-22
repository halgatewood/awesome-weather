<?php
	
if( is_admin() )
{
	
	// MEDIA BUTTON
	function awesome_weather_media_button() 
	{
		global $pagenow, $typenow, $wp_version;
		$output = '';
		if ( version_compare( $wp_version, '3.5', '>=' ) AND in_array( $pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) ) 
		{
			$img = '<style>#awe-thick-location.error { border: solid 1px red; } #awesome-weather-media-button::before { font: 400 18px/1 dashicons; content: \'\f176\'; }</style><span class="wp-media-buttons-icon" id="awesome-weather-media-button"></span>';
			$output = '<a href="#TB_inline&width=750&height=500&inlineId=add-awesome-weather" class="thickbox button awesome-weather-thickbox" title="' .  __( 'Add Awesome Weather Widget Shortcode', 'awesome-weather'  ) . '" style="padding-left: .4em;"> ' . $img . __( 'Add Weather', 'awesome-weather'  ) . '</a>';
		}
		echo $output;
	}
	add_action( 'media_buttons', 'awesome_weather_media_button', 11 );
	
	// MEDIA BUTTON FUNCTIONALITY
	function awesome_weather_admin_footer_for_thickbox() 
	{
		global $pagenow, $typenow, $wp_version, $awesome_weather_sizes;
		
		$appid 				= apply_filters( 'awesome_weather_appid', awe_get_appid() );
		$theme_folder 		= substr(strrchr(get_stylesheet_directory(),'/'),1);
		
		if ( version_compare( $wp_version, '3.5', '>=' ) AND in_array( $pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) ) { ?>
			<script type="text/javascript">
	            function insert_awesome_weather() 
	            {
		           	var add_to_shortcode = "";
		           	
		           	var awe_location = jQuery('#awe-thick-location').val();
		           	
		           	if( awe_location )
		           	{
		           		if( awe_location )  add_to_shortcode = add_to_shortcode + " location=\"" + awe_location + "\"";
		           		
		           		var awe_size = jQuery('#awe-thick-size').val();
		           		if( awe_size )  add_to_shortcode = add_to_shortcode + " size=\"" + awe_size + "\"";
		           	
				   		var awe_custom_template = jQuery('#awe-thick-custom-template').val();
		           		if( awe_custom_template )  add_to_shortcode = add_to_shortcode + " custom_template_name=\"" + awe_custom_template + "\"";
		           	
				   		var awe_forecast_days = jQuery('#awe-thick-forecast_days').val();
				   		if( awe_forecast_days )  add_to_shortcode = add_to_shortcode + " forecast_days=\"" + awe_forecast_days + "\"";
		           
				   		var awe_override_title = jQuery('#awe-thick-override_title').val();
				   		if( awe_override_title )  add_to_shortcode = add_to_shortcode + " override_title=\"" + awe_override_title + "\"";	
				   		
				   		var awe_widget_title = jQuery('#awe-thick-widget_title').val();
				   		if( awe_widget_title )  add_to_shortcode = add_to_shortcode + " widget_title=\"" + awe_widget_title + "\"";	
		           
				   		var awe_background = jQuery('#awe-thick-background').val();
				   		if( awe_background )  add_to_shortcode = add_to_shortcode + " background=\"" + awe_background + "\"";	
		           
				   		var awe_custom_bg_color = jQuery('#awe-thick-custom_bg_color').val();
				   		if( awe_custom_bg_color )  add_to_shortcode = add_to_shortcode + " custom_bg_color=\"" + awe_custom_bg_color + "\"";	
		           
				   		var awe_text_color = jQuery('#awe-thick-text_color').val();
				   		if( awe_text_color )  add_to_shortcode = add_to_shortcode + " text_color=\"" + awe_text_color + "\"";	
		           
				   		var awe_extended_url = jQuery('#awe-thick-extended-url').val();
				   		if( awe_extended_url )  add_to_shortcode = add_to_shortcode + " extended_url=\"" + awe_extended_url + "\"";	
		           
				   		var awe_extended_text = jQuery('#awe-thick-extended-text').val();
				   		if( awe_extended_text )  add_to_shortcode = add_to_shortcode + " extended_text=\"" + awe_extended_text + "\"";	
		           
		           
				   		var awe_owm_city_id = jQuery('#awe-owm-city-id').val();
				   		if( awe_owm_city_id )  add_to_shortcode = add_to_shortcode + " owm_city_id=\"" + awe_owm_city_id + "\"";
				   		
				   		// CHECKBOXES
				   		if( jQuery('#awe-thick-background_by_weather').is(":checked") )  add_to_shortcode = add_to_shortcode + " background_by_weather=\"1\"";
				   		if( jQuery('#awe-thick-units-f') .is(":checked") ) add_to_shortcode = add_to_shortcode + " units=\"F\"";
				   		if( jQuery('#awe-thick-units-c') .is(":checked") ) add_to_shortcode = add_to_shortcode + " units=\"C\"";
				   		if( jQuery('#awe-thick-units-auto') .is(":checked") ) add_to_shortcode = add_to_shortcode + " units=\"auto\"";
				   		
				   		
				   		if( jQuery('#awe-thick-use_user_location') .is(":checked") ) add_to_shortcode = add_to_shortcode + " use_user_location=\"1\"";
				   		if( jQuery('#awe-thick-allow_user_to_change').is(":checked") ) add_to_shortcode = add_to_shortcode + " allow_user_to_change=\"1\"";
				   		if( jQuery('#awe-thick-show_icons').is(":checked") ) add_to_shortcode = add_to_shortcode + " show_icons=\"1\"";
				   		if( jQuery('#awe-thick-hide_stats').is(":checked") ) add_to_shortcode = add_to_shortcode + " hide_stats=\"1\"";
				   		if( jQuery('#awe-thick-hide_attribution').is(":checked") ) add_to_shortcode = add_to_shortcode + " hide_attribution=\"1\"";
		           
				   		jQuery('#awe-thick-location').removeClass("error");
				   		window.send_to_editor("[awesome-weather" + add_to_shortcode + "]");
		           	}
		           	else
		           	{
			           	jQuery('#awe-thick-location').addClass("error");
		           	}
	            }
	            
	            function awe_thick_size_change()
	            {
		            if( jQuery('#awe-thick-size').val() == "custom" )
		            {
			            jQuery("#awe-thick-custom-template-wrap").slideDown();
		            }
		            else
		            {
			            jQuery("#awe-thick-custom-template-wrap").slideUp();
		            }
	            }

			</script>
	
			<div id="add-awesome-weather" style="display: none;">
				<div class="wrap" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
					
					<div>
						<?php _e('Location:', 'awesome-weather'); ?>
						<input type="text" id="awe-thick-location" name="awe-location" value="" placeholder="<?php _e('Required', 'awesome-weather'); ?>">
					</div>
					
					<div style="padding: 10px 0px;">
						<?php _e('Template:', 'awesome-weather'); ?> &nbsp;
				    	<select id="awe-thick-size" onchange="awe_thick_size_change();">
				      	<?php foreach($awesome_weather_sizes as $size => $text) { ?>
				      		<option value="<?php echo $size; ?>"><?php echo $text; ?></option>
				      	<?php } ?>
				    	</select>
					</div>
					
					<div id="awe-thick-custom-template-wrap" style="padding-bottom: 10px; display: none;">
						<label for="awe-thick-custom-template"><?php _e('Custom Template Filename:', 'awesome-weather'); ?></label> <small>(<?php _e('found in theme folder', 'awesome-weather'); ?>)</small><br>
						<?php echo $theme_folder; ?>/awe-&nbsp;<input id="awe-thick-custom-template" type="text" value="" style="width: 80px; font-size: 11px;" />&nbsp;.php
					</div>
					
					<div style="padding-bottom: 10px;">
						<?php _e('Units:', 'awesome-weather'); ?>&nbsp;
						<input type="radio" name="awe-thick-units" id="awe-thick-units-f" checked="checked"> <label for="awe-thick-units-f">F</label> &nbsp; &nbsp;
						<input type="radio" name="awe-thick-units" id="awe-thick-units-c"> <label for="awe-thick-units-c">C</label> &nbsp; &nbsp;
						<input type="radio" name="awe-thick-units" id="awe-thick-units-auto"> <label for="awe-thick-units-auto">auto</label> 
					</div>
					
					<div style="padding: 10px 0px;">
						<input type="button" class="button-primary" value="<?php echo __( 'Insert Weather Widget', 'awesome-weather' ); ?>" onclick="insert_awesome_weather();" />
					</div>

					<hr>
					
					<h3><?php _e('Optional', 'awesome-weather'); ?></h3>

					<div style="padding-bottom: 20px;">
						<?php _e('OpenWeatherMap City ID:', 'awesome-weather'); ?>
						<input type="number" id="awe-owm-city-id" value="">
					</div>
	
					<div style="padding-bottom: 10px;">
					<?php _e('Forecast:', 'awesome-weather'); ?>
					<select id="awe-thick-forecast_days">
						<option value=""><?php _e('Default', 'awesome-weather'); ?></option>
						<?php for( $d = 1; $d <= 15; $d++ ) { ?>
							<option value="<?php echo $d; ?>"> <?php echo sprintf( _n( '%s Day', '%s Days', $d, 'awesome-weather' ), $d ); ?></option>
						<?php } ?>
						<option value="hide"><?php _e("Don't Show", 'awesome-weather'); ?></option>
					</select>
					</div>

					<div style="padding-bottom: 10px;">
						<?php _e('Banner Title:', 'awesome-weather'); ?>
						<input type="text" id="awe-thick-override_title" value="">
					</div>
					
					<div style=" padding-bottom: 10px; display: inline-block">
						<?php _e('Widget Title:', 'awesome-weather'); ?>
						<input type="text" id="awe-thick-widget_title" value="">
					</div>
					
					
					<div style="margin: 10px 0; padding: 20px; background: #efefef;">
						<div style="padding-bottom: 7px;">
							<?php _e('Background Image:', 'awesome-weather'); ?>
							<input type="text" id="awe-thick-background" value="" style="width: 90%;">
						</div>
						<div style="padding: 10px 0;">
							<input id="awe-thick-background_by_weather" name="background_by_weather" type="checkbox" value="1">
							<label for="awe-thick-background_by_weather"><?php _e('Use Different Background Images Based on Weather', 'awesome-weather'); ?></label>  <a href="https://halgatewood.com/docs/plugins/awesome-weather-widget/creating-different-backgrounds-for-different-weather" target="_blank">(?)</a> &nbsp;
						</div>
						<div>
							<?php _e('Custom Background Color:', 'awesome-weather'); ?><br>
							<input type="text" id="awe-thick-custom_bg_color" value="" style="width: 60%;" placeholder="#7fb761 / rgba(0,0,0,0.5)">
						</div>
					</div>
					
					<div>
						<?php _e( 'Text Color', 'awesome-weather' ); ?><br>
						<input type="text" id="awe-thick-text_color" value="" style="width: 60%;" placeholder="#000000">
					</div>
					
					<div style="padding-bottom: 10px;">
						<input type="checkbox" id="awe-thick-use_user_location" value="">
						<label for="awe-thick-use_user_location"><?php _e('Use User Location', 'awesome-weather'); ?></label>
					</div>
					
					<div style="padding-bottom: 10px;">
						<input type="checkbox" id="awe-thick-allow_user_to_change" value="">
						<label for="awe-thick-allow_user_to_change"><?php _e('Allow User to Change the Location', 'awesome-weather'); ?></label>
					</div>
					
					<div style="padding-bottom: 10px;">
						<input type="checkbox" id="awe-thick-show_icons" value="">
						<label for="awe-thick-show_icons"><?php _e('Show Weather Icons', 'awesome-weather'); ?></label>
					</div>
					
					<div style="padding-bottom: 10px;">
						<input type="checkbox" id="awe-thick-hide_stats" value="">
						<label for="awe-thick-hide_stats"><?php _e('Hide Stats', 'awesome-weather'); ?></label>
					</div>
					
					<div style="padding-bottom: 10px;">
						<input type="checkbox" id="awe-thick-hide_attribution" value=""> 
						<label for="awe-thick-hide_attribution"><?php _e('Hide Weather Attribution', 'awesome-weather'); ?></label>
					</div>
					
					<div style="padding-bottom: 10px;">
						<input id="awe-thick-show-link" type="checkbox" value="1" />
						<label for="awe-thick-show-link"><?php _e('Link to Extended Forecast', 'awesome-weather'); ?></label>
					</div>
					
					<div style="padding-bottom: 10px;">
			        	<label for="awe-thick-extended-url"><?php _e('Custom Extended Forecast URL:', 'awesome-weather'); ?></label><br>
			        	<input id="awe-thick-extended-url" type="text" value="" style="width: 60%;" >
					</div>

					<div style="padding-bottom: 10px;">
			        	<label for="awe-thick-extended-text"><?php _e('Custom Extended Forecast Text:', 'awesome-weather'); ?></label><br>
			        	<input id="awe-thick-extended-text" type="text" value="" style="width: 60%;" >
					</div>
					
					<div style="padding: 10px 0px;">
						<input type="button" class="button-primary" value="<?php echo __( 'Insert Weather Widget', 'awesome-weather' ); ?>" onclick="insert_awesome_weather();" />
						<a class="button-secondary" onclick="tb_remove();" title="<?php _e( 'Cancel', 'awesome-weather' ); ?>"><?php _e( 'Cancel', 'awesome-weather' ); ?></a>
					</div>
				</div>
			</div>
		<?php
		}
	}
	add_action( 'admin_footer', 'awesome_weather_admin_footer_for_thickbox' );
	
}