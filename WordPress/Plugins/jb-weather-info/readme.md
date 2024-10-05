# Simple Weather Information Plugin

This plugin fetches weather information from Open-Meteo based on latitude and longitude values stored in post meta. It then appends a temperature section to the end of the post content.

## Available Filters

### jb_weather_info_wrapper_element

Change the wrapping HTML element tag

```php
add_filter( 'jb_weather_info_wrapper_element', function() {
    return 'div';
});
```

### jb_weather_info_header_level

Change the header level tag for the weather info section

```php
add_filter( 'jb_weather_info_header_level', function() {
    return 'h2';
});
```

### jb_weather_info_wrapper_class

Filter the top level wrapper class of the weather information section
```php
add_filter( 'jb_weather_info_wrapper_class', function() {
    return 'filtered-weather-info';
});
```
