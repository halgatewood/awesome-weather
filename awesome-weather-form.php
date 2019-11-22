<form action="javascript:;" method="post" class="awesome-weather-form" data-widget-id="<?php echo $weather->id; ?>">
	<div class="awesome-weather-city-error"><?php echo $weather->t->city_not_found; ?></div>
	<input type="text" name="awe-new-location" class="awesome-weather-form-user-location" placeholder="<?php echo $weather->t->search_placeholder; ?>" autocomplete="off">
	<div class="awe-searching"><i class="wi <?php echo apply_filters('awesome_weather_loader', 'wi-day-sunny'); ?>"></i></div>
</form>