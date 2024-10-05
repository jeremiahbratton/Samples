<?php 
 /**
  * Markup for latitude and longitude meta fields
  */

  $latitude_field_data = get_post_meta($post->ID, 'latitude', true);
  $longitude_field_data = get_post_meta($post->ID, 'longitude', true);

  ?>

<!-- Nothing fancy, just a simple form with two fields for latitude and longitude -->
  <div>
      <label for="jb_weather_info_latitude">Latitude:</label>
      <input type="text" id="jb_weather_info_latitude" name="jb_weather_info_latitude" value="<?php echo esc_attr($latitude_field_data); ?>">
      <label for="jb_weather_info_longitude">Longitude:</label>
      <input type="text" id="jb_weather_info_longitude" name="jb_weather_info_longitude" value="<?php echo esc_attr($longitude_field_data); ?>">
  </div>
  