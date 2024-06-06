<?php

namespace MooWoodle\Core;

class Course {
	public function __construct() {
		// Add Link Moodle Course in WooCommerce edit product tab.
		add_filter( 'woocommerce_product_data_tabs', [ &$this, 'moowoodle_linked_course_tab' ], 99, 1 );
		add_action( 'woocommerce_product_data_panels', [ &$this, 'moowoodle_linked_course_panals' ] );
		add_action( 'woocommerce_process_product_meta', [ &$this, 'save_product_meta_data' ] );

		add_action( 'init', [ &$this, 'register_course_taxonomy' ] );
		add_action( 'init', [ &$this, 'register_course_post_type' ] );
	}

	/**
	 * Register 'course' post tipe.
	 * @return void
	 */
	public function register_course_post_type() {
		$args = [
			'labels' => [
				'name' 			 	 => sprintf(_x('%s', 'post type general name', 'moowoodle'), __('Courses', 'moowoodle')),
				'singular_name' 	 => sprintf(_x('%s', 'post type singular name', 'moowoodle'), __('Course', 'moowoodle')),
				'add_new'			 => sprintf(_x('Add New %s', 'course', 'moowoodle'), __('Course', 'moowoodle')),
				'add_new_item'		 => sprintf(__('Add New %s', 'moowoodle'), __('Course', 'moowoodle')),
				'edit_item'			 => sprintf(__('Edit %s', 'moowoodle'), __('Course', 'moowoodle')),
				'new_item' 			 => sprintf(__('New %s', 'moowoodle'), __('Course', 'moowoodle')),
				'all_items'			 => sprintf(__('%s', 'moowoodle'), __('Courses', 'moowoodle')),
				'view_item' 		 => sprintf(__('View %s', 'moowoodle'), __('Course', 'moowoodle')),
				'search_items'		 => sprintf(__('Search %s', 'moowoodle'), __('Courses', 'moowoodle')),
				'not_found'			 => sprintf(__('No %s found', 'moowoodle'), strtolower(__('Courses', 'moowoodle'))),
				'not_found_in_trash' => sprintf(__('No %s found in Trash', 'moowoodle'), strtolower(__('Courses', 'moowoodle'))),
			],
			'public' 			 => false,
			'publicly_queryable' => false,
			'show_ui' 			 => true,
			'query_var' 		 => true,
			'rewrite' 			 => true,
			'has_archive' 		 => false,
			'hierarchical' 		 => false,
			'show_in_menu' 		 => false,
			'supports' 			 => [ 'title', 'editor' ],
			'capability_type' 	 => 'post',
			'capabilities' 		 => [
				'create_posts' => false,
				'delete_posts' => false,
			],
		];

		register_post_type( 'course', $args );
	}

	/**
	 * Register 'course_cat' taxonomy.
	 * @return void
	 */
	public function register_course_taxonomy() {
		register_taxonomy(
			'course_cat',
			'course',
			[
				'labels' => [
					'name' 			    => sprintf(_x('%s category', 'moowoodle'), __('Course', 'moowoodle')),
					'singular_name' 	=> sprintf(_x('%s category', 'moowoodle'), __('Course', 'moowoodle')),
					'add_new_item' 		=> sprintf(_x('Add new %s category', 'moowoodle'), __('Course', 'moowoodle')),
					'new_item_name' 	=> sprintf(_x('New %s category', 'moowoodle'), __('Course', 'moowoodle')),
					'menu_name' 		=> sprintf(_x('%s category', 'moowoodle'), __('Course', 'moowoodle')), //'Categories',
					'search_items' 		=> sprintf(_x('Search %s categories', 'moowoodle'), __('Course', 'moowoodle')), //'Search Course Categories',
					'all_items' 		=> sprintf(_x('All %s categories', 'moowoodle'), __('Course', 'moowoodle')), //'All Course Categories',
					'parent_item' 		=> sprintf(_x('Parent %s category', 'moowoodle'), __('Course', 'moowoodle')), //'Parent Course Category',
					'parent_item_colon' => sprintf(_x('Parent %s category', 'moowoodle'), __('Course', 'moowoodle')), //'Parent Course Category:',
					'edit_item'			=> sprintf(_x('Edit %s category', 'moowoodle'), __('Course', 'moowoodle')), //'Edit Course Category',
					'update_item' 		=> sprintf(_x('New %s category name', 'moowoodle'), __('Course', 'moowoodle')), //'New Course Category Name'
				],
				'show_ui' 		=> false,
				'show_tagcloud' => false,
				'hierarchical' 	=> true,
				'query_var' 	=> true,
			]
		);
	}

	/**
	 * get function for Courses from post.
	 * @param array $args
	 * @return array $courses
	 */
	public function get_courses( $args = [] ) {
		$args = array_merge([
			'post_type'   => 'course',
			'post_status' => 'publish'
		], $args );

		return get_posts($args);
	}

	/**
	 * Update all course
	 * @param mixed $courses
	 * @return void
	 */
	public function update_courses( $courses ) {
		$updated_ids = [];

		foreach ( $courses as $course ) {
			// sync courses post data.
			$course_id = $this->update_course( $course );

			if ( $course_id ) {
				$updated_ids[] = $course_id;
			}

			\MooWoodle\Util::increment_sync_count( 'course' );
		}

		// remove courses that not exist in moodle.
		$this->remove_exclude_ids( $updated_ids );
	}
	
	/**
	 * Update moodle courses data in Wordpress post.
	 * if not exist create new post.
	 * @param array $course (moodle course data)
	 * @return int course id
	 */
	public function update_course( $course ) {
		if ( empty( $course ) || $course['format'] == 'site' ) return 0;

		// get the course id linked with moodle.
		$course_id = self::get_courses(
			[
				'meta_key' 		=> 'moodle_course_id',
				'meta_value' 	=> $course[ 'id' ],
				'meta_compare' 	=> '=',
				'fields'	 	=> 'ids'
			]
		);

		$course_id = $course_id ? $course_id[0] : 0;
		
		// prepare argument for update or create course.
		$args = [
			'post_title' 	=> $course['fullname'],
			'post_name' 	=> $course['shortname'],
			'post_content' 	=> $course['summary'],
			'post_status' 	=> 'publish',
			'post_type' 	=> 'course',
		];

		// if course already exist update course
		// otherwise create new course.
		if ( $course_id ) {
			$args[ 'ID' ] = $course_id;
			$new_course_id = wp_update_post( $args );
		} else {	
			$new_course_id = wp_insert_post( $args );
		}
		
		// update course meta data.
		update_post_meta( $new_course_id, '_course_short_name', sanitize_text_field( $course[ 'shortname' ] ) );
		update_post_meta( $new_course_id, '_course_idnumber', sanitize_text_field( $course[ 'idnumber' ] ) );
		update_post_meta( $new_course_id, '_course_startdate', $course[ 'startdate' ] );
		update_post_meta( $new_course_id, '_course_enddate', $course[ 'enddate' ] );
		update_post_meta( $new_course_id, 'moodle_course_id', ( int ) $course[ 'id' ] );
		update_post_meta( $new_course_id, '_category_id', ( int ) $course[ 'categoryid' ] );
		update_post_meta( $new_course_id, '_visibility', $course[ 'visible' ] ) ? 'visible' : 'hidden';

		// set course category id.
		$term = MooWoodle()->category->get_category( $course[ 'categoryid' ], 'course_cat' );
		if ( $term ) wp_set_post_terms( $new_course_id, $term->term_id, 'course_cat' );
		
		return $new_course_id;
	}
	
	/**
	 * Delete all the courses which id is not prasent in $exclude_ids array.
	 * @param array $exclude_ids (course post ids)
	 * @return void
	 */
	public function remove_exclude_ids( $exclude_ids ) {
		$posts =  get_posts(
			[
				'post_type' 	=> 'course',
				'numberposts' 	=> -1,
				'post_status' 	=> 'publish',
				'post__not_in' 	=> $exclude_ids
			]
		);

		// delete posts.
		foreach ($posts as $post) {
			wp_delete_post( $post->ID, false );
		}
	}

	/**
	 * Get the course url
	 * @param mixed $moodle_course_id
	 * @param mixed $course_name
	 * @return string
	 */
	public function get_course_url( $moodle_course_id ) {
		$redirect_uri  = trailingslashit( MooWoodle()->setting->get_setting( 'moodle_url' ) ) . "course/view.php?id=" . $moodle_course_id;
		return $redirect_uri;
	}

	/**
	 * Creates custom tab for product types.
	 * @param array $product_data_tabs
	 * @return array
	 */
	public function moowoodle_linked_course_tab( $product_data_tabs ) {
		$product_data_tabs[ 'moowoodle' ] = [
			'label'  => __('Moodle Linked Course', 'moowoodle'),
			'target' => 'moowoodle_course_link_tab',
		];
		return $product_data_tabs;
	}

	/**
	 * Add meta box panal.
	 * @return void
	 */
	public function moowoodle_linked_course_panals() {
		global $post;

		$linked_course_id = get_post_meta( $post->ID, 'linked_course_id', true );

		$courses 		  = $this->get_courses([
			'relation' 	  => 'OR',
			'numberposts' => -1,
			'fields' 	  => 'ids',
			'meta_query'  => [
				'relation' 	  => 'OR',
				[
					'key'     => 'linked_product_id',
					'value'   => $post->ID,
				],
				[
					'key'     => 'linked_product_id',
					'compare' => 'NOT EXISTS',
				],
			],
		]);

		?>
		<div id="moowoodle_course_link_tab" class="panel woocommerce_options_panel">
		<p>
			<label for="courses"><?php esc_html_e('Linked Course', 'moowoodle');?></label>
			<select id="courses-select" name="course_id">
				<option value="0"><?php esc_html_e('Select course...', 'moowoodle');?></option>
				<?php
				foreach ( $courses as $course_id ) {
					$course_short_name = get_post_meta( $course_id, '_course_short_name', true );
					$course_path = array_map( function ( $term ) {
						return $term->name;
					}, get_the_terms( $course_id, 'course_cat' ) );
					$course_name = implode( ' / ', $course_path );
					$course_name .= ' - ' . esc_html( get_the_title( $course_id ) );
					?>
					<option value="<?php echo esc_attr( $course_id ); ?>" <?php selected( $course_id, $linked_course_id ); ?>>
						<?php echo esc_html( $course_name ) . ( ! empty( $course_short_name ) ? " ( " . esc_html( $course_short_name ) . " )" : ''); ?>
					</option>
					<?php
				}
				?>
			</select>
    	</p>
		<?php
		echo esc_html_e( "Cannot find your course in this list?", "moowoodle" );
		?>
		<a href="<?php echo esc_url( get_site_url() ) ?>/wp-admin/admin.php?page=moowoodle-synchronization" target="_blank"><?php esc_html_e( 'Synchronize Moodle Courses from here.', 'moowoodle' );?></a>
		<?php
		// Nonce field (for security)
		echo '<input type="hidden" name="product_meta_nonce" value="' . wp_create_nonce() . '"></div>';
	}

	/**
	 * Linked course with a product
	 * @param int $product_id
	 * @return mixed
	 */
	public function save_product_meta_data( $product_id ) {
		// Security check
		if ( !filter_input( INPUT_POST, 'product_meta_nonce', FILTER_DEFAULT ) === null 
			|| !wp_verify_nonce( filter_input( INPUT_POST, 'product_meta_nonce', FILTER_DEFAULT ) )
			|| !current_user_can( 'edit_product', $product_id )
		) {
			return $product_id;
		}

		$course_id = filter_input( INPUT_POST, 'course_id', FILTER_DEFAULT );
		
		// Linked product to course.
		if ( $course_id !== null ) {
			update_post_meta( $course_id, 'linked_product_id', $product_id );
			update_post_meta( $product_id, 'linked_course_id', wp_kses_post( $course_id ) );
			update_post_meta( $product_id, 'moodle_course_id', get_post_meta( $course_id, 'moodle_course_id', true ) );
		}

		return $product_id;
	}
}
