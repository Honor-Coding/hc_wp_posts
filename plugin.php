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

// general wordpress tools 
require_once HC_WP_POSTS_PATH . 'includes/class-hc-wpx.php';  

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
   
// TODO: it is not showing multiple ids     
/*    
    // do something here with debug 
    $cats1 = 'post-surgery,pre-surgery​';
    $cats2 = '15,16';
    $cats3 = '16,15';
    $cats4 = ['post-surgery','pre-surgery​'];
    $cats5 = ['15','16'];
    $cats6 = [15,16];
    $cats7 = ['15'];
    $cats8 = [15];
    $cats9 = [];
    $cats10 = '';
    $cats11 = ['post-surgery'];
            
    $debug['posts1'] = hc_posts()->get_posts_by_category( $cats1 );
    $debug['posts2'] = hc_posts()->get_posts_by_category( $cats2 );
    $debug['posts3'] = hc_posts()->get_posts_by_category( $cats3 );
    $debug['posts4'] = hc_posts()->get_posts_by_category( $cats4 );
    $debug['posts5'] = hc_posts()->get_posts_by_category( $cats5 );
    $debug['posts6'] = hc_posts()->get_posts_by_category( $cats6 );
    $debug['posts7'] = hc_posts()->get_posts_by_category( $cats7 );
    $debug['posts8'] = hc_posts()->get_posts_by_category( $cats8 );
    $debug['posts9'] = hc_posts()->get_posts_by_category( $cats9 );
    $debug['posts10'] = hc_posts()->get_posts_by_category( $cats10 );
    $debug['posts11'] = hc_posts()->get_posts_by_category( $cats11 );
*/    
    
    if ( ! empty( $debug ) && $iteration === 0 ) {
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