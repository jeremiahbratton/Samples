<?php
/**
 * This file is provided as a sample and was used within a theme to setup 
 * a card carousel usig glider.js via the ACF Pro plugin.
 */
$base_class= "block-explore-case-studies";
$classes = [$base_class, 'alignfull'];
if( !empty( $block['className'] ) )
	$classes = array_merge( $classes, explode( ' ', $block['className'] ) );

$anchor = '';
if( !empty( $block['anchor'] ) )
	$anchor = ' id="' . sanitize_title( $block['anchor'] ) . '"';

$studies = get_field('studies');

if($studies) {
	echo'<div class="' . join(' ', $classes) . '"' . $anchor . '>';
	foreach($studies as $study) {
        $title = $study['title'];
        $case_study = $study['case_study'][0];
        $excerpt = $case_study->post_excerpt ? $case_study->post_excerpt : $case_study->post_title;
        $permalink = get_the_permalink( $case_study->ID );
        $image = wp_get_attachment_image(
            get_post_thumbnail_id($case_study->ID),
            'large', 
            false,
            ["class" => "block-explore-case-studies__image", "style" => "aspect-ratio:1/1"]
        );

        echo '<a class="'. $base_class.'__link" href="'. get_the_permalink( $case_study->ID ).'">';
            echo '<div class="'. $base_class.'__content">';
                echo '<h2 class="'. $base_class.'__title">'. $title .'</h2>';
                echo '<p class="'. $base_class.'__excerpt">' . $excerpt . '</p>'; 
            echo '</div>';
            echo $image;
        echo '</a>';
    }
	echo '</div>';
    echo '<div class="'. $base_class.'__controls">';
        echo '<button class="'. $base_class.'__prev" aria-label="show previous case study"><img alt="Show previous case study" src="'.get_template_directory_uri() . '/assets/icons/utility/white-left-button.svg" /></button>';
        echo '<button class="'. $base_class.'__next" aria-label="show next case study"><img alt="Show next case study" src="'.get_template_directory_uri() . '/assets/icons/utility/white-right-button.svg" /></button>';
        echo '<div class="'. $base_class.'__status" data-slides="'.count($studies).'" ><span class="current">01</span> / <span class="total">04</span></div>';
    echo '</div>';
}
