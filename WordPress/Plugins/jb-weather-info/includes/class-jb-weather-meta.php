<?php
/**
 * Class to setup and manage meta fields for the weather plugin
 */
class JB_Weather_Meta
{
    protected $plugin_name;
    public function __construct($plugin_name)
    {
        $this->plugin_name = $plugin_name;

    }

    /**
     * Add latitude and longitude meta fields to posts
     */
    public function add_latitude_longitude_meta_fields()
    {
        add_meta_box('jb_weather_info_metabox', 'Weather Location', array($this, 'latitude_longitude_meta_box_callback'), 'post', 'side', 'default');
    }

    /**
     * Callback function for latitude and longitude meta fields
     */
    public function latitude_longitude_meta_box_callback($post)
    {
        wp_nonce_field('jb_weather_info_metabox', 'jb_weather_info_metabox_nonce');
        include_once(JB_WEATHER_INFO_DIR . 'includes/template-metaboxes.php');
    }

    /**
     * Save latitude and longitude meta fields
     */
    public function save_latitude_longitude_meta_fields($post_id)
    {
        if (!isset($_POST['jb_weather_info_metabox_nonce'])) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (!wp_verify_nonce($_POST['jb_weather_info_metabox_nonce'], 'jb_weather_info_metabox')) {
            return;
        }

        $latitude_field = '';
        $longitude_field = '';

        if (isset($_POST['jb_weather_info_latitude'])) {
            $latitude_field = sanitize_text_field($_POST['jb_weather_info_latitude']);
        }

        if (isset($_POST['jb_weather_info_longitude'])) {
            $longitude_field = sanitize_text_field($_POST['jb_weather_info_longitude']);

        }

        update_post_meta($post_id, 'latitude', $latitude_field);
        update_post_meta($post_id, 'longitude', $longitude_field);
    }

}