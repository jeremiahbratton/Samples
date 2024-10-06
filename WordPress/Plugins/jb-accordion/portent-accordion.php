<?php
/**
 * Plugin Name:       Mobile Accordion Panel
 * Description:       A custom accordion block that is responsive and accessible.
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            jb
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       jb-accordion
 *
 * @package           jb-accordion
 */
namespace jb\Accordion;

//die haxxers
if(!defined('ABSPATH')){
    exit;
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/how-to-guides/block-tutorial/writing-your-first-block-type/
 */
function accordion_block_init() {
	register_block_type( __DIR__ );


}

add_action( 'init', __NAMESPACE__ . '\accordion_block_init' );



function accordion_block_fe_assets() {
    if (!is_admin()) {
        wp_enqueue_script(
            'jb-accordion-script',
            plugins_url('build/accordion.js', __FILE__),
            []
        );
    }
}

add_action( 'enqueue_block_assets', __NAMESPACE__ . '\accordion_block_fe_assets' );
