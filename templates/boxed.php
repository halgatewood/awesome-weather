<div id="<?php awe_widget_id( $weather ); ?>" class="<?php echo $background_classes ?>" <?php echo $inline_style; ?>>

<?php if($weather->background_image) { ?>
	<div class="awesome-weather-cover" style="background-image: url(<?php echo $weather->background_image; ?>);">
	<div class="awesome-weather-darken">
<?php } ?>

	<?php awe_change_weather_form( $weather ); ?>
	
	<div class="awesome-weather-header awecf"><span><?php echo $header_title; ?><?php awe_change_weather_trigger( $weather ); ?></span></div>

	<?php if( isset($weather->data['current'])) { ?>
		<div class="awesome-weather-current-temp">
			
			<div class="awesome-weather-boxed-box awecf">
				<strong>
				<?php if($weather->show_icons) { ?><span><i class="<?php echo $weather->data['current']['icon']; ?>"></i></span><?php } ?>
				<?php echo $weather->data['current']['temp']; ?><sup>&deg;</sup>
				<?php echo $weather->data['current']['description']; ?>
				</strong>
			</div>
			
			<?php if($weather->show_stats) { ?>
				<div class="awe_highlow awecf awesome-weather-boxed-box">
					<?php if($weather->show_icons) { ?><span><i class="wi wi-thermometer-exterior"></i></span><?php } ?>
					<?php echo $weather->t->high; ?> <?php echo $weather->data['current']['high']; ?> &bull; <?php echo $weather->t->low; ?> <?php echo $weather->data['current']['low']; ?>
				</div>	
				<div class="awe_humidty awecf awesome-weather-boxed-box">
					<?php if($weather->show_icons) { ?><span><i class="wi wi-humidity"></i></span><?php } ?>
					<?php echo $weather->data['current']['humidity']; ?>% <?php echo $weather->t->humidity; ?>
				</div>
				<div class="awe_wind awecf awesome-weather-boxed-box">
					<?php if($weather->show_icons) { ?><span><i class="wi wi-wind wi-towards-<?php echo strtolower($weather->data['current']['wind_direction']); ?>"></i></span><?php } ?>
					<?php echo $weather->t->wind; ?> <?php echo $weather->data['current']['wind_speed']; ?><?php echo $weather->data['current']['wind_speed_text']; ?> <?php echo $weather->data['current']['wind_direction']; ?>
				</div>
				<div class="awe_sun awesome-weather-boxed-box awecf">
					<?php if($weather->show_icons) { ?><span><i class="wi wi-sunrise"></i></span><?php } ?>
					<?php echo $weather->data['current']['sunrise_time']; ?>
					→
					<?php echo $weather->data['current']['sunset_time']; ?>
				</div>
				<div class="awesome-weather-boxed-box awecf">
					<?php if($weather->show_icons) { ?><span><i class="wi wi-time-5"></i></span><?php } ?>
					<?php echo date_i18n( apply_filters("awesome_weather_date_formatstring", get_option( 'date_format' ) ) ); ?>
				</div>
			<?php } ?>

		</div><!-- /.awesome-weather-current-temp -->
	<?php } ?>

	<?php if($weather->forecast_days != "hide") { ?>
		<div class="awesome-weather-boxed-forecast awesome-weather-boxed-box awecf awe_days_<?php echo count($weather_forecast); ?>">
			<?php if($weather->show_icons) { ?><span>&nbsp;</span><?php } ?>
			<?php foreach( $weather_forecast as $forecast ) { ?>
				<div class="awesome-weather-forecast-day">
					<?php if($weather->show_icons) { ?><i class="<?php echo $forecast->icon; ?>"></i><?php } ?>
					<div class="awesome-weather-forecast-day-abbr"><?php echo $forecast->day_of_week; ?></div>
					<div class="awesome-weather-forecast-day-temp"><?php echo $forecast->high; ?></div>
				</div>
			<?php } ?>
		</div><!-- /.awesome-weather-forecast -->
	<?php } ?>
	
	<?php 
		if( $weather->show_icons )
		{
			awe_extended_link( $weather, "<div class='awesome-weather-boxed-box'><span><i class='wi wi-direction-up-right'></i></span>", "</div>" ); 	
		}
		else
		{
			awe_extended_link( $weather, "<div class='awesome-weather-boxed-box'>", "</div>" ); 
		}
	?>
	
	<?php awe_attribution( $weather ); ?>

<?php if($weather->background_image) { ?>
	</div><!-- /.awesome-weather-cover -->
	</div><!-- /.awesome-weather-darken -->
<?php } ?>

</div><!-- /.awesome-weather-wrap: boxed -->