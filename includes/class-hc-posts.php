<?php
// -----------------------------------------------------------
// HC_Posts - Class
// -----------------------------------------------------------
// 
//  Purpose: 
//      Expedites retrieval of posts by simplifying parameters 
//      Converts complex WP_Post objects into ready-to-use arrays 
//
// -----------------------------------------------------------
 
namespace HonorCoding\WP_Posts;

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! class_exists( 'HC_Posts' ) ) {

	class HC_Posts {

            
                // this class only needs to be instantiated once 
                private static $_instance = null;
                

                // -----------------------------------------------------------
                // INSTANTIATION 
                // -----------------------------------------------------------

                // Return an instance of this class 
                // grabbing the instance prevents the need for a global variable to reload multiple times on every page
                public static function instance() {

                    // If the single instance hasn't been set, set it now.
                    if ( is_null( self::$_instance ) ) {
                        self::$_instance = new self();
                    }

                    return self::$_instance;

                }

		public function __construct() {
                }

                
                // -----------------------------------------------------------
                // GET POST DATA 
                // -----------------------------------------------------------

                /**
                 * gets a list of posts that match the categories 
                 * 
                 * @param array/string $categories - can be an array or a comma-separated string 
                 *                                 - can be names, slugs or ids 
                 * @return array of posts (see get_posts)
                 */
                public function get_posts_by_category( $categories ) {
                    
                    $posts = [];
                    
                    // convert a string to an array 
                    if ( ! is_array( $categories ) ) {
                        $categories = \WPX::simple_explode( $categories );
                    }
                    
                    if ( ! empty ( $categories ) ) {
                        
                        $args = [
                            // TODO:
                            // do something depending on whether names, slugs or ids 
                        ];
                        
                        $posts = $this->get_posts( $args );
                        
                    }
                    
                    return $posts;
                    
                }
                
                
                /**
                 * gets a list of posts, converted to array (not wp_post objects) 
                 * 
                 * @param array $args
                 * @return array of [ 'id', 'title', 'excerpt', 'permalink', 'image' ]
                 */
                public function get_posts( $args ) {
                    
                    $posts = [];
                    
                    // TODO: 
                    // make adjustments to $args as needed...
                    
                    $wp_posts = get_posts( $args );

                    foreach( $wp_posts as $wp_post ) {

                        // TODO:
                        // convert WP_Posts objects into an array called $posts
                        // get id, title, excerpt, image (sized), permalink... what else? 

                    }    
                    
                    return $posts;
                    
                }                
                
                
        } // end: class HC_Posts        

} // end: if ! class_exists ( 'HC_Posts' )

                