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

/**
 * GDPR Consent Banner Implementation
 */

// Enqueue consent banner assets
function enqueue_gdpr_consent_banner() {
    // Only load for EEA visitors
    if (is_eea_visitor()) {
        wp_enqueue_script(
            'gdpr-consent-banner', 
            get_template_directory_uri() . '/assets/js/consent-banner.js', 
            array('jquery'), 
            '1.0.2', 
            true
        );
        
        wp_enqueue_style(
            'gdpr-consent-banner', 
            get_template_directory_uri() . '/assets/css/consent-banner.css', 
            array(), 
            '1.0.2'
        );
        
        // Pass data to JavaScript
        wp_localize_script('gdpr-consent-banner', 'gdprAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gdpr_consent_nonce'),
            'privacyUrl' => get_privacy_policy_url(),
            'cookiePolicyUrl' => home_url('/cookie-policy/'), // Update this URL
        ));
    }
}
add_action('wp_enqueue_scripts', 'enqueue_gdpr_consent_banner');

// EEA Detection (basic implementation - enhance with geolocation service)
function is_eea_visitor() {
    // Basic detection - you should use a proper geolocation service
    $eea_countries = array(
        'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR',
        'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL',
        'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'IS', 'LI', 'NO'
    );
    
    // Get visitor's country code (implement proper geolocation)
    $visitor_country = get_visitor_country_code();
    
    return in_array($visitor_country, $eea_countries);
}

// Get visitor country code (placeholder - implement with MaxMind, CloudFlare, etc.)
function get_visitor_country_code() {
    // For development/testing, always return an EEA country
    if (WP_DEBUG) {
        return 'DE'; // Germany
    }
    $ip = $_SERVER['REMOTE_ADDR'];
    $response = wp_remote_get("http://ip-api.com/json/{$ip}?fields=countryCode");
    
    if (!is_wp_error($response)) {
        $data = json_decode(wp_remote_retrieve_body($response), true);
        return $data['countryCode'] ?? 'US';
    }
    
    
    // Implement actual geolocation here
    // Example with CloudFlare:
    // return $_SERVER['HTTP_CF_IPCOUNTRY'] ?? 'US';
    
    // Example with MaxMind GeoIP2:
    // $reader = new \GeoIp2\Database\Reader('/path/to/GeoLite2-Country.mmdb');
    // $record = $reader->country($_SERVER['REMOTE_ADDR']);
    // return $record->country->isoCode;
    
    return 'US'; // Default to non-EEA
}

// AJAX handler to save consent
add_action('wp_ajax_save_gdpr_consent', 'handle_save_gdpr_consent');
add_action('wp_ajax_nopriv_save_gdpr_consent', 'handle_save_gdpr_consent');

function handle_save_gdpr_consent() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'gdpr_consent_nonce')) {
        wp_die('Security check failed');
    }
    
    $user_id = get_current_user_id();
    $consent_data = array(
        'essential' => true, // Always true
        'analytics' => isset($_POST['analytics']) ? (bool)$_POST['analytics'] : false,
        'advertising' => isset($_POST['advertising']) ? (bool)$_POST['advertising'] : false,
        'personalization' => isset($_POST['personalization']) ? (bool)$_POST['personalization'] : false,
        'timestamp' => current_time('timestamp'),
        'ip' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'version' => '1.0'
    );
    
    if ($user_id > 0) {
        // Save for logged-in users
        update_user_meta($user_id, 'gdpr_consent', $consent_data);
    } else {
        // Save for anonymous users (you might want to use a custom table)
        $session_id = session_id();
        if (empty($session_id)) {
            session_start();
            $session_id = session_id();
        }
        set_transient('gdpr_consent_' . $session_id, $consent_data, YEAR_IN_SECONDS);
    }
    
    wp_send_json_success($consent_data);
}

// AJAX handler to get consent
add_action('wp_ajax_get_gdpr_consent', 'handle_get_gdpr_consent');
add_action('wp_ajax_nopriv_get_gdpr_consent', 'handle_get_gdpr_consent');

function handle_get_gdpr_consent() {
    if (!wp_verify_nonce($_POST['nonce'], 'gdpr_consent_nonce')) {
        wp_die('Security check failed');
    }
    
    $user_id = get_current_user_id();
    $consent_data = null;
    
    if ($user_id > 0) {
        $consent_data = get_user_meta($user_id, 'gdpr_consent', true);
    } else {
        $session_id = session_id();
        if (empty($session_id)) {
            session_start();
            $session_id = session_id();
        }
        $consent_data = get_transient('gdpr_consent_' . $session_id);
    }
    
    wp_send_json_success($consent_data);
}

// Add consent banner HTML to footer
function add_gdpr_consent_banner() {
    if (is_eea_visitor()) {
        get_template_part('template-parts/consent-banner');
    }
}
add_action('wp_footer', 'add_gdpr_consent_banner');

// Google Consent Mode v2 integration
function add_google_consent_mode() {
    if (is_eea_visitor()) {
        ?>
        <!-- Google Consent Mode v2 -->
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        
        // Set default consent states for EEA users
        gtag('consent', 'default', {
            'analytics_storage': 'denied',
            'ad_storage': 'denied',
            'ad_user_data': 'denied',
            'ad_personalization': 'denied',
            'personalization_storage': 'denied',
            'functionality_storage': 'granted',
            'security_storage': 'granted',
            'wait_for_update': 500
        });
        </script>
        <?php
    } else {
        // Non-EEA users - grant all consent by default
        ?>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        
        gtag('consent', 'default', {
            'analytics_storage': 'granted',
            'ad_storage': 'granted',
            'ad_user_data': 'granted',
            'ad_personalization': 'granted',
            'personalization_storage': 'granted',
            'functionality_storage': 'granted',
            'security_storage': 'granted'
        });
        </script>
        <?php
    }
}
add_action('wp_head', 'add_google_consent_mode', 1);