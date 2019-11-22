<div id="<?php awe_widget_id( $weather ); ?>" class="<?php echo $background_classes ?>" <?php echo $inline_style; ?>>

<?php if($weather->background_image) { ?><div class="awesome-weather-cover" style="background-image: url(<?php echo $weather->background_image; ?>);"><?php } ?>

	<?php awe_change_weather_form( $weather ); ?>

	<div class="awesome-weather-header awecf"><span><?php echo $header_title; ?><?php awe_change_weather_trigger( $weather ); ?></span></div>

	<?php if( isset($weather->data['current'])) { ?>
	
		<div class="awesome-weather-currently">
			<div class="awesome-weather-darken awecf">
				<div class="awesome-weather-current-temp"><strong><?php echo $weather->data['current']['temp']; ?><sup>&deg;</sup></strong></div>
				<div class="awesome-weather-current-conditions">
					<div class="awe_desc awe-nowrap">
						<strong>
						<?php if($weather->show_icons) { ?><i class="<?php echo $weather->data['current']['icon']; ?>"></i><?php } ?>
						<?php echo $weather->data['current']['description']; ?>
						</strong>
					</div>
					<div class="awe-nowrap awe-date"><?php echo date_i18n( apply_filters("awesome_weather_date_formatstring", "l, F j" ) ); ?></div>
				</div>
			</div>
		</div>

	<?php } ?>

<?php if($weather->background_image) { ?></div><!-- /.awesome-weather-cover --><?php } ?>

<?php if($weather->show_stats OR $weather->forecast_days != "hide" ) { ?><div class="awe-material-details"><?php } ?>

	<?php if($weather->show_stats) { ?>
	<div class="awesome-weather-todays-stats awecf">
		<div class="awe_wind">
			<?php if($weather->show_icons) { ?><span><i class="wi wi-wind wi-towards-<?php echo strtolower($weather->data['current']['wind_direction']); ?>"></i></span><?php } ?>
			<?php echo $weather->data['current']['wind_speed']; ?><small><?php echo $weather->data['current']['wind_speed_text']; ?></small>
		</div>
		<div class="awe_humidty">
			<?php if($weather->show_icons) { ?><i class="wi wi-humidity"></i><?php } ?>
			<?php echo $weather->data['current']['humidity']; ?>%
		</div>
		<div class="awe_highlow">
			<i class="wi wi-direction-up"></i> <?php echo $weather->data['current']['high']; ?><sup>&deg;</sup>
			<i class="wi wi-direction-down"></i> <?php echo $weather->data['current']['low']; ?><sup>&deg;</sup>
		</div>
	</div><!-- /.awesome-weather-todays-stats -->
	<?php } ?>
	
	<?php if($weather->forecast_days != "hide") { ?>
	
		<div class="awesome-weather-forecast awe_days_<?php echo count($weather_forecast); ?> awecf">
	
			<?php foreach( $weather_forecast as $forecast ) { ?>
				<div class="awesome-weather-forecast-day">
					<div class="awesome-weather-forecast-day-abbr"><small><?php echo $forecast->day_of_week; ?></small></div>
					<?php if($weather->show_icons) { ?><i class="<?php echo $forecast->icon; ?>"></i><?php } ?>
					<div class="awesome-weather-forecast-day-temp"><strong><small><?php echo $forecast->high; ?><sup>&deg;</sup></small></strong></div>
				</div>
			<?php } ?>
	
		</div><!-- /.awesome-weather-forecast -->
	
	<?php } ?>
	
	<?php awe_extended_link( $weather ); ?>
	
	<?php awe_attribution( $weather ); ?>
	
<?php if($weather->show_stats OR $weather->forecast_days != "hide" ) { ?></div><?php } ?>

</div><!-- /.awesome-weather-wrap: wide -->