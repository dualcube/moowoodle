<?php

class MooWoodle_Sync {
	
	function __construct() {		
		add_action( 'admin_init', array( &$this, 'sync' ) );
	}

	/**
	 * Initiate sync process.
	 *
	 * @access public
	 * @return void
	*/
	public function sync() {
		global  $MooWoodle;
		if ( ! isset( $_POST[ 'syncnow' ] ) ) {
			return;
		}

		$sync_settings = $MooWoodle->options_synchronize_settings;
		if ( isset( $sync_settings[ 'sync_courses_category' ] ) && $sync_settings[ 'sync_courses_category' ] == "Enable" ) {
			$this->sync_categories();
		}
		if ( isset( $sync_settings[ 'sync_courses' ] ) && $sync_settings[ 'sync_courses' ] == "Enable" ) {
			$this->sync_courses();
		}
	}

	/**
	 * Sync course categories from moodle.
	 *
	 * @access private
	 * @return void
	*/
	private function sync_categories() {
		global $MooWoodle;

		$categories = moowoodle_moodle_core_function_callback( 'get_categories' );

		if ( ! $MooWoodle->ws_has_error ) {
			$this->update_categories( $categories, 'course_cat', 'moowoodle_term' );
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

		if ( empty( $taxonomy ) || empty( $meta_key ) || ! taxonomy_exists( $taxonomy ) ) {
			return;
		}
		
		$category_ids = array();
		if ( ! empty( $categories ) ) {
			foreach ( $categories as $category ) {
				$term_id = moowoodle_get_term_by_moodle_id( $category[ 'id' ], $taxonomy, $meta_key );
				
				if ( ! $term_id ) {
					$name = $category[ 'name' ];
					$description = $category[ 'description' ];
					$term = wp_insert_term( $name, $taxonomy, array( 'description' =>  $description, 'slug' => "{$name} {$category[ 'id' ]}" ) );

					if ( ! is_wp_error( $term ) ) {
						add_term_meta ( $term[ 'term_id' ], '_category_id', $category[ 'id' ], false );
						add_term_meta ( $term[ 'term_id' ], '_parent', $category[ 'parent' ], false );
						add_term_meta ( $term[ 'term_id' ], '_category_path', $category[ 'path' ], false );
					}
				} else {
					$term = wp_update_term( $term_id, $taxonomy, array( 'name' => $category[ 'name' ], 'slug' => "{$category[ 'name' ]} {$category[ 'id' ]}", 'description' => $category[ 'description' ] ) );

					if ( ! is_wp_error( $term ) ) {
						update_term_meta( $term[ 'term_id' ], '_parent', $category[ 'parent' ], '' );
						update_term_meta( $term[ 'term_id' ], '_category_path', $category[ 'path' ], false );
					}
				}
				$category_ids[] = $category[ 'id' ];
			}			
		}

		$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
		if ( $terms ) {
			foreach ( $terms as $term ) {
				$category_id = get_term_meta( $term->term_id, '_category_id', true );
				if ( in_array( $category_id, $category_ids ) ) {
					$parent = get_term_meta( $term->term_id, '_parent', true );
					$parent_term_id = moowoodle_get_term_by_moodle_id( $parent, $taxonomy, $meta_key );
					wp_update_term( $term->term_id, $taxonomy, array( 'parent' => $parent_term_id ) );
				} else if ( ! empty( $category_id ) ) {
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
		global $MooWoodle;

		$sync_settings = $MooWoodle->options_synchronize_settings;
		$courses = moowoodle_moodle_core_function_callback( 'get_courses' );

		$this->update_posts( $courses, 'course', 'course_cat', 'moowoodle_term' );
		if ( isset( $sync_settings[ 'sync_products' ] ) && $sync_settings[ 'sync_products' ] == "Enable" ) {
			$this->update_posts( $courses, 'product', 'product_cat', 'woocommerce_term' );
		}
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

		if ( empty( $post_type ) || ! post_type_exists( $post_type ) || empty( $taxonomy ) || ! taxonomy_exists( $taxonomy ) || empty( $meta_key ) ) {
			return;
		}
		$course_ids = array();

		if ( ! empty( $courses ) ){
			
			foreach ( $courses as $course ) {
				if ( $course[ 'format' ] == 'site' ) {
					continue;
				}

				$post_id = moowoodle_get_post_by_moodle_id( $course[ 'id' ], $post_type );

				$post_status = 'publish';
				$args = array( 'post_title'   => $course[ 'fullname' ],
							   'post_name'	  => $course[ 'shortname' ],
							   'post_content' => $course[ 'summary' ],
							   'post_status'  => $post_status,
							   'post_type'    => $post_type							
						    );

				if ( $post_id > 0 ) {
					$args[ 'ID' ] = $post_id;
					$new_post_id = wp_update_post( $args );				
				} else {
					$new_post_id = wp_insert_post( $args );
				}

				if ( $new_post_id > 0 ) {					
					if ( $post_type == 'product' ) {
						update_post_meta( $new_post_id, 'linked_course_id', (int) $course[ 'id' ] );
						update_post_meta( $new_post_id, '_sku', 'course-' . (int) $course[ 'id' ]);
						update_post_meta( $new_post_id, '_virtual', 'yes' );
						update_post_meta( $new_post_id, '_sold_individually', 'yes' );
						update_post_meta( $new_post_id, '_course_startdate', $course[ 'startdate' ] );
						update_post_meta( $new_post_id, '_course_enddate', $course[ 'enddate' ] );
					} else {
						$shortname = $course[ 'shortname' ];
						update_post_meta( $new_post_id, '_course_short_name', sanitize_text_field( $shortname ) );
						update_post_meta( $new_post_id, '_course_idnumber', sanitize_text_field( $course[ 'idnumber' ] ) );
					}
					update_post_meta( $new_post_id, 'moodle_course_id', (int) $course[ 'id' ] );	
					update_post_meta( $new_post_id, '_category_id', (int) $course[ 'categoryid' ] );
					update_post_meta( $new_post_id, '_visibility', $visibility = ( $course[ 'visible' ] ) ? 'visible' : 'hidden' );					
				}				
				$course_ids[ $course[ 'id' ] ] = $course[ 'categoryid' ];				
			}
		}

		$posts = get_posts( array( 'post_type' => $post_type, 'numberposts' => -1, 'post_status' => 'publish' ) );
		if ( $posts ) {
			foreach ( $posts as $post ) {
				$course_id = get_post_meta( $post->ID, 'moodle_course_id', true );
				if ( array_key_exists( $course_id, $course_ids ) ) {
					$term_id = moowoodle_get_term_by_moodle_id( $course_ids[ $course_id ], $taxonomy, $meta_key );
					wp_set_post_terms( $post->ID, $term_id, $taxonomy );					
				} else if ( ! empty( $course_id ) ) {
					wp_delete_post( $post->ID, false );					
				}
			}
		}		
	}
}
