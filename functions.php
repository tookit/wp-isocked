<?php


add_filter( 'xmlrpc_enabled', '__return_false' );


/**
 * Loading All CSS Stylesheets and Javascript Files.
 *
 * @since v1.0
 */
function coder_scripts_loader()
{

    // 1. Style
    wp_enqueue_style('main', get_template_directory_uri() . '/style.css', array(), time(), 'all');
    // ScrollTrigger - with gsap.js passed as a dependency
    wp_enqueue_script('main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), time(), true);
}
add_action('wp_enqueue_scripts', 'coder_scripts_loader');


/**


/**
 *  Add custom elementor widgets
 */

 require_once('elementor/widgets.php');
 require_once('elementor/icons.php');

