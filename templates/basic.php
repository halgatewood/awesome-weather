<div id="<?php awe_widget_id( $weather ); ?>" class="<?php echo $background_classes ?>" <?php echo $inline_style; ?>>
	
	<?php if($weather->background_image) { ?>
	<div class="awesome-weather-cover" style="background-image: url(<?php echo $weather->background_image; ?>);">
	<div class="awesome-weather-darken">
	<?php } ?>
	
	<?php if( isset($weather->data['current'])) { ?>

		<?php if($weather->show_icons) { ?><span class="awesome-weather-stats-icon"><i class="<?php echo $weather->data['current']['icon']; ?>"></i></span><?php } ?>
		<span class="awesome-weather-current-temp"><?php echo $weather->data['current']['temp']; ?><sup>&deg;</sup></span><!-- /.awesome-weather-current-temp -->
		<span class="awe_desc"><?php echo $weather->data['current']['description']; ?></span>
	
		<?php if($weather->show_stats) { ?>
		<div class="awesome-weather-todays-stats">
			<span class="awe_humidty"><?php echo $weather->data['current']['humidity']; ?>% <?php echo $weather->t->humidity; ?></span>
			<span class="awe_wind"><?php echo $weather->t->wind; ?> <?php echo $weather->data['current']['wind_speed']; ?><?php echo $weather->data['current']['wind_speed_text']; ?> <?php echo $weather->data['current']['wind_direction']; ?></span>
			<span class="awe_highlow"><?php echo $weather->t->high; ?> <?php echo $weather->data['current']['high']; ?> &bull; <?php echo $weather->t->low; ?> <?php echo $weather->data['current']['low']; ?></span>	
		</div><!-- /.awesome-weather-todays-stats -->
		<?php } ?>	
		
	<?php } ?>	
	
	<?php if($weather->forecast_days != "hide") { ?>
	
		<div class="awesome-weather-forecast-text awe_days_<?php echo count($weather_forecast); ?> awecf">
	
			<?php foreach( $weather_forecast as $forecast ) { ?>
				<span class="awesome-weather-forecast-day-text">
					<?php if($weather->show_icons) { ?><i class="<?php echo $forecast->icon; ?>"></i><?php } ?>
					<span class="awesome-weather-forecast-day-temp-text"><?php echo $forecast->high; ?><sup>&deg;</sup></span>
					<span class="awesome-weather-forecast-day-abbr-text"><?php echo $forecast->day_of_week; ?></span>
				</span>
			<?php } ?>
	
		</div><!-- /.awesome-weather-forecast -->
	
	<?php } ?>
	
	<?php awe_extended_link( $weather ); ?>
	
	<?php awe_attribution( $weather ); ?>
	
	<?php if($weather->background_image) { ?>
		</div><!-- /.awesome-weather-cover -->
		</div><!-- /.awesome-weather-darken -->
	<?php } ?>
</div><!-- /.awesome-weather-wrap: basic -->