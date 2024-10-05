<?php 

/**
 * Replace your images with a random dog when you can't find one.
 * Completely impractical for production use. Amusue your friends, terrify your clients if they are afraid of dogs.
 */

 add_filter('wp_get_attachment_image_src', function ($image, $attachment_id, $size, $icon) {
    
    if (!$image) {
        $src = '';
        $width = null;
        $height = null;

        //Pull image sizes to check against so that we pass along a dogo at the desired size
        $get_size_details = get_intermediate_image_sizes();
        if (in_array($size, $get_size_details)) {
            $width = get_option($size . '_size_w');
            $height = get_option($size . '_size_h');
        }

        //connect to dog.ceo using cURL get a random dog image
        $url = 'https://dog.ceo/api/breeds/image/random';

        //Heh... fetch.
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response);

        if ($data->status === 'success') {
            $src = $data->message;
        }

        return [
            $src,
            $width,
            $height
        ];
    } else {
        return $image;
    }
}, 10, 4);