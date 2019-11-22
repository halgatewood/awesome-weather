<div id="<?php awe_widget_id( $weather ); ?>" class="<?php echo $background_classes ?>" <?php echo $inline_style; ?>>

<?php if($weather->background_image) { ?>
	<div class="awesome-weather-cover" style="background-image: url(<?php echo $weather->background_image; ?>);">
	<div class="awesome-weather-darken">
<?php } ?>

	<?php awe_change_weather_form( $weather ); ?>

	<div class="awesome-weather-header"><span><?php echo $header_title; ?><?php awe_change_weather_trigger( $weather ); ?></span></div>

	<?php if( isset($weather->data['current'])) { ?>

		<?php if($weather->show_icons) { ?>
		<div class="awesome-weather-stats-icon">
			<i class="<?php echo $weather->data['current']['icon']; ?>"></i>
		</div>
		<?php } ?>

		<div class="awesome-weather-current-temp">
			<strong><?php echo $weather->data['current']['temp']; ?><sup>&deg;</sup></strong>
		</div><!-- /.awesome-weather-current-temp -->

		<?php if($weather->show_stats) { ?>
		<div class="awesome-weather-todays-stats">
			<div class="awe_desc"><?php echo $weather->data['current']['description']; ?></div>
			<div class="awe_humidty"><?php echo $weather->data['current']['humidity']; ?>% <?php echo $weather->t->humidity; ?></div>
			<div class="awe_wind"><?php echo $weather->t->wind; ?> <?php echo $weather->data['current']['wind_speed']; ?><?php echo $weather->data['current']['wind_speed_text']; ?> <?php echo $weather->data['current']['wind_direction']; ?></div>
			<div class="awe_highlow"><?php echo $weather->t->high; ?> <?php echo $weather->data['current']['high']; ?> &bull; <?php echo $weather->t->low; ?> <?php echo $weather->data['current']['low']; ?></div>	
		</div><!-- /.awesome-weather-todays-stats -->
		<?php } ?>
	
	<?php } ?>
	
	<?php if($weather->forecast_days != "hide") { ?>
	
		<div class="awesome-weather-forecast awe_days_<?php echo count($weather_forecast); ?> awecf">
	
			<?php foreach( $weather_forecast as $forecast ) { ?>
				<div class="awesome-weather-forecast-day">
					<?php if($weather->show_icons) { ?><i class="<?php echo $forecast->icon; ?>"></i><?php } ?>
					<div class="awesome-weather-forecast-day-temp"><?php echo $forecast->high; ?><sup>&deg;</sup></div>
					<div class="awesome-weather-forecast-day-abbr"><?php echo $forecast->day_of_week; ?></div>
				</div>
			<?php } ?>
	
		</div><!-- /.awesome-weather-forecast -->
	
	<?php } ?>
	
	<?php awe_extended_link( $weather ); ?>
	
	<?php awe_attribution( $weather ); ?>

<?php if($weather->background_image) { ?>
	</div><!-- /.awesome-weather-cover -->
	</div><!-- /.awesome-weather-darken -->
<?php } ?>

</div><!-- /.awesome-weather-wrap: tall -->