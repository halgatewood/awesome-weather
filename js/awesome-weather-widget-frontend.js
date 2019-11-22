
if( typeof awe == 'undefined') { var awe = []; }

// GLOBAL FUNCTIONS
function awesome_weather_show_form( awe_widget_id ) 
{
	awe_stop_loading( awe_widget_id );
	jQuery('#' + awe_widget_id + ' .awesome-weather-form').slideDown();
	jQuery('#' + awe_widget_id + ' .awesome-weather-form input').focus();
}
	
function awe_stop_loading( awe_widget_id )
{
	jQuery('#' + awe_widget_id + ' .awe-searching').hide();
}	

// 2.0 - AJAX LOADING
function awe_ajax_load( weather )
{
	awe['awe_weather_widget_json_' + weather.id] = weather;
	
	var widget_obj = weather;
	widget_obj.action ='awesome_weather_refresh';
	jQuery.post(widget_obj.ajaxurl, widget_obj, function( response ) 
	{
		jQuery('.awe-ajax-' + widget_obj.id).html( response );
	});
}


// DOCUMENT LOAD
jQuery(document).ready(function()
{
	
	// CHANGE LOCATION FORM
	jQuery(document).on('submit', '.awesome-weather-form', function( e )
	{
		e.preventDefault();
		
		var this_form 		= jQuery(this);
		var awe_widget_id 	= this_form.data('widget-id');
		
		if( jQuery('#awe-ajax-' + awe_widget_id).length )
		{
			var current_widget 	= jQuery('#awe-ajax-' + awe_widget_id);
		}
		else
		{
			var current_widget 	= jQuery('#' + awe_widget_id);
		}

		var user_location 	= jQuery(this_form).find('.awesome-weather-form-user-location').val();
		if( user_location )
		{
			// SHOW LOADING
			current_widget.find('.awe-searching').fadeIn();
			
			var widget_obj 				= awe['awe_weather_widget_json_' + awe_widget_id];
			widget_obj.latlng 			= "0";
			widget_obj.owm_city_id 		= "0";
			widget_obj.user_location 	= user_location;
			
			// PASS WEATHER OBJECT BACK THROUGH THE SYSTEM
			jQuery.post(widget_obj.ajaxurl, widget_obj, function( response ) 
			{
				if( response == "false" || response == false || response == "" || (response.indexOf('awesome-weather-error') >= 0) )
				{
					if( response.indexOf('awesome-weather-error') >= 0 )
					{
						current_widget.find('.awesome-weather-city-error').replaceWith( response.replace('awesome-weather-error','awesome-weather-city-error') );
						current_widget.find('.awesome-weather-city-error').fadeIn();
					}
					else
					{
						current_widget.find('.awesome-weather-error').fadeIn();
					}	
				}
				else
				{
					// SPIT BACK THE RESULTS IN THE CONTAINER
					current_widget.replaceWith( response );
					current_widget.find('.awesome-weather-error').hide();
				}
				
				// STOP LOADING
				awe_stop_loading( awe_widget_id );
			});
		}

		e.preventDefault();
	});
	

	// WEATHER TRIGGER FROM CLICK
	jQuery(document).on('click', '.awe-weather-trigger a', function(e) 
	{
		e.preventDefault();
		
		var this_btn 		= jQuery(this);
		var awe_widget_id 	= this_btn.data('widget-id');
		
		if( jQuery('#awe-ajax-' + awe_widget_id).length )
		{
			var current_widget 	= jQuery('#awe-ajax-' + awe_widget_id);
		}
		else
		{
			var current_widget 	= jQuery('#' + awe_widget_id);
		}

		// HIDE WEATHER BUBBLE
		current_widget.find('.awe-weather-bubble').hide();
		
		if( jQuery('#' + awe_widget_id + ' .awesome-weather-form').is(":visible") )
		{
			jQuery('#' + awe_widget_id + ' .awesome-weather-form').slideUp(400, function() {
				jQuery('#' + awe_widget_id + ' .awesome-weather-form .awesome-weather-error').hide();
			});
			return false;
		}

		// GET OBJECT OF WEATHER
		var widget_obj = awe['awe_weather_widget_json_' + awe_widget_id];
		
		// JUST SHOW FORM
		var show_form_first = false;
		
		// WIDGET ATTRIBUTE: skip_geolocate
		if( widget_obj.skip_geolocate !== 'undefined' )
		{
			if( widget_obj.skip_geolocate ) { show_form_first = true; }
		}
		
		if( show_form_first )
		{
			awesome_weather_show_form( awe_widget_id );
			return false;
		}
		
		// CHECK IF HTML5 GEOLOCATION IS AVAILABLE
		if( navigator.geolocation ) 
		{
			var geo_options = { enableHighAccuracy: true, timeout: 5000 };
        	navigator.geolocation.getCurrentPosition(awesome_weather_set_location, awesome_weather_show_form_to_user, geo_options );
			this_btn.addClass('awe-spin');
    	} 
    	else 
    	{
	    	// NO GEO LOCATION, SHOW FORM
		    awesome_weather_show_form( awe_widget_id );
		}
		
		function awesome_weather_show_form_to_user()
		{
			awesome_weather_show_form( awe_widget_id );
		}
		
		function awesome_weather_set_location( position ) 
		{
			// SAVE LOCATION AND REFRESH
			var widget_obj = awe['awe_weather_widget_json_' + awe_widget_id];
			
			// ADD LAT/LNG TO NEW WEATHER OBJECT
			widget_obj.latlng 			= position.coords.latitude + "," + position.coords.longitude;
			widget_obj.geotriggered 	= "0";
			widget_obj.owm_city_id 		= "0";
		
			// PASS WEATHER OBJECT BACK THROUGH THE SYSTEM
			jQuery.post(widget_obj.ajaxurl, widget_obj, function( response ) 
			{
				// SPIT BACK THE RESULTS IN THE CONTAINER
				current_widget.replaceWith( response );
				awe_stop_loading( awe_widget_id );
			});
		}

		return false;
	});
});