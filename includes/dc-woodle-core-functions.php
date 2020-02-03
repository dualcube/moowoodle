<?php
/**
 * Get a settings field value.
 *
 * @param string $name (default: null)
 * @param string $tab (default: null)
 * @return mixed
 */
if(!function_exists('woodle_get_settings')) {
  function woodle_get_settings($name = '', $tab = '') {
    if(empty($tab) && empty($name)) return '';
    if(empty($tab)) return get_option($name);
    if(empty($name)) return get_option("dc_{$tab}_settings_name");
    $settings = get_option("dc_{$tab}_settings_name");
    if(!isset($settings[$name])) return '';
    return $settings[$name];
  }
}

/**
 * Add woocommerce inactive notice.
 *
 * @return void
 */
if( ! function_exists( 'woodle_wc_inactive_notice' ) ) {
  function woodle_wc_inactive_notice() {
  	global $DC_Woodle;
  	
  	woodle_add_notice( __( '<strong>Woocommerce is inactive.</strong> The <a target="_blank" 
  													href="https://wordpress.org/plugins/woocommerce/">Woocommerce plugin</a> 
  													must be active for the MooWoodle plugin to work. Please <a href="' . 
  													admin_url( 'plugins.php' ) . '">install & activate Woocommerce &nbsp;&raquo;</a>', $DC_Woodle->text_domain ) );
  }
}

/**
 * Print notice.
 *
 * @return void
 */
if( ! function_exists( 'woodle_print_notice' ) ) {
	function woodle_print_notice() {
		global $DC_Woodle;
		
		if( ! isset( $_SESSION['woodle_notice'] ) ) {
			return;
		}
    ?>
    <div id="message" class="<?php echo $_SESSION['woodle_notice']['type']; ?>">
      <p><?php printf( $_SESSION['woodle_notice']['message'] ); ?></p>
    </div>
		<?php
		unset( $_SESSION['woodle_notice'] );
	}
	
	add_action( 'admin_notices', 'woodle_print_notice' );
}

/**
 * Add notice.
 * 
 * @param string $message (default: null)
 * @param string $type (default: string)
 * @return void
 */
if( ! function_exists('woodle_add_notice') ) {
	function woodle_add_notice( $message = '', $type = 'error' ) {
		global $DC_Woodle;
		
		$_SESSION['woodle_notice'] = array( 'message' => $message, 'type' => $type );
	}
}

/**
 * Required moodle core functions.
 *
 * @param string $key (default: null)
 * @return array/string
 */
if( ! function_exists( 'woodle_get_moodle_core_functions' ) ) {
	function woodle_get_moodle_core_functions( $key = '' ) {
		$moodle_core_functions = array( 'get_categories' => 'core_course_get_categories',
										'get_courses'	 => 'core_course_get_courses',
										'get_users' 	 => 'core_user_get_users',
										'create_users'	 => 'core_user_create_users',
										'update_users'	 => 'core_user_update_users',
										'enrol_users'	 => 'enrol_manual_enrol_users'
									);
		
		if( empty( $key ) ) {
			return $moodle_core_functions;
		} else if( array_key_exists( $key, $moodle_core_functions ) ) {
			return $moodle_core_functions[$key];
		}
		
		return null;
	}
}

/**
 * Sync error messages.
 *
 * @param string $key (default: null)
 * @return array/string
 */
if( ! function_exists( 'woodle_get_sync_error_message' ) ) {
	function woodle_get_sync_error_message( $errorcode = '' ) {
		$error_messages = array( 'successful'				=> 'Synchronization is complete.',
														 'accessexception' 	=> '<strong>Error!</strong> Web service in your <a href="' . 
																									  woodle_get_settings( 'access_url', 'dc_woodle_general' ) . 
																									  '/admin/settings.php?section=externalservices" target="_blank">moodle site</a> 
																									  is disabled or not configured properly.',
														 'invalidtoken' 	 	=> '<strong>Error!</strong> Invalid webservice token. <a href="' . 
																									  woodle_get_settings( 'access_url', 'dc_woodle_general' ) . 
																									  '/admin/settings.php?section=webservicetokens" target="_blank">Click here</a> for tokens.',
														 'mdlnotconfigured'	=> '<strong>Error!</strong> Your <a href="' . 
																									  woodle_get_settings( 'access_url', 'dc_woodle_general' ) . 
																									  '" target="_blank">moodle site</a> might not configured properly.',
														 'invalidparameter' => ''
													 );
		
		if( empty( $errorcode ) || ! array_key_exists( $errorcode, $error_messages ) ) {
			return null;
		}
		
		return $error_messages[$errorcode];
	}
}

/**
 * Call to moodle core functions.
 *
 * @param string $function_name (default: null)
 * @param string $request_param (default: null)
 * @return mixed
 */
if( ! function_exists( 'woodle_moodle_core_function_callback' ) ) {
	function woodle_moodle_core_function_callback( $function_name = '', $request_param = array() ) {
		global $DC_Woodle;
		
		$response = null;
		$url = woodle_get_settings( 'access_url', 'dc_woodle_general' );
		$token = woodle_get_settings( 'ws_token', 'dc_woodle_general' );
		$request_url = $url . '/webservice/rest/server.php?wstoken=' . $token . '&wsfunction=' . $function_name . '&moodlewsrestformat=json';
		
    if( ! empty( $url )  && ! empty( $token ) && $function_name != '' ) {
    	$request_query = http_build_query( $request_param );
      $response = wp_remote_post( $request_url, array( 'body' => $request_query ) );
    } 
    
    if( ! is_wp_error( $response ) && $response != null && $response['response']['code'] == 200 ) {
      if( is_string( $response['body'] ) ) {
				$response_arr = json_decode( $response['body'], true );
				
				if( json_last_error() === JSON_ERROR_NONE ) {
					if( is_null( $response_arr ) || ! array_key_exists( 'exception', $response_arr ) ) {
						$DC_Woodle->ws_has_error = false;
						$DC_Woodle->ws_error_msg = woodle_get_sync_error_message( 'successful' );
						
						return $response_arr;
					} else {
						$DC_Woodle->ws_has_error = true;
						$DC_Woodle->ws_error_msg = woodle_get_sync_error_message( $response_arr['errorcode'] );
					}
				} else {
					$DC_Woodle->ws_has_error = true;
					$DC_Woodle->ws_error_msg = woodle_get_sync_error_message( 'mdlnotconfigured' );
				}
			} else {
				$DC_Woodle->ws_has_error = true;
				$DC_Woodle->ws_error_msg = woodle_get_sync_error_message( 'mdlnotconfigured' );
			}
    } else {
    	$DC_Woodle->ws_has_error = true;
    	$DC_Woodle->ws_error_msg = woodle_get_sync_error_message( 'mdlnotconfigured' );
    }
		
    return null;
	}
}

/**
 * Woodle Term Meta API - set table name
 *
 * @return void
 */
function woodle_taxonomy_metadata_wpdbfix() {
	global $wpdb;
	
	$termmeta_name = 'woodle_termmeta';
	$wpdb->woodle_termmeta = $wpdb->prefix . $termmeta_name;
	$wpdb->tables[] = 'woodle_termmeta';
}

add_action( 'init', 'woodle_taxonomy_metadata_wpdbfix', 0 );
add_action( 'switch_blog', 'woodle_taxonomy_metadata_wpdbfix', 0 );

/**
 * Woodle Term Meta API - Add term meta
 *
 * @param mixed $term_id
 * @param mixed $meta_key
 * @param mixed $meta_value
 * @param bool $unique (default: false)
 * @return bool
 */
function add_woodle_term_meta( $term_id, $meta_key, $meta_value, $unique = false ) {
	return add_metadata( 'woodle_term', $term_id, $meta_key, $meta_value, $unique );
}

add_filter( 'woodle_add_woodle_term_meta', 'add_woodle_term_meta', 10, 4 );
add_filter( 'woodle_add_woocommerce_term_meta', 'add_woocommerce_term_meta', 10, 4 );

/**
 * Woodle Term Meta API - Update term meta
 *
 * @param mixed $term_id
 * @param string $meta_key
 * @param mixed $meta_value
 * @param string $prev_value (default: '')
 * @return bool
 */
function update_woodle_term_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {
	return update_metadata( 'woodle_term', $term_id, $meta_key, $meta_value, $prev_value );
}

add_filter( 'woodle_update_woodle_term_meta', 'update_woodle_term_meta', 10, 4 );
add_filter( 'woodle_update_woocommerce_term_meta', 'update_woocommerce_term_meta', 10, 4 );

/**
 * Woodle Term Meta API - Delete term meta
 *
 * @param mixed $term_id
 * @param mixed $meta_key
 * @param string $meta_value (default: '')
 * @param bool $delete_all (default: false)
 * @return bool
 */
function delete_woodle_term_meta( $term_id, $meta_key, $meta_value = '', $delete_all = false ) {
	return delete_metadata( 'woodle_term', $term_id, $meta_key, $meta_value, $delete_all );
}

add_filter( 'woodle_delete_woodle_term_meta', 'delete_woodle_term_meta', 10, 4 );
add_filter( 'woodle_delete_woocommerce_term_meta', 'delete_woocommerce_term_meta', 10, 4 );

/**
 * Woodle Term Meta API - Get term meta
 *
 * @param mixed $term_id
 * @param string $key
 * @param bool $single (default: true)
 * @return mixed
 */
function get_woodle_term_meta( $term_id, $key, $single = true ) {
	return get_metadata( 'woodle_term', $term_id, $key, $single );
}

add_filter( 'woodle_get_woodle_term_meta', 'get_woodle_term_meta', 10, 3 );
add_filter( 'woodle_get_woocommerce_term_meta', 'get_woocommerce_term_meta', 10, 3 );

/**
 * When a term is deleted, delete its meta.
 *
 * @param mixed $term_id
 * @return void
 */
function woodle_delete_term_meta( $term_id ) {
	global $wpdb;
	
	$term_id = (int) $term_id;
	if ( ! $term_id ) {
		return;
	}
	$wpdb->query( "DELETE FROM {$wpdb->woodle_termmeta} WHERE `woodle_term_id` = {$term_id}" );
}

add_action( 'delete_term', 'woodle_delete_term_meta' );

/**
 * Returns term id by moodle category id
 *
 * @param int $category_id
 * @param string $taxonomy (default: null)
 * @param string $meta_key (default: null)
 * @return int
 */
function woodle_get_term_by_moodle_id( $category_id, $taxonomy = '', $meta_key = '' ) {
	if( empty( $category_id ) || ! is_numeric( $category_id ) || empty( $taxonomy ) || ! taxonomy_exists( $taxonomy ) || empty( $meta_key ) ) {
		return 0;
	}
	$terms = woodle_get_terms( $taxonomy );
	if( $terms ) {
		foreach( $terms as $term ) {
			if( apply_filters( "woodle_get_{$meta_key}_meta", $term->term_id, '_category_id', true ) == $category_id ) {
				return $term->term_id;
			}
		}
	}
	return 0;
}

/**
 * Returns terms
 *
 * @param string $taxonomy (default: null)
 * @return array
 */
function woodle_get_terms( $taxonomy = '' ) {
	global $wpdb;
	
	if( empty( $taxonomy ) || ! taxonomy_exists( $taxonomy ) ) {
		return false;
	}
	
	$query = "SELECT terms.term_id, terms.name, terms.slug, term_taxonomy.term_taxonomy_id, term_taxonomy.parent, term_taxonomy.description
						FROM {$wpdb->terms} AS terms
						INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy
						ON terms.term_id = term_taxonomy.term_id
						WHERE term_taxonomy.taxonomy = '$taxonomy'";
	$terms = $wpdb->get_results( $query );
	$terms = ( is_wp_error( $terms ) || empty( $terms ) ) ? false : $terms;
	return $terms;
}

/**
 * Returns post id by moodle category id.
 *
 * @param int $course_id
 * @param string $post_type (default: null)
 * @return int
 */
function woodle_get_post_by_moodle_id( $course_id, $post_type = '' ) {
	if( empty( $course_id ) || ! is_numeric( $course_id ) || empty( $post_type ) || ! post_type_exists( $post_type ) ) {
		return 0;
	}
	$posts = woodle_get_posts( array( 'post_type' => $post_type ) );
	if( $posts ) {
		foreach( $posts as $post ) {
			if( get_post_meta( $post->ID, '_course_id', true ) == $course_id ) {
				return $post->ID;
			}
		}
	}
	return 0;
}

/**
 * Returns posts
 *
 * @param array $args (default: array)
 * @return array
 */ 
function woodle_get_posts( $args = array() ) {
	global $wpdb;
	
	$query = "SELECT ID, post_content, post_title, post_excerpt, post_status, post_name, post_parent, post_type
						FROM {$wpdb->posts}";
	if( ! empty( $args ) ) {
		$index = 1;
		foreach( $args as $col => $arg ) {
			if( $index != 1 ) {
				$query .= " AND `{$col}` = '{$arg}'";
			} else {
				$query .= " WHERE `{$col}` = '{$arg}'";
			}
			$index += 1;
		}
	}
	
	$posts = $wpdb->get_results( $query );
	$posts = ( is_wp_error( $posts ) || empty( $posts ) ) ? false : $posts;
	return $posts;
}

