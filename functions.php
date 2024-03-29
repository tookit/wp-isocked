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
 * Add google tag manager
 */
add_action('wp_head', 'custom_add_google_tag_manager_head', 0);
function custom_add_google_tag_manager_head()
{
    ?>
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-MM8HJ22');
    </script>

<?php
}
add_action('wp_footer', 'custom_google_tag_manager_no_js');

function custom_google_tag_manager_no_js()
{
?>
    <!-- Google Tag Manager (noscript)-->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MM8HJ22" height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->
<?php
}



/**
 *  Add custom elementor widgets
 */

 require_once('elementor/widgets.php');
 require_once('elementor/icons.php');

