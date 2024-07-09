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
                public function get_posts_by_category( $categories = '' ) { 
                    
                    $posts = [];
                    
                    // convert a string to an array 
                    if ( ! is_array( $categories ) ) {
                        $categories = \WPX::simple_explode( $categories );
                    }

                    // check if a list of names, slugs or ids 
                    $cat_ids = [];
                    foreach( $categories as $cat ) {
               
                        // if numeric, then assume ids 
                        $is_number = (int)$cat;
                        if ( $is_number !== 0 && is_numeric( $is_number ) ) {
                            $cat_ids[] = $is_number;                            
                        // if not numeric,
                        } else {
                            // check for name 
                            $term = get_term_by('name', $cat, 'category');
                            if ( $term ) {
                                $cat_ids[] = $term->term_id;
                            } else {
                                // if not name, check for slug 
                                $term = get_term_by('slug', $cat, 'category');
                                if ( $term ) {
                                    $cat_ids[] = $term->term_id;
                                }
                            }                        
                        }
                        
                    }
                
                    if ( ! empty ( $cat_ids ) ) {
                        
                        $args = [
                            'category__in' => \WPX::simple_implode( $cat_ids ),
                        ];
                        
                        $posts = $this->get_posts( $args );
                        
                    }
                    
                    return $posts;
                    
                } // end : get_posts_by_category()
                
                
                /**
                 * gets a list of posts, converted to array (not wp_post objects) 
                 * 
                 * @param array $args : wp_query args (see: https://developer.wordpress.org/reference/classes/wp_query/) 
                 * @param string $thumb_size : options = { post-thumbnail, ... }
                 * @return array of [ 'id', 'title', 'excerpt', 'permalink', 'image' ]
                 */
                public function get_posts( $args = [], $thumb_size = 'post-thumbnail' ) {
                    
                    $posts = [];
                    
                    if ( ! is_array ( $args ) ) {
                        $args = [];
                    }
                    
                    if ( ! isset( $args['post_status'] ) ) {
                        $args['post_status'] = 'publish'; // show only published posts
                    }
                    
                    if ( ! isset( $args['posts_per_page'] ) ) {
                        $args['posts_per_page'] = '-1'; // show all posts (not paginated) 
                    }
                    
                    $wp_posts = get_posts( $args );

                    foreach( $wp_posts as $wp_post ) {
                        
                        $thumbnail = get_the_post_thumbnail_url( $wp_post->ID, $thumb_size );
                        
                        $posts[] = [
                            'id' => $wp_post->ID,
                            'title' => $wp_post->post_title,
                            'excerpt' => $wp_post->post_excerpt,
                            'link' => get_permalink( $wp_post->ID ),
                            'thumbnail' => ( $thumbnail ) ? $thumbnail : '',
                        ];

                    }    
                    
                    return $posts;
                    
                }                
                
                
        } // end: class HC_Posts        

} // end: if ! class_exists ( 'HC_Posts' )

                