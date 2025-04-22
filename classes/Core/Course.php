<?php

namespace MooWoodle\Core;

class Course {
	public function __construct() {
		// Add Link Moodle Course in WooCommerce edit product tab.
		add_filter( 'woocommerce_product_data_tabs', [ &$this, 'moowoodle_linked_course_tab' ], 99, 1 );
		add_action( 'woocommerce_product_data_panels', [ &$this, 'moowoodle_linked_course_panals' ] );
		add_action( 'woocommerce_process_product_meta', [ &$this, 'save_product_meta_data' ] );
	}
	
	// public function get_courses( $course_id ) {
	// 	global $wpdb;
	
	// 	// Query the courses using linked_course_id (primary key) for the current product
	// 	$courses = $wpdb->get_results(
	// 		$wpdb->prepare(
	// 			"SELECT * FROM {$wpdb->prefix}moowoodle_courses 
	// 			 WHERE id = %d",
	// 			$course_id
	// 		)
	// 	);
	
	// 	return $courses;
	// }
	
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
		global $post, $wpdb;
		$linked_course_id = get_post_meta( $post->ID, 'linked_course_id', true );
	
		$courses_table    = $wpdb->prefix . 'moowoodle_courses';
		$categories_table = $wpdb->prefix . 'moowoodle_categories';
	
		// Fetch only the linked course
		$courses = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT c.id, c.fullname, c.shortname, cat.name AS category_name
				 FROM $courses_table c
				 LEFT JOIN $categories_table cat ON c.category_id = cat.moodle_category_id
				 WHERE c.id = %d",
				$linked_course_id
			)
		);
		?>
		<div id="moowoodle_course_link_tab" class="panel woocommerce_options_panel">
			<p>
				<label for="courses"><?php esc_html_e( 'Linked Course', 'moowoodle' ); ?></label>
				<select id="courses-select" name="course_id">
					<option value="0"><?php esc_html_e( 'Select course...', 'moowoodle' ); ?></option>
					<?php
					foreach ( $courses as $course ) {
						$course_name = '';
	
						if ( ! empty( $course->category_name ) ) {
							$course_name .= esc_html( $course->category_name ) . ' - ';
						}
	
						$course_name .= esc_html( $course->fullname );
	
						if ( ! empty( $course->shortname ) ) {
							$course_name .= ' ( ' . esc_html( $course->shortname ) . ' )';
						}
						?>
						<option value="<?php echo esc_attr( $course->id ); ?>" <?php selected( $course->id, $linked_course_id ); ?>>
							<?php echo $course_name; ?>
						</option>
						<?php
					}
					?>
				</select>
			</p>
			<p>
				<?php esc_html_e( "Cannot find your course in this list?", "moowoodle" ); ?><br/>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=moowoodle-synchronization' ) ); ?>" target="_blank">
					<?php esc_html_e( 'Synchronize Moodle Courses from here.', 'moowoodle' ); ?>
				</a>
			</p>
			<input type="hidden" name="product_meta_nonce" value="<?php echo esc_attr( wp_create_nonce() ); ?>">
		</div>
		<?php
	}

	/**
	 * Linked course with a product
	 * @param int $product_id
	 * @return mixed
	 */
	public function save_product_meta_data( $product_id ) {
		file_put_contents( WP_CONTENT_DIR . '/mo_file_log.txt', 'response:cou'. var_export($product_id, true) . "\n", FILE_APPEND );

		// Security check
		if ( !filter_input( INPUT_POST, 'product_meta_nonce', FILTER_DEFAULT ) === null 
			|| !wp_verify_nonce( filter_input( INPUT_POST, 'product_meta_nonce', FILTER_DEFAULT ) )
			|| !current_user_can( 'edit_product', $product_id )
		) {
			return $product_id;
		}

		// Get the selected course id
		$course_id = intval( filter_input( INPUT_POST, 'course_id', FILTER_DEFAULT ) );
		// file_put_contents( WP_CONTENT_DIR . '/mo_file_log.txt', 'response:ci'. var_export($course_id, true) . "\n", FILE_APPEND );
		// file_put_contents( WP_CONTENT_DIR . '/mo_file_log.txt', 'response:ci'. var_export($product_id, true) . "\n", FILE_APPEND );

		// // Linked product to course.
		// if ( $course_id ) {
		// 	update_post_meta( $course_id, 'linked_product_id', $product_id );
		// 	update_post_meta( $product_id, 'linked_course_id', wp_kses_post( $course_id ) );
		// 	update_post_meta( $product_id, 'moodle_course_id', get_post_meta( $course_id, 'moodle_course_id', true ) );
		// } else {
		// 	// Deattach previously set linked
		// 	$linked_course_id = intval( get_post_meta( $product_id, 'linked_course_id', true ) );
		// 	delete_post_meta( $linked_course_id, 'linked_product_id' );
		// 	delete_post_meta( $product_id, 'linked_course_id' );
		// }

		return $product_id;
	}

	/**
	 * Insert or update Moodle courses into custom database table.
	 *
	 * @param array $courses List of Moodle course objects.
	 * @return array List of updated Moodle course IDs.
	 */
	public function update_courses( $courses ) {
		$updated_ids = [];
	
		foreach ( $courses as $course ) {
			$updated_id = $this->update_course( $course );
	
			if ( $updated_id ) {
				$updated_ids[] = $updated_id;
				// Only increment sync count on insert
			    \MooWoodle\Util::increment_sync_count( 'course' );
			}
		}
	
		return $updated_ids;
	}
	

	public function update_course( $course ) {
		global $wpdb;
	
		$table = $wpdb->prefix . 'moowoodle_courses';
	
		// Skip site format courses.
		if ( isset( $course['format'] ) && $course['format'] === 'site' ) {
			return false;
		}
	
		$moodle_course_id = (int) $course['id'];
	
		// Check if course already exists.
		$exists = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM $table WHERE moodle_course_id = %d",
				$moodle_course_id
			)
		);
	
		$data = [
			'moodle_course_id' => $moodle_course_id,
			'shortname'        => sanitize_text_field( $course['shortname'] ),
			'category_id'      => (int) $course['categoryid'],
			'fullname'         => sanitize_text_field( $course['fullname'] ),
			'startdate'        => isset( $course['startdate'] ) ? (int) $course['startdate'] : null,
			'enddate'          => isset( $course['enddate'] ) ? (int) $course['enddate'] : null,
		];
	
		if ( $exists ) {
			$wpdb->update(
				$table,
				$data,
				[ 'moodle_course_id' => $moodle_course_id ]
			);
		} else {
			$data['created'] = time();
			$wpdb->insert(
				$table,
				$data
			);
		}
	
		return $moodle_course_id;
	}
	
	
	/**
	 * Migrate courses from post table
	 */
	public static function migrate_courses() {
		global $wpdb;
	
		$courses = get_posts( [
			'post_type'      => 'course',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'meta_key'       => 'moodle_course_id',
		] );
	
		if ( empty( $courses ) ) return;
	
		foreach ( $courses as $course ) {
			$moodle_course_id = get_post_meta( $course->ID, 'moodle_course_id', true );
			if ( ! $moodle_course_id ) continue;
	
			$shortname   = get_post_meta( $course->ID, '_course_short_name', true );
			$category_id = get_post_meta( $course->ID, '_category_id', true );
			$startdate   = get_post_meta( $course->ID, '_course_startdate', true );
			$enddate     = get_post_meta( $course->ID, '_course_enddate', true );
			$product_id  = get_post_meta( $course->ID, 'linked_product_id', true );
	
			// Check if course already exists
			$exists = $wpdb->get_var( $wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}moowoodle_courses WHERE moodle_course_id = %d",
				$moodle_course_id
			) );
	
			$data = [
				'moodle_course_id' => (int) $moodle_course_id,
				'shortname'        => sanitize_text_field( $shortname ),
				'category_id'      => (int) $category_id,
				'fullname'         => sanitize_text_field( $course->post_title ),
				'startdate'        => $startdate ? (int) $startdate : null,
				'enddate'          => $enddate ? (int) $enddate : null,
				'created'          => time(),
			];
	
			if ( $exists ) {
				$wpdb->update(
					$wpdb->prefix . 'moowoodle_courses',
					$data,
					[ 'id' => $exists ]
				);
				$new_course_id = $exists;
			} else {
				$wpdb->insert(
					$wpdb->prefix . 'moowoodle_courses',
					$data
				);
				$new_course_id = $wpdb->insert_id;
			}
	
			if ( $product_id ) {
				update_post_meta( $product_id, 'linked_course_id', $new_course_id );
			}
	
			wp_delete_post( $course->ID, true );
		}
	}
	
}