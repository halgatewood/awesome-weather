=== Awesome Weather Widget ===
Contributors: halgatewood
Donate link: https://halgatewood.com/donate/
Tags: widgets, sidebar, shortcode, openweathermap, darksky, weather, weather widget, forecast, global, temp, local weather, local forecast
Requires at least: 5.0
Tested up to: 5.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Finally beautiful weather widgets for your beautiful site.

== Description ==

This plugin allows you to easily add super clean (and awesome) weather widgets to your site. 

= Weather by OpenWeatherMap or Dark Sky =
The weather data is available either by [OpenWeatherMap](https://openweathermap.org) or [Dark Sky](https://darksky.net). 

They require a free to access the data. 

* [Open Weather Map API Key](http://openweathermap.org/appid#get)
* [Dark Sky Secret Key](https://darksky.net/dev)

Once you have the API Key, you can save it in the WordPress admin under `'Settings' -> 'Awesome Weather'`

= Help Guides =
There are lots of juicy [Help Guides on my website](https://halgatewood.com/docs/plugins/awesome-weather-widget).
These will answer most of the questions you have. 
Some popular ones include:

*   [Adding a Widget](https://halgatewood.com/docs/plugins/awesome-weather-widget/adding-widget)
*   [Shotcode Attributes](https://halgatewood.com/docs/plugins/awesome-weather-widget/using-shortcode)
*   [Creating Custom Templates](https://halgatewood.com/docs/plugins/awesome-weather-widget/creating-custom-templates)
*   [Getting the User's Location](https://halgatewood.com/docs/plugins/awesome-weather-widget/user-location-detection-settings)
*   [Bonus Functions for Getting Weather Data](https://halgatewood.com/docs/plugins/awesome-weather-widget/pro-functions-grab-weather-data)
*   [Available Filters](https://halgatewood.com/docs/plugins/awesome-weather-widget/available-filters)
*   [Cache Settings](https://halgatewood.com/docs/plugins/awesome-weather-widget/clearing-weather-cache)
*   [API Key Settings](https://halgatewood.com/docs/plugins/awesome-weather-widget/register-for-an-openweathermap-api-key-appid)

= About the Developer =
The development of this plugin was done by [Hal Gatewood](https://halgatewood.com) mostly when I should be sleeping. I have a full time job that is not building WordPress plugins, so please keep this in mind when you submit your support tickets. I also **do not work for OpenWeatherMap or Dark Sky** and have no control over the quality of the weather data returned from them. Sorry.

= Setup =
Use the built in widget with all of its marvelous settings or add it to a page or theme with the shortcode:
`[awesome-weather owm_city_id="4544349"]` or `[awesome-weather location="Oklahoma City"]`

== Installation ==

1. Add plugin to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Register for an OpenWeatherMap [API Key](http://openweathermap.org/appid#get)
1. Add your API Key to the settings field in 'Settings' -> 'Awesome Weather' (added in version 1.5.3)
1. Use shortcode or widget to display awesome weather on your awesome site

The easiest shortcode setting is just: `[awesome-weather location="Oklahoma City"]`


== Screenshots ==

1. Basic wide layout
2. Basic tall layout
3. Micro, using the checkbox 'Hide Stats'
4. Widget Settings
5. Background Image Option (1.2)
6. Add inline styles to your widget and set custom background colors (1.3.1)
7. Use different background images based on weather (1.5)
8. Search for the City ID directly in the widget settings (1.5)

== Upgrade Notice ==

I have merged my PRO plugin into this FREE plugin. Enjoy all the new features!

== Changelog ==

= 3.0.1 = 
- Fix: Locale issues simplified.

= 3.0 =
- New: Pro replaces free plugin. Thanks to everyone who supported me.

= 2.0 =
* Improvements to City Lookup
* Either location attribute or owm_city_id is required, not both now!
* Code cleanup

= 1.5.14 =
* As per consensus in the support forums, we have changed the forecast temp to use the max temp instead of the generic variable returned from the weather data

= 1.5.13 =
* Another attempted fix to the forecast days

= 1.5.12 =
* Attempted fix to forecast days due to OpenWeatherMap API Change

= 1.5.11 =
* Get just the weather data with function: awesome_weather_data
* trim value of the API key in awe_get_appid 
* removing trailing slash for background images on some systems double slashes were being added

= 1.5.10 =
* PHP7 Support
* New lat and lon attributes to get weather by coordinates

= 1.5.9 =
* Updated language to allow for custom translations located at: wp-content/languages/awesome-weather/awesome-weather-{locale}.mo 
* Checkbox to hide the attribution in the widget settings or use shortcode attribute hide_attribution="1"

= 1.5.8 =
* Language fixes and improvements
* OpenWeatherMap attribution added as per licensing requirements
* High and Low temperature fix

= 1.5.7 =
* Wording changes to help improve user experience, thus new updated .pot file
* Bug fix for AWESOME_WEATHER_APPID constant, wasn't always being used.
* Changed awesome-weather-widget.js to awesome-weather-widget-admin.js
* Moved Widget functions into widget.php file
* Fixed locations searches that contain commas
* Check for variables exist before trying to display them (minimizes PHP Notices)
* Rounding wind speed to nearest integer
* Added filters used in the PRO version to keep consistancy

= 1.5.6.2 =
* Added SK to locales and a filter to modify list of available locales

= 1.5.6.1 =
* Fix trailing slash issue with one of the preset background image checks

= 1.5.6 =
* Two new filters available to remove to the Google font Open Sans. (`awesome_weather_use_google_font` AND `awesome_weather_google_font_queue_name`)
* Added new missing background preset `atmosphere.jpg`

= 1.5.5 =
* New constant for the AppID: AWESOME_WEATHER_APPID. Set in `wp-config.php` for multisite installations
* Shortcode attribute for locale: `locale="fr"`
* Shortcode attribute for the units display symbol: `units_display_symbol="&deg;F"`

= 1.5.4 =
* Preset background images added for the 'Use Different Background Images Based on Weather' option
* Color picker to choose a font color for the whole widget
* CSS whitespace: no-wrap on the little degree symbols
* Fixed wind speed to mph for 'Imperial' and m/s for Celsius as per the OpenWeatherMap weather data section
* Updated .pot file

= 1.5.3.1 =
* Added an error message in the admin widget page to let users know they need an APPID

= 1.5.3 =
* Added spot for API key in 'Settings' -> 'Awesome Weather'
* Added new setting to decide how to show error messages.

= 1.5.2 =
* Updated WP_Widget construct to prevent future breaking.

= 1.5.1 =
* Changed .custom class to .awe_custom

= 1.5 =
* You can now speed up your weather and provide better accuracy by using the new Search box in the widget to find the OpenWeatherMap City ID. 
* We also added the ability to use different background images by weather. Also new CSS properties using the weather condition code and text are added so you can target based on weather type.
* Added several new filters to modify aspects of the weather widget like changing the C and F to a Degree symbol.
* Fixed an issue where changing the forecast days would not clear the cache.
* Default cache is now 30 minutes

= 1.4.3.3 =
* Hungarian language added (thanks Istvan Hidegkuti

= 1.4.3.2 =
* Finnish language added (thanks Mikko Anttila)

= 1.4.3.1 =
More languages correctly supported from the OpenWeatherMap API

= 1.4.3 =
* Fixed spanish translations for weather description
* Transient bug for languages also

= 1.4.2 = 
Added box-sizing: border-box to hopefully clean up the widget in many themes.

= 1.4.1 =
* New translations added.
* Caching fixes
* Fixed checkboxes in widget settings
* Checked to be working with WordPress 3.9

= 1.4 =
* Extended forecast now uses WP current_time to determine what days to show, setting in 'Settings' -> 'General'
* Added a Widget Title field that uses the standard widget code from the sidebar (optional)
* Fixed bottom margin issue for Firefox
* Minor CSS tweaks
* Support for OpenWeatherMaps City ID, just insert in the Location field.
* Support for rgba() in the Custom Background Color
* Changed default cached time from 1 hour to 3 hours
* Cleaned up two PHP notices in admin

= 1.3.4 =
Fixed issue with Location stripping spaces from text cause weather to not get accessed. Thanks @storkontheroof!

= 1.3.3 =
Moved Google Font out of CSS into enqueue

= 1.3.2 =
* Improved support for poorly coded themes that load the before_title and after_title with extra divs and don't take into account that widgets may not use a title. 
* Portuguese translation created by user: alvarogois

= 1.3.1 = 
* Added setting for color override
* Added URL param to clear transient cache '?clear_awesome_widget'
* Added new translations for the days of the week in the extended forecast
* Improved caching support
* Ability to add inline styles to the widget shortcode

= 1.3 =
* Upgraded to most recent OpenWeatherMap APIS
* Ready for translations
* Current Locales available from OpenWeatherMap: 
* English - en, Russian - ru, Italian - it, Spanish - sp, Ukrainian - ua, German - de, Portuguese - pt, Romanian - ro, Polish - pl, Finnish - fi, Dutch - nl, French - fr, Bulgarian - bg, Swedish - se, Chinese Traditional - zh_tw, Chinese Simplified - zh_cn, Turkish - tr 

= 1.2.6 =
* Improved error handling with API calls

= 1.2.5 =
* Widget with custom background CSS issue.

= 1.2.4 =
* Forecast now stops showing today.

= 1.2.3 =
* remove file_get_contents and used wp_remote_get

= 1.2.1 =
* Background Image Option in widget

= 1.2 =
* Background Image Option

= 1.1 =
* Errors are now commented out. Look in the source to see what the problem is.
* Ability to add link to openweathermap for extended forecast

= 1.0.2 =
* Removed debugging code, sorry!

= 1.0.1 =
* Changed API endpoints

= 1.0 =
* Initial load of the plugin.