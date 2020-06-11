<?php

class DC_Woodle_Sync {
	
	public $has_error;
	
	public $error_msg;
	
	public function __construct() {
		add_action( 'wp_loaded', array( &$this, 'sync' ) );
	}
	
	/**
	 * Initiate sync process.
	 *
	 * @access public
	 * @return void
	 */
	public function sync() {
		global $DC_Woodle;
		
		$user_id = get_current_user_id();

		if ( $user_id <= 0 ) {
			return;
		}
		
		if ( 'POST' !== strtoupper( $_SERVER[ 'REQUEST_METHOD' ] ) ) {
			return;
		}

		if ( empty( $_POST['dc_dc_woodle_sync_settings_name']['action'] ) || 'sync_courses_and_categories' !== $_POST['dc_dc_woodle_sync_settings_name']['action'] ) {
			return;
		}
		
		if( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'dc-sync_courses_and_categories' ) ) {
			return;
		}
		
		if ( empty( $_POST['dc_dc_woodle_sync_settings_name']['sync_now'] ) || $_POST['dc_dc_woodle_sync_settings_name']['sync_now'] == 'no' ) {
			return;
		}
		
		$access_url = woodle_get_settings( 'access_url', 'dc_woodle_general' );
		
		if( empty( $access_url ) ) {
			woodle_add_notice( '<strong>Synchronization can not be done.</strong> Moodle access url is not set. 
													<a href="'. admin_url( 'admin.php?page=dc-woodle-setting-admin' ) .'"> Set it now &nbsp;&raquo;</a>' );
			return;
		}
		
		$ws_token = woodle_get_settings( 'ws_token', 'dc_woodle_general' );
		
		if( empty( $ws_token ) ) {
			woodle_add_notice( '<strong>Synchronization can not be done.</strong> Moodle web service token is not set.
													<a href="'. admin_url( 'admin.php?page=dc-woodle-setting-admin' ) .'"> Set it now &nbsp;&raquo;</a>' );
			return;
		}

	if($_POST['dc_dc_woodle_sync_settings_name']['sync_category'] == 'yes')
    		$this->sync_categories();
    
    if( ! $DC_Woodle->ws_has_error ) {
    	if($_POST['dc_dc_woodle_sync_settings_name']['sync_course'] == 'yes')
    		$this->sync_courses();
    }
    
    if( ! $DC_Woodle->ws_has_error ) {
			woodle_add_notice( $DC_Woodle->ws_error_msg, 'updated' );
		} else {
			woodle_add_notice( $DC_Woodle->ws_error_msg, 'error' );
		}
	}
	
	/**
	 * Sync course categories from moodle.
	 *
	 * @access private
	 * @return void
	 */
	private function sync_categories() {
		global $DC_Woodle;
		
		$categories = woodle_moodle_core_function_callback( $DC_Woodle->moodle_core_functions['get_categories'] );
		
		if( ! $DC_Woodle->ws_has_error ) {
			$this->update_categories( $categories, 'course_cat', 'woodle_term' );
			$this->update_categories( $categories, 'product_cat', 'woocommerce_term' );
		}
	}
	
	/**
	 * Update moodle course categories in Wordpress site.
	 *
	 * @access private
	 * @param array $categories
	 * @param string $taxonomy
	 * @param string $meta_key
	 * @return void
	 */
	private function update_categories( $categories, $taxonomy, $meta_key ) {
		if( empty( $taxonomy ) || empty( $meta_key ) || ! taxonomy_exists( $taxonomy ) ) {
			return;
		}
		
		$category_ids = array();
		if( ! empty( $categories ) ) {
			foreach( $categories as $category ) {
				$term_id = woodle_get_term_by_moodle_id( $category['id'], $taxonomy, $meta_key );
				if( ! $term_id ) {
					$name = $category['name'];
					$description = $category['description'];
					$term = wp_insert_term( $name, $taxonomy,
																	array( 'description' =>  $description, 'slug' => "{$name} {$category['id']}" ) 
																);
					if( ! is_wp_error( $term ) ) {
						apply_filters( "woodle_add_{$meta_key}_meta", $term['term_id'], '_category_id', $category['id'], false );
						apply_filters( "woodle_add_{$meta_key}_meta", $term['term_id'], '_parent', $category['parent'], false );
						apply_filters( "woodle_add_{$meta_key}_meta", $term['term_id'], '_category_path', $category['path'], false );
					}
				} else {
					$term = wp_update_term( $term_id, $taxonomy,
																	array( 'name' => $category['name'], 'slug' => "{$category['name']} {$category['id']}", 'description' => $category['description'] )
																);
					if( ! is_wp_error( $term ) ) {
						apply_filters( "woodle_update_{$meta_key}_meta", $term['term_id'], '_parent', $category['parent'], '' );
						apply_filters( "woodle_update_{$meta_key}_meta", $term['term_id'], '_category_path', $category['path'], false );
					}
				}
				$category_ids[] = $category['id'];
			}
		}
		
		$terms = woodle_get_terms( $taxonomy );
		if( $terms ) {
			foreach( $terms as $term ) {
				$category_id = apply_filters( "woodle_get_{$meta_key}_meta", $term->term_id, '_category_id', true );
				if( in_array( $category_id, $category_ids ) ) {
					$parent = apply_filters( "woodle_get_{$meta_key}_meta", $term->term_id, '_parent', true );
					$parent_term_id = woodle_get_term_by_moodle_id( $parent, $taxonomy, $meta_key );
					wp_update_term( $term->term_id, $taxonomy, array( 'parent' => $parent_term_id ) );
				} else if( ! empty( $category_id ) ) {
					wp_delete_term( $term->term_id, $taxonomy );
				}
			}
		}
	}
	
	/**
	 * Sync courses from moodle.
	 *
	 * @access private
	 * @return void
	 */
	private function sync_courses() {
		global $DC_Woodle;
		
		$courses = woodle_moodle_core_function_callback( $DC_Woodle->moodle_core_functions['get_courses'] );
		
		$this->update_posts( $courses, 'course', 'course_cat', 'woodle_term' );
		$this->update_posts( $courses, 'product', 'product_cat', 'woocommerce_term' );
	}
	
	/**
	 * Update moodle courses in Wordpress site.
	 *
	 * @access private
	 * @param array $courses
	 * @param string $post_type (default: null)
	 * @param string $taxonomy (default: null)
	 * @param string $meta_key (default: null)
	 * @return void
	 */
	private function update_posts( $courses, $post_type = '', $taxonomy = '', $meta_key = '' ) { 
		if( empty( $post_type ) || ! post_type_exists( $post_type ) || empty( $taxonomy ) || ! taxonomy_exists( $taxonomy ) || empty( $meta_key ) ) {
			return;
		}
		
		$course_ids = array();
		$create_wc_product = ( $post_type == 'product' ) ? woodle_get_settings( 'create_wc_product', 'dc_woodle_general' ) : '';
		
		if( ! empty( $courses ) ) {
			foreach( $courses as $course ) {
				if( $course['format'] == 'site' ) {
					continue;
				}
				
				$post_id = woodle_get_post_by_moodle_id( $course['id'], $post_type );	
				$post_status = 'publish';
				
				if( $post_type == 'product' && $post_id ) {
					$product = get_post( $post_id );
					$post_status = $product->post_status;
				}
				
				$args = array( 'post_title' => $course['fullname'],
											 'post_name'      => $course['shortname'],
											 'post_content'   => $course['summary'],
											 'post_status'    => $post_status
										 );
				$visibility = '';

				if( $post_id ) {
					$args['ID'] = $post_id;					
					$post_id = wp_update_post( $args );
					
					if( $post_id ) {
						if( $post_type != 'product' ) {
							$shortname = $course['shortname'];
							update_post_meta( $post_id, '_course_short_name', $shortname );
							update_post_meta( $post_id, '_course_idnumber', $course['idnumber'] );
						}
						
						update_post_meta( $post_id, '_category_id', (int) $course['categoryid'] );
						update_post_meta( $post_id, '_visibility', $visibility = ( $course['visible'] ) ? 'visible' : 'hidden' );
						
						if( $post_type == 'product' ) {
							update_post_meta( $post_id, '_virtual', 'yes' );
							update_post_meta( $post_id, '_sold_individually', 'yes' );
							update_post_meta( $post_id, '_course_startdate', $course['startdate'] );
							update_post_meta( $post_id, '_course_enddate', $course['enddate'] );
						}
					}
				}	else if( $post_type != 'product' || $create_wc_product == 'yes' ) {
					$args['post_type'] = $post_type;
					$post_id = wp_insert_post( $args );
					
					if( $post_id ) {
						if( $post_type != 'product' ) {
							add_post_meta( $post_id, '_course_short_name', $course['shortname'] );
							add_post_meta( $post_id, '_course_idnumber', $course['idnumber'] );
						}
						
						add_post_meta( $post_id, '_course_id', (int) $course['id'] );
						add_post_meta( $post_id, '_category_id', (int) $course['categoryid'] );
						add_post_meta( $post_id, '_visibility', $visibility = ( $course['visible'] ) ? 'visible' : 'hidden' );
						
						if( $post_type == 'product' ) {
							add_post_meta( $post_id, '_sku', 'course-' . $course['id']);
							add_post_meta( $post_id, '_virtual', 'yes' );
							add_post_meta( $post_id, '_sold_individually', 'yes' );
						}
					}
				}

				$course_ids[$course['id']] = $course['categoryid'];
			}
		}
		
		$posts = woodle_get_posts( array( 'post_type' => $post_type ) );
		if( $posts ) {
			foreach( $posts as $post ) {
				$course_id = get_post_meta( $post->ID, '_course_id', true );
				if( array_key_exists( $course_id, $course_ids ) ) {
					$term_id = woodle_get_term_by_moodle_id( $course_ids[$course_id], $taxonomy, $meta_key );
					wp_set_post_terms( $post->ID, $term_id, $taxonomy );
				} else if( ! empty( $course_id ) ) {
					wp_delete_post( $post->ID, false );
				}
			}
		}
	}
}