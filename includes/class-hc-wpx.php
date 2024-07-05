<?php 

// -----------------------------------------------------------
// WPX : Class
//
// Purpose:
//     Extended Wordpress Tools
//
// How to Use: 
//     This class only contains static functions. 
//     e.g. \WPX::get_template( $template );
// -----------------------------------------------------------


// no unauthorized access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


if ( ! class_exists( 'WPX' ) ) :
    
    class WPX {
        

        const TEMPLATE_FOLDER_PLUGIN = HC_WP_POSTS_PATH . 'templates/';
        const TEMPLATE_FOLDER_THEME  = '/templates/';


        /** 
         * get_template - returns the contents of a template file
         *                allows template files to be stored in the theme as well as the plugin
         *
         * How it works: 
         *      first, it searches the theme folder: /templates/hc_wp_posts/ 
         *      if not found, then it searches the plugin /templates/ folder 
         * 
         * @param $template - the name of the template 
         * @param $args - an array of args to be passed to the template 
         * @param $location - an additional path to search in the "templates" folder 
         * 
         * @return string - returns the contents of the template file, 
         *                  or an empty string if the template file is not found
         */
        public static function get_template( $template, $args = array(), $location = '' ) {

            $contents = ''; // return an empty string if file not found

            $template_path = '';
            $location = ( $location !== '' ) ? trim( $location ) . '/' : '';
            $path = $location . trim( $template ) . '.php';

            // first look in the theme template folder 
            $theme_path = get_stylesheet_directory() . self::TEMPLATE_FOLDER_THEME . $path;
            if ( file_exists( $theme_path ) ) {
                $template_path = $theme_path;
            }
            
            // if not found, then look in the plugin template folder 
            if ( $template_path === '' ) {
                $plugin_path = self::TEMPLATE_FOLDER_PLUGIN . $path;
                if ( file_exists ( $plugin_path ) ) {
                    $template_path = $plugin_path;
                }
            }

            // if template exists, then get the contents of the template file 
            // (otherwise, simply returns an empty string)             
            if ( $template_path !== '' ) {
                ob_start();    
                    include $template_path;
                $contents = ob_get_clean();
            }
            
            return $contents;

        }


        /**
            * Simplifies PHP explode 
            * (internally handles errors and removes empty strings)
            * 
            * @param string $delimiter - token separator 
            * @param string $string - string to tokenize
            * 
            * @return array (returns an empty array if no valid tokens found) 
            */
        public static function simple_explode( $string, $delimiter = ',' ) {

            if ( $delimiter !== '' ) { 
                $array = explode( $delimiter, $string ); 
            } else {
                $array = array();
            }

            // get rid of empty strings
            foreach( $array as $key => $value ) {
                $value = trim( $value );
                if ( $value === '' ) {
                    unset( $array[ $key ] );
                }
            }

            return $array;

        }


        /**
            * Simplifies PHP implode
            * (internally handles errors and removes empty strings)
            * 
            * @param string $glue - token separator 
            * @param array $array - join $array elements into a string
            * 
            * @return string (returns an empty string if no elements found) 
            */
        public static function simple_implode( $array, $glue = ',' ) {

            $string = '';

            if ( is_array( $array ) ) {
                foreach( $array as $token ) {
                    $token = trim( $token );
                    if ( $token && $token !== '' ) {
                        $string = ( $string !== '' ) ? $string . $glue . $token : $token; 
                    }
                }
            }

            return $string;

        }   


        // -------------------------------------------------------
        // ARRAY TOOLS 
        // -------------------------------------------------------
        
        /**
         * find duplicate entries in an array
         * 
         * WHEN:
         *  - works with linear arrays: 
         *      [ 'duplicate', item2', item3', 'duplicate' ] 
         * 
         *  - works with object-style arrays: 
         *      [ 
         *          'item1' => 
         *              [ 'index1' => 'duplicate', 'index2' => 'value2' ], 
         *          'item2' => 
         *              [ 'index1' => 'value3', 'index2' => 'value4' ],  
         *          'item3' => 
         *              [ 'index1' => 'duplicate', 'index2' => 'value2' ]
         *      ] 
         * 
         * HOW TO USE: 
         *  - linear arrays 
         *      find_duplicates( $array ); 
         * 
         *  - object-style arrays 
         *      find_duplicates( $array, 'index1' );
         * 
         * RETURNS: 
         *  - a list of duplicates, referenced by their numerical index 
         *      e.g. [ 
         *              0 => [ 1, 4, 5 ],
         *              1 => [ 3, 7 ]
         *           ] 
         *      so... 
         *          $array[1] === $array[4] === $array[5] 
         *          $array[3] === $array[7]
         * 
         * @param array $array
         * @param string $index (optional) - if index is 
         * @return array of lists
         */
        public static function find_duplicates( $array, $index = '' ) {

            $array_length = count( $array );

            $is_dup = [];
            $dups = [];

            for ($i = 0; $i < $array_length; $i++) {

                // if $i is already listed as a duplicate, then continue 
                if ( in_array( $i, $is_dup ) ) {
                    continue;
                }

                for ($j = $i + 1; $j < $array_length; $j++) {

                    // if $j is already listed as a duplicate, then continue 
                    if ( in_array( $j, $is_dup ) ) {
                        continue;
                    }

                    if ( $index !== '' ) {

                        if ($array[$i][$index] == $array[$j][$index] ) {
                            // add to duplicates list     
                            if ( ! isset( $dups[$i] ) ) {
                                $dups[$i][] = $i;
                                $is_dup[] = $i;
                            }
                            $dups[$i][] = $j;
                            $is_dup[] = $j;
                        }            

                    } else {

                        if ($array[$i] == $array[$j]) {
                            // add to duplicates list     
                            if ( ! isset( $dups[$i] ) ) {
                                $dups[$i][] = $i;
                                $is_dup[] = $i;
                            }
                            $dups[$i][] = $j;
                            $is_dup[] = $j;
                        }

                    }

                } // end : for $j 

            } // end : for $i

            return $dups;

        } // end : find_duplicates()
        
        
        /**
            * Inserts an element into an array before the position of the insert_key
            * Note: 
            *      - Only works on arrays of key=>value pairs
            * 
            * @params
            *      - (array) $array = the array to be inserted into 
            *      - (string) $insert_before = array key to insert before 
            *                                  (must be the array key, not the numeric index,
            *                                  unless the numeric index is the array key)
            *      - (array) $new_array = an array of key->value pairs to be inserted 
            * 
            * @returns 
            *      - (array) the new array with the inserted value 
            */
        public static function array_insert( $array, $insert_before, $new_array ) {

            // get the numeric index from the array key 
            $keys = array_keys( $array); 
            $array_index = -1;
            foreach( $keys as $index => $key ) {
                if ( $key === $insert_before ) {
                    $array_index = $index;
                    break;
                }        
            }

            // split the array into two halfs 
            if ( $array_index > -1 ) {

                $half_one = array_slice( $array, 0, $array_index );
                $half_two = array_slice( $array, $array_index );

            } else {

                $half_one = $array;
                $half_two = [];

            }

            // combine the arrays
            $results = array_merge( $half_one, $new_array, $half_two );

            return $results;

        }    


        // -------------------------------------------------------
        // URL TOOLS 
        // -------------------------------------------------------
        public static function is_current_page( $slug ) {
            
            $i = strpos( self::get_page_slug(), $slug );
            
            if ( 
                    $i !== false //&& $i === 0 
                ) {
                return true;
            } else {
                return false;
            }
            
        }

        public static function get_current_url() {
            
            $p = self::parse_current_url();
            $url = $p['site'] . $p['path'] . $p['query'];
            return $url;
            
        }

        /**
         * 
         * @return array [ 'https', 'domain', 'site', 'path', 'query' ]
         */
        public static function parse_current_url() {

            $url = array();
            
            $url['https'] = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . '://';
            $url['domain'] = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : '';

            $url['site'] = $url['https'] . $url['domain'];

            $request = wp_parse_url( $_SERVER['REQUEST_URI'] );
            
            $url['path'] = ( isset( $request['path'] ) ) ? $request['path'] : '';    
            $url['query'] = ( isset( $request['query'] ) ) ? $request['query'] : '';    

            return $url;

        }
        
        public static function parse_url_query( $query ) {

            $params = array();
            parse_str( $query, $params );
            return $params;

        }        

        /**
         * gets the page slug 
         * 
         * Example: 
         *      If the current page is: https://innerworkspro.com/portal/support/?test=test
         *      The results is: /portal/support/
         */
        public static function get_page_slug( $url = '' ) {
            
            $slug = '';

            if ( $url === '' ) {
                
                // get the current url slug if no url specified 
                $tokens = self::parse_current_url();
                
            } else {
                
                $tokens = wp_parse_url( $url );
                
            }

            if( is_array( $tokens ) && isset( $tokens['path'] ) ) {
                $slug = $tokens['path'];
            }
            
            // remove prefix slash
            if (substr( $slug, 0, 1 ) == '/') {
                $slug = substr( $slug, 1 );
            } 

            // remove postfix(?) slash
            if (substr( $slug, -1, 1 ) == '/') {
                $slug = substr( $slug, 0, strlen($slug) - 1 );
            } 

            // make consistent
            $slug = strtolower( $slug );

            return $slug;
            
        }     
        
        public static function recombine_parsed_url( $tokens ) {
            
            $url = '';
            $url .= ( isset( $tokens['scheme'] ) ) ? $tokens['scheme'] . '://' : '';
            $url .= ( isset( $tokens['host'] ) ) ? $tokens['host'] : ''; 
            $url .= ( isset( $tokens['path'] ) ) ? $tokens['path'] : ''; 
            $url .= ( isset( $tokens['query'] ) ) ? '?' . $tokens['query'] : ''; 
            return $url;
            
        }
        
        /**
         * replaces the value of $param in the $url 
         * 
         * Example: 
         *      update_url_param( 'site.com/folder/?test=one', 'test', 'two' ); 
         *      // returns 'site.com/folder/?test=two'
         * 
         * @param string $url
         * @param string $param
         * @param string $value
         * 
         * @returns string $updated_url
         */
        public static function update_url_param( $url, $param, $value ) {  
            
            // parse the url and convert the query string into an array 
            $tokens = wp_parse_url( $url );
            if( is_array( $tokens ) && isset( $tokens['query'] ) ) {                   
                $query = self::parse_url_query( $tokens['query'] );
            } else {
                $query = [];
            }
            
            // merge the updated value with the query 
            $updated_param = [ $param => $value ];
            $new_query = array_merge( $query, $updated_param );
            
            // mergy the $new_query array into a string
            $param_value_pairs = [];
            foreach( $new_query as $item => $value ) {
                if ( $item !== '' && $value !== '' ) {
                    $param_value_pairs[] = $item . '=' . $value;
                }
            }
            $new_query_string = self::simple_implode( $param_value_pairs, '&' );
            
            // recombine the url 
            $tokens['query'] = $new_query_string;
            $updated_url = self::recombine_parsed_url( $tokens );
            
            return $updated_url;
            
        }

        // removes the param from the url 
        public static function remove_url_param( $url, $param ) {
            
            return self::update_url_param( $url, $param, '' );
            
        }

    } // end class: WPX

endif;


