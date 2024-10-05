<?php

/*
Plugin Name: Weather Info
Description: A small hook plugin that pulls weather data from openweathermap and slots it into the post during the_content filter.
Version: 1.0
Author: Jeremiah Bratton
*/

define( 'JB_WEATHER_INFO_DIR', plugin_dir_path( __FILE__ ) );
require_once plugin_dir_path(__FILE__ ) . 'includes/class-jb-weather-info.php';

function run_jb_weather_info() {
    $jb_weather_info = new JB_Weather_Info();
    $jb_weather_info->init();
}
run_jb_weather_info();

add_filter( 'jb_weather_info_wrapper_element', function() {
    return 'div';
});

add_filter( 'jb_weather_info_header_level', function() {
    return 'h2';
});

add_filter( 'jb_weather_info_wrapper_class', function() {
    return 'filtered-weather-info';
});


