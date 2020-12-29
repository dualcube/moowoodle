<?php

if(!function_exists('moowoodle_alert_notice')) {
   function moowoodle_alert_notice() {
    ?>
    <div id="message" class="error">
      <p><?php printf( __( '%sMooWoodle is inactive.%s The %sWooCommerce plugin%s must be active for the MooWoodle to work. Please %sinstall & activate WooCommerce%s', 'moowoodle' ), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . admin_url( 'plugins.php' ) . '">', '&nbsp;&raquo;</a>' ); ?></p>
    </div>
    <?php
  }
}

/**
 * Required moodle core functions.
 *
 * @param string $key (default: null)
 * @return array/string
 */
if ( ! function_exists( 'moowoodle_get_moodle_core_functions' ) ) {
  function moowoodle_get_moodle_core_functions( $key = '' ) {
    $moodle_core_functions = array( 'get_categories' => 'core_course_get_categories',
                                    'get_courses'  => 'core_course_get_courses',
                                    'get_moodle_users'    => 'core_user_get_users',
                                    'create_users'   => 'core_user_create_users',
                                    'update_users'   => 'core_user_update_users',
                                    'enrol_users'  => 'enrol_manual_enrol_users'
                                  );
    
    if ( empty( $key ) ) {
      return $moodle_core_functions;
    } else if ( array_key_exists( $key, $moodle_core_functions ) ) {
      return $moodle_core_functions[ $key ];
    }    
    return null;
  }
}

/**
 * Call to moodle core functions.
 *
 * @param string $function_name (default: null)
 * @param string $request_param (default: null)
 * @return mixed
 */
if ( ! function_exists( 'moowoodle_moodle_core_function_callback' ) ) {
  function moowoodle_moodle_core_function_callback( $function_name = '', $request_param = array() ) {
    global $MooWoodle;
    
    $response = null;

    $conn_settings = $MooWoodle->options_general_settings;
    $url = $conn_settings[ 'moodle_url' ];
    $token = $conn_settings[ 'moodle_access_token' ];
   
    if ( $function_name == 'core_user_get_users' ) {
      $request_url = $url . '/webservice/rest/server.php?wstoken=' . $token . '&wsfunction=' . $function_name . '&moodlewsrestformat=json&criteria[0][key]=email&criteria[0][value]=%%';
    } else{
      $request_url = $url . '/webservice/rest/server.php?wstoken=' . $token . '&wsfunction=' . $function_name . '&moodlewsrestformat=json';
    }
        
    if ( ! empty( $url )  && ! empty( $token ) && $function_name != '' ) {
      $request_query = http_build_query( $request_param );
      $response = wp_remote_post( $request_url, array( 'body' => $request_query ) );
    } 
    
    if ( ! is_wp_error( $response ) && $response != null && $response[ 'response' ][ 'code' ] == 200 ) {
      if ( is_string( $response[ 'body' ] ) ) {
        $response_arr = json_decode( $response[ 'body' ], true );
        
        if ( json_last_error() === JSON_ERROR_NONE ) {
          if ( is_null( $response_arr ) || ! array_key_exists( 'exception', $response_arr ) ) {
            $MooWoodle->ws_has_error = false;
            return $response_arr;
          } else {
            $MooWoodle->ws_has_error = true;
          }
        } else {
          $MooWoodle->ws_has_error = true;
        }
      } else {
        $MooWoodle->ws_has_error = true;
      }
    } else {
      $MooWoodle->ws_has_error = true;
    }    
    return null;
  }
}

/**
 * Returns term id by moodle category id
 *
 * @param int $category_id
 * @param string $taxonomy (default: null)
 * @param string $meta_key (default: null)
 * @return int
 */
function moowoodle_get_term_by_moodle_id( $category_id, $taxonomy = '', $meta_key = '' ) {
  if ( empty( $category_id ) || ! is_numeric( $category_id ) || empty( $taxonomy ) || ! taxonomy_exists( $taxonomy ) || empty( $meta_key ) ) {
    return 0;
  }

  $terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
  if ( $terms ) {
    foreach ( $terms as $term ) {
      if ( apply_filters( "moowoodle_get_{$meta_key}_meta", $term->term_id, '_category_id', true ) == $category_id ) {
        return $term->term_id;
      }
    }
  }
  return 0;
}

/**
 * Returns post id by moodle category id.
 *
 * @param int $course_id
 * @param string $post_type (default: null)
 * @return int
 */
function moowoodle_get_post_by_moodle_id( $course_id, $post_type = '' ) {
  if ( empty( $course_id ) || ! is_numeric( $course_id ) || empty( $post_type ) || ! post_type_exists( $post_type ) ) {
    return 0;
  }
  $posts = get_posts( array( 'post_type' => $post_type, 'numberposts' => -1 ) );

  if ( $posts ) {
    foreach ( $posts as $post ) {
      if ( get_post_meta( $post->ID, 'moodle_course_id', true ) == $course_id ) {
        return $post->ID;
      }
    }
  }
  return 0;
}

/**
 * Woodle Term Meta API - Get term meta
 *
 * @param mixed $term_id
 * @param string $key
 * @param bool $single (default: true)
 * @return mixed
 */
function get_moowoodle_term_meta( $term_id, $key, $single = true ) {
  return get_metadata( 'moowoodle_term', $term_id, $key, $single );
}

add_filter( 'moowoodle_get_moowoodle_term_meta', 'get_moowoodle_term_meta', 10, 3 );
add_filter( 'moowoodle_get_woocommerce_term_meta', 'get_term_meta', 10, 3 );

/**
 * Woodle Term Meta API - set table name
 *
 * @return void
 */
function moowoodle_taxonomy_metadata_wpdbfix() {
  global $wpdb;
  
  $termmeta_name = 'moowoodle_termmeta';
  $wpdb->moowoodle_termmeta = $wpdb->prefix . $termmeta_name;
  $wpdb->tables[] = 'moowoodle_termmeta';
  
}

add_action( 'init', 'moowoodle_taxonomy_metadata_wpdbfix', 0 );
add_action( 'switch_blog', 'moowoodle_taxonomy_metadata_wpdbfix', 0 );

/**
 * Woodle Term Meta API - Add term meta
 *
 * @param mixed $term_id
 * @param mixed $meta_key
 * @param mixed $meta_value
 * @param bool $unique (default: false)
 * @return bool
 */
function add_moowoodle_term_meta( $term_id, $meta_key, $meta_value, $unique = false ) {
  return add_metadata( 'moowoodle_term', $term_id, $meta_key, $meta_value, $unique );
}

add_filter( 'moowoodle_add_moowoodle_term_meta', 'add_moowoodle_term_meta', 10, 4 );
add_filter( 'moowoodle_add_woocommerce_term_meta', 'add_term_meta', 10, 4 );

/**
 * Woodle Term Meta API - Update term meta
 *
 * @param mixed $term_id
 * @param string $meta_key
 * @param mixed $meta_value
 * @param string $prev_value (default: '')
 * @return bool
 */
function update_moowoodle_term_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {
  return update_metadata( 'moowoodle_term', $term_id, $meta_key, $meta_value, $prev_value );
}

add_filter( 'moowoodle_update_moowoodle_term_meta', 'update_moowoodle_term_meta', 10, 4 );
add_filter( 'moowoodle_update_woocommerce_term_meta', 'update_term_meta', 10, 4 );

/**
 * Woodle Term Meta API - Delete term meta
 *
 * @param mixed $term_id
 * @param mixed $meta_key
 * @param string $meta_value (default: '')
 * @param bool $delete_all (default: false)
 * @return bool
 */
function delete_moowoodle_term_meta( $term_id, $meta_key, $meta_value = '', $delete_all = false ) {
  return delete_metadata( 'moowoodle_term', $term_id, $meta_key, $meta_value, $delete_all );
}

add_filter( 'moowoodle_delete_moowoodle_term_meta', 'delete_moowoodle_term_meta', 10, 4 );
add_filter( 'moowoodle_delete_woocommerce_term_meta', 'delete_woocommerce_term_meta', 10, 4 );

/**
 * When a term is deleted, delete its meta.
 *
 * @param mixed $term_id
 * @return void
 */
function moowoodle_delete_term_meta( $term_id ) {
  global $wpdb;
  
  $term_id = (int) $term_id;
  if ( ! $term_id ) {
    return;
  }
  $wpdb->get_results( "DELETE FROM {$wpdb->moowoodle_termmeta} WHERE `moowoodle_term_id` = {$term_id}" );
}

add_action( 'delete_term', 'moowoodle_delete_term_meta' );

// Get all woocommerce pages
if ( ! function_exists( 'moowoodle_wp_pages' ) ) {
  
  function moowoodle_wp_pages() {
    $args = array( 'posts_per_page' => -1, 'post_type' => 'page', 'orderby' => 'title', 'order' => 'ASC' );
        $wp_posts = get_posts( $args );
        foreach ( $wp_posts as $post ) : setup_postdata( $post );    
        $page_array[ $post->ID ] = $post->post_title;       
        endforeach; 
        wp_reset_postdata();
    return $page_array;
  }
}

if ( ! function_exists( 'moowoodle_wp_roles' ) ) {
  function moowoodle_wp_roles() {
    global $wp_roles;
     $roles = $wp_roles->roles;     
     foreach ( $wp_roles->roles as $key=>$value ){ 
       $role_array[ $key ] = $value[ 'name' ];
     }     
     return $role_array;
  }
}

// Old version to new migration
if ( ! function_exists( 'moowoodle_option_migration_2_to_3' ) ) {
  function moowoodle_option_migration_2_to_3() {

    global $MooWoodle, $wpdb;
    if( !get_option( 'moowoodle_migration_completed' ) ) :

      $old_setting = get_option( 'dc_dc_woodle_general_settings_name' );

      $conn_settings = $MooWoodle->options_general_settings;
      $conn_settings[ 'moodle_url' ] = $old_setting[ 'access_url' ];
      $conn_settings[ 'moodle_access_token' ] = $old_setting[ 'ws_token' ];
      update_option( 'moowoodle_general_settings', $conn_settings );

      $display_settings = $MooWoodle->options_display_settings;
      if ( isset( $old_setting[ 'wc_product_dates_display' ] ) && $old_setting[ 'wc_product_dates_display' ] == "yes" ) {
        $display_settings[ 'start_end_date' ] = "Enable";
        update_option( 'moowoodle_display_settings', $display_settings );
      }

      $sync_settings = $MooWoodle->options_synchronize_settings;
      if ( isset( $old_setting[ 'create_wc_product' ] ) && $old_setting[ 'create_wc_product' ] == "yes" ) {
        $sync_settings[ 'sync_products' ] = "Enable";
        update_option( 'moowoodle_synchronize_settings', $sync_settings );
      }            
      
      delete_option( 'dc_dc_woodle_general_settings_name' );
      delete_post_meta_by_key( '_cohert_id' );
      delete_post_meta_by_key( '_group_id' );

      $wpdb->update( $wpdb->postmeta, array( 'meta_key' => 'linked_course_id' ), array( 'meta_key' => 'product_course_id' ) );
      $wpdb->update( $wpdb->postmeta, array( 'meta_key' => 'moodle_course_id' ), array( 'meta_key' => '_course_id' ) );
      
      delete_option( 'woodle_version' );
      delete_option( 'woodle_db_version' );

      $table_rename = "ALTER TABLE {$wpdb->woodle_termmeta} RENAME TO 'wp_moowoodle_termmeta';";
      $result = $wpdb->get_results( $table_rename );

      $column_rename = "ALTER TABLE {$wpdb->moowoodle_termmeta} CHANGE COLUMN woodle_term_id moowoodle_term_id bigint(20);";
      $result = $wpdb->get_results( $column_rename );

      update_option( 'moowoodle_migration_completed', 'migrated' );
    endif;
  }    
}