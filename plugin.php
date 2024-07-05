<?php
/**
 * Plugin Name: WP Posts
 * Description: Displays Wordpress posts.
 * Version: 1.0
 * Author: Honor Coding
 * Author URI:   https://honorcoding.com/
 * Text Domain:  hc_wp_posts
 * Domain Path:  /
*/


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}


// declare to dependencies that the plugin has loaded
define( 'HC_WP_POSTS', TRUE );

// note: final character is a slash
define( 'HC_WP_POSTS_URL', plugin_dir_url(__FILE__) );     
define( 'HC_WP_POSTS_PATH', plugin_dir_path(__FILE__) );   



// --------------------------------------------------
// PLUGIN TOOLS
// --------------------------------------------------

// handles post data 
require_once HC_WP_POSTS_PATH . 'includes/class-hc-posts.php';  
function hc_posts() {
    return \HonorCoding\WP_Posts\HC_Posts::instance();
}



// --------------------------------------------------
// ENQUEUE STYLES AND SCRIPTS 
// --------------------------------------------------
function hcwp_enqueue_styles_and_scripts() {
    
    // styles
    $css_slug = "hc-wp-posts-style"; 
    $css_uri = HC_WP_POSTS_URL . 'assets/css/style.css';
    $css_filetime = filemtime( HC_WP_POSTS_PATH . 'assets/css/style.css' );
    wp_register_style( $css_slug, $css_uri, array(), $css_filetime );
    wp_enqueue_style( $css_slug ); 
    
    // scripts 
    $js_slug = "hc-wp-posts-scripts"; 
    $js_uri = HC_WP_POSTS_URL . 'assets/js/scripts.js';
    $js_filetime = filemtime( HC_WP_POSTS_PATH . 'assets/js/scripts.js' );
    wp_register_script( $js_slug, $js_uri, array('jquery'), $js_filetime, true );    
    wp_enqueue_script( $js_slug );   

    // allow script to send ajax requests 
    // Localize the script with new data
    $ajaxsettings = array(
        'ajax_url' => admin_url('admin-ajax.php'),
    );
    wp_localize_script( $js_slug, 'hc-wp-posts', $ajaxsettings );

}
add_action( 'wp_enqueue_scripts', 'hcwp_enqueue_styles_and_scripts' );






// --------------------------------------------------
// DEBUG TOOLS
// --------------------------------------------------
add_action( 'wp_footer', 'hcwp_debug_in_footer' );
global $debug;
function hcwp_debug_in_footer() {
    
    global $debug;
    
    // do something here with debug     
    
    if ( ! empty( $debug ) ) {
        echo hcwp_dump( $debug );
    }
    
}
function hcwp_dump( $var ) {
    ob_start();
    print_r( $var );
    $results = ob_get_clean();
    $results = '<pre>' . $results . '</pre>';
    return $results;    
}