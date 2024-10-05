<?php

class JB_Weather_Content
{

    protected $plugin_name;
    protected $default_latitude = '64.200844';
    protected $default_longitude = '-149.493668';

    protected $transient_timeout = 60 * MINUTE_IN_SECONDS;

    public function __construct($plugin_name)
    {
        $this->plugin_name = $plugin_name;
    }

    /**
     * Add weather section to the end of the_content using filter
     */
    public function add_weather_section_to_content($content)
    {
        $weather_info = $this->get_weather_info(get_the_ID());
        if ($weather_info) {
            //Filterable aspects
            $weather_info_wrapper_class = apply_filters( 'jb_weather_info_wrapper_class', 'weather-info' );
            $weather_info_wrapper_element = apply_filters( 'jb_weather_info_wrapper_element', 'section' );
            $weather_info_header_level = apply_filters( 'jb_weather_info_header_level', 'h2' );

            //API Data variables
            $weather_current_temperature = $weather_info->current_weather->temperature;
            $weather_current_units = $weather_info->current_weather_units->temperature;

            $content .= sprintf('<%1$s class="%2$s">', esc_html($weather_info_wrapper_element), esc_attr($weather_info_wrapper_class));
            $content .= sprintf('<%1$s>Weather Info</%1$s>', esc_html($weather_info_header_level));
            $content .= sprintf(
                    '<p>Temperature:
                                <span class="%1$s-temperature">%2$s</span>
                                <span class="%1$s-units">%3$s</span>
                    </p>',
                    esc_attr($weather_info_wrapper_class),
                    esc_html($weather_current_temperature),
                    esc_html($weather_current_units)
                );
            $content .= sprintf('</%s>', esc_html($weather_info_wrapper_element));
        }
        return $content;
    }

    /**
     * Reach out for weather information but, check for a transient first
     */
    private function get_weather_info($post_id)
    {
        $transient_name = "jb-weather-info-$post_id";
        $weather_info = get_transient($transient_name);

        if ($weather_info) {
            return $weather_info;
        } else {
            $location = $this->get_weather_location_post_meta($post_id);
            $weather_info = $this->do_weather_info_query($location['latitude'], $location['longitude']);
            set_transient($transient_name, $weather_info, $this->transient_timeout);
            return $weather_info;
        }
    }

    /**
     * Get weather location post meta and if it is blank return coordinates for Anchorage Alaska
     */
    private function get_weather_location_post_meta($post_id): array
    {
        $lat = get_post_meta($post_id, 'latitude', true) ?? $this->default_latitude;
        $lon = get_post_meta($post_id, 'longitude', true) ?? $this->default_longitude;
        return ['latitude' => $lat, 'longitude' => $lon];
    }


    /**
     * Get weather information from open-meteo.com using curl and return the json.
     * 
     * @param array $atts
     * @return string
     */
    private function do_weather_info_query($lat, $lon)
    {
        $query_params = array(
            'latitude' => $lat,
            'longitude' => $lon,
            'current_weather' => 'true',
            'temperature_unit' => 'fahrenheit'
        );
        $url = 'https://api.open-meteo.com/v1/forecast?' . http_build_query($query_params);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);
    }
}

