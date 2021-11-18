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
 * Call to moodle core functions.
 *
 * @param string $function_name (default: null)
 * @param string $request_param (default: null)
 * @return mixed
 */
if ( ! function_exists( 'moowoodle_moodle_core_function_callback' ) ) {
  function moowoodle_moodle_core_function_callback( $key = '', $request_param = array() ) {
    global $MooWoodle;
    
    $response = null;
    $function_name = "";
    $moodle_core_functions = array( 'get_categories' => 'core_course_get_categories',
                                    'get_courses'  => 'core_course_get_courses',
                                    'get_moodle_users'    => 'core_user_get_users',
                                    'create_users'   => 'core_user_create_users',
                                    'update_users'   => 'core_user_update_users',
                                    'enrol_users'  => 'enrol_manual_enrol_users'
                                  );
    if ( array_key_exists( $key, $moodle_core_functions ) ) {
      $function_name = $moodle_core_functions[ $key ];
    }

    $conn_settings = $MooWoodle->options_general_settings;
    $url = $conn_settings[ 'moodle_url' ];
    $token = $conn_settings[ 'moodle_access_token' ];
    $request_url = $url . '/webservice/rest/server.php?wstoken=' . $token . '&wsfunction=' . $function_name . '&moodlewsrestformat=json';

    if ( $function_name == 'core_user_get_users' ) {
      $request_url = $request_url . '&criteria[0][key]=email&criteria[0][value]=%%';
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
      if ( get_term_meta( $term->term_id, '_category_id', true ) == $category_id ) {
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

      $terms = retrieve_term( 'course_cat' );
      if ( $terms ) {
        foreach ( $terms as $term ) {
          add_meta_value( $term->term_id );
        }
      }
      
      update_option( 'moowoodle_migration_completed', 'migrated' );
    endif;
  }    
}

if( ! function_exists( 'retrieve_term' ) ) {
  function retrieve_term( $taxonomy ) {
    global $wpdb;
     
    $query = "SELECT terms.term_id, terms.name, terms.slug, term_taxonomy.term_taxonomy_id, term_taxonomy.parent, term_taxonomy.description
            FROM {$wpdb->terms} AS terms
            INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy
            ON terms.term_id = term_taxonomy.term_id
            WHERE term_taxonomy.taxonomy = '$taxonomy'";
    $terms = $wpdb->get_results( $query );
    $terms = ( is_wp_error( $terms ) || empty( $terms ) ) ? false : $terms;
    return $terms; 
  }
}

if( ! function_exists( 'add_meta_value' ) ) {
  function add_meta_value( $term_id ) {
    global $wpdb;
    $query_id = "SELECT meta_value FROM {$wpdb->prefix}woodle_termmeta WHERE woodle_term_id = $term_id and meta_key = '_category_id' ";
    $query_parent = "SELECT meta_value FROM {$wpdb->prefix}woodle_termmeta WHERE woodle_term_id = $term_id and meta_key = '_parent' ";
    $query_path = "SELECT meta_value FROM {$wpdb->prefix}woodle_termmeta WHERE woodle_term_id = $term_id and meta_key = '_category_path' ";
    
    $category_id = $wpdb->get_row( $query_id );
    $parent = $wpdb->get_row( $query_parent );
    $category_path = $wpdb->get_row( $query_path );

    $insert_id = add_term_meta ( $term_id, '_category_id', $category_id->meta_value, false );
    $insert_parent = add_term_meta ( $term_id, '_parent', $parent->meta_value, false );
    $insert_path = add_term_meta ( $term_id, '_category_path', $category_path->meta_value, false );

  }
}

if( ! function_exists( 'get_moowoodle_course_url' ) ) {
  function get_moowoodle_course_url( $linked_course_id, $course_name ) {
    global $MooWoodle;
    $course = $linked_course_id;
    $class = 'moowoodle';
    $target = '_blank';
    $authtext = '';
    $activity = 0;
    $content = $course_name;
    $conn_settings = $MooWoodle->options_general_settings;
    $redirect_uri = $conn_settings[ 'moodle_url' ] . "/course/view.php?id=" . $course;
    $url = '<a target="' . esc_attr( $target ) . '" class="' . esc_attr( $class ) . '" href="' . $redirect_uri . '">' . $content . '</a>';
    return $url;
  }
}

if( ! function_exists( 'get_account_menu_items' ) ) {
  function get_account_menu_items() {
    $menu_items = wc_get_account_menu_items();
    $menu_array = array();
    $i = 0;
    foreach ( $menu_items as $key => $value ) {
      $menu_array[$i] = $value;
      $i++;
    }
    return $menu_array;
  }
}

if( ! function_exists( 'moodle_customer_created_orders_count' ) ) {
  function moodle_customer_created_orders_count($customer_id) {
    $count = 0;
    $customer_orders = get_posts( array(
      'numberposts' => -1,
      'meta_key' => '_customer_user',
      'orderby' => 'date',
      'order' => 'DESC',
      'meta_value' => $customer_id,
      'post_type' => 'shop_order',
      'post_status' => 'wc-completed'
    ) );
    if ( !empty($customer_orders) ) {
      foreach ( $customer_orders as $customer_order ) {
        $course_exist_in_order_product = moodle_course_exist_in_order_items($customer_order->ID);
        if ($course_exist_in_order_product) $count++;
      }
    }
    return $count;
  }
}

if( ! function_exists( 'moodle_course_exist_in_order_items' ) ) {
  function moodle_course_exist_in_order_items($order_id) {
    $order = wc_get_order( $order_id );
    if ($order) {
      foreach ( $order->get_items() as $enrolment ) {
        $linked_course_id = get_post_meta( $enrolment->get_product_id(), 'linked_course_id', true ) ? get_post_meta( $enrolment->get_product_id(), 'linked_course_id', true ) : '';
        if ($linked_course_id) {
         return true; 
       } else {}
      }
    }
    return false;
  }
}