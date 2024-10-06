<?php
/**
 * Compact Newsletter Signup Button Block
 *
 * @package      jeremiahBratton
 * @author       Jeremiah Braton
 * @since        1.0.0
 * @license      GPL-2.0+
 **/

$button_text = get_field( 'button_text' );

echo '<div class="block-newsletter">';
	echo '<button class="block-newsletter__button wp-block-button__link has-dark-blue-background-color has-background" aria-expanded="false">'. esc_html( $button_text ) .'</button>';
echo '<div class="block-newsletter__form-container hidden">';
	echo '<div id="hs_form_target_module_1531514890824345_block_subscribe_8504" class="block-newsletter__form"></div>';
echo '</div>';
	echo '<button class="block-newsletter__close-button"><img width="10" height="10" class="icon block-newsletter__close-button__close-icon" src="'. get_template_directory_uri() .'/assets/icons/utility/close-white.svg" role="presentation"/> Close</button>';
echo '</div>';
