<?php

class JB_Weather_Info {

    protected $plugin_name;

    public function init() {
        $this->plugin_name = 'jb-weather-info';

        $this->load_dependencies();
        $this->admin_hooks();
        $this->public_hooks();
    }

    private function load_dependencies() {

        // Load public files
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jb-weather-content.php';

        // Load admin files
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jb-weather-meta.php';
    }

    private function admin_hooks() {
        $jb_weather_meta = new JB_Weather_Meta( $this->plugin_name );

        add_action('add_meta_boxes', array( $jb_weather_meta, 'add_latitude_longitude_meta_fields') );
        add_action('save_post', array( $jb_weather_meta, 'save_latitude_longitude_meta_fields') );
    }

    private function public_hooks() {
        $jb_weather = new JB_Weather_Content( $this->plugin_name );

        add_filter('the_content', array( $jb_weather, 'add_weather_section_to_content' ) );
    }
}