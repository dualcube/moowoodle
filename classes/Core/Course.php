<?php

namespace MooWoodle\Core;

use MooWoodle\Util;

class Course {
	public function __construct() {
		// Add Link Moodle Course in WooCommerce edit product tab.
		add_filter( 'woocommerce_product_data_tabs', [ &$this, 'add_additional_product_tab' ], 99, 1 );
		add_action( 'woocommerce_product_data_panels', [ &$this, 'add_additional_product_data_panel' ] );
		add_action( 'wp_ajax_get_linkable_courses_or_cohorts', [ $this, 'get_linkable_courses' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );

	}
	
	public function enqueue_admin_assets() {

		\MooWoodle\FrontendScripts::admin_load_scripts();
		\MooWoodle\FrontendScripts::enqueue_script( 'moowoodle-product-tab-js' );
		\MooWoodle\FrontendScripts::enqueue_style( 'moowoodle-product-tab-css' );
		\MooWoodle\FrontendScripts::localize_scripts( 'moowoodle-product-tab-js' );

	}
	

	/**
	 * Creates custom tab for product types.
	 * @param array $product_data_tabs
	 * @return array
	 */
	public function add_additional_product_tab( $product_data_tabs ) {
		$product_data_tabs[ 'moowoodle' ] = [
			'label'  => __('Moodle Linked Course or Cohort', 'moowoodle'),
			'target' => 'moowoodle_course_link_tab',
		];
		return $product_data_tabs;
	}

	/**
	 * Add meta box panel.
	 * @return void
	 */
	public function add_additional_product_data_panel() {
		global $post;

		$linked_course_id = get_post_meta( $post->ID, 'linked_course_id', true );
		$linked_cohort_id = get_post_meta( $post->ID, 'linked_cohort_id', true );
		$default_type     = $linked_course_id ? 'course' : ( $linked_cohort_id ? 'cohort' : '' );
		?>
		<div id="moowoodle_course_link_tab" class="panel">
			<p class="form-field moowoodle-link-type-field">
				<label><?php esc_html_e( 'Link Type', 'moowoodle' ); ?></label><br>
				<span class="moowoodle-radio-group">
					<label class="moowoodle-radio-option">
						<input type="radio" name="link_type" value="course" <?php checked( $default_type, 'course' ); ?>>
						<?php esc_html_e( 'Course', 'moowoodle' ); ?>
					</label>
					<label class="moowoodle-radio-option cohort">
						<input type="radio" name="link_type" value="cohort" <?php checked( $default_type, 'cohort' ); ?> 
							<?php echo MooWoodle()->util->is_khali_dabba() ? '' : 'disabled'; ?>>
						<?php esc_html_e( 'Cohort', 'moowoodle' ); ?>
						<?php echo MooWoodle()->util->is_khali_dabba() ? '' : '<span>Pro</span>'; ?>
					</label>
				</span>
			</p>

			<p id="dynamic-link-select" class="form-field <?php echo $default_type ? 'show' : ''; ?>">
				<label for="linked_item"><?php esc_html_e( 'Select Item', 'moowoodle' ); ?></label>
				<select id="linked_item" name="linked_item">
					<option value=""><?php esc_html_e( 'Select an item...', 'moowoodle' ); ?></option>
				</select>
			</p>

			<p>
				<span>
					<?php esc_html_e( "Can't find your course or cohort?", 'moowoodle' ); ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=moowoodle-synchronization' ) ); ?>" target="_blank">
						<?php esc_html_e( 'Synchronize Moodle data from here.', 'moowoodle' ); ?>
					</a>
				</span>
			</p>

			<input type="hidden" name="moowoodle_meta_nonce" value="<?php echo esc_attr( wp_create_nonce( 'moowoodle_meta_nonce' ) ); ?>">
			<input type="hidden" name="product_meta_nonce" value="<?php echo esc_attr( wp_create_nonce() ); ?>">
			<input type="hidden" id="post_id" value="<?php echo esc_attr( $post->ID ); ?>">
		</div>
		<?php
	}
	
	/**
	 * Handle AJAX request to fetch courses available for linking to a product.
	 *
	 * @return void
	 */
	public function get_linkable_courses() {
		// Verify nonce
		if ( ! check_ajax_referer( 'moowoodle_meta_nonce', 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid nonce', 'moowoodle' ) );
			return;
		}

		// Retrieve and sanitize input
		$type = sanitize_text_field( filter_input( INPUT_POST, 'type' ) ?: '' );
		$post_id = absint( filter_input( INPUT_POST, 'post_id' ) ?: 0 );
		$linkable_courses = [];
		$linked_course_id = null;

		if ( $type === 'course' ) {
			$linked_course_id = get_post_meta( $post_id, 'linked_course_id', true );

			if ( $linked_course_id ) {
				$courses = MooWoodle()->course->get_course([
					'id' => $linked_course_id
				]);

				if ( ! empty( $courses ) ) {
					$linkable_courses[] = $courses[0];
				}
			} else {
				$linkable_courses = MooWoodle()->course->get_course([
					'product_id' => 0
				]);
			}

			wp_send_json_success( [
				'items'       => $linkable_courses,
				'selected_id' => $linked_course_id,
			] );
		}else if( $type === 'cohort' ){
			return apply_filters( 'get_linkable_cohorts', $post_id );
		}
		
		wp_send_json_error( __( 'Invalid type', 'moowoodle' ) );
	}

	/**
	 * Update or insert multiple courses based on Moodle data.
	 * Skips courses with format 'site'.
	 *
	 * @param array $courses List of courses to update or insert.
	 * @return void
	 */
	public function update_courses( $courses ) {
		foreach ( $courses as $course ) {
			// Skip site format courses
			if ( $course['format'] === 'site' ) {
				continue;
			}
		
			$args = [
				'moodle_course_id' => (int) $course['id'],
				'shortname'        => sanitize_text_field( $course['shortname'] ?? '' ),
				'category_id'      => (int) ( $course['categoryid'] ?? 0 ),
				'fullname'         => sanitize_text_field( $course['fullname'] ?? '' ),
				'startdate'        => (int) ( $course['startdate'] ?? 0 ),
				'enddate'          => (int) ( $course['enddate'] ?? 0 ),
			];

			self::save_course( $args );

			\MooWoodle\Util::increment_sync_count( 'course' );
		}
	}
	
	/**
	 * Insert or update a course record by Moodle course ID.
	 *
	 * @param array $args Course data. Must include 'moodle_course_id'.
	 * @return int|false Rows affected or false on failure.
	 */
	public static function save_course( $args ) {
		global $wpdb;

		if ( empty( $args ) || empty( $args['moodle_course_id'] ) ) {
			return false;
		}

		$table = $wpdb->prefix . Util::TABLES['course'];

		if ( self::get_course( [ 'moodle_course_id' => $args['moodle_course_id'] ] ) ) {
			return $wpdb->update(
				$table,
				$args,
				[ 'moodle_course_id' => $args['moodle_course_id'] ]
			);
		}
		
		$args['created'] = current_time( 'mysql' );

		$wpdb->insert( $table, $args );
		return $wpdb->insert_id;
	}

	/**
	 * Get course records from the database based on filters.
	 *
	 * @param array $where Conditions to filter courses.
	 * @return array List of matching courses.
	 */
	public static function get_course( $where ) {
		global $wpdb;
	
		$query_segments = [];
	
		if ( isset( $where['id'] ) ) {
			$query_segments[] = " ( id = " . esc_sql( intval( $where['id'] ) ) . " ) ";
		}
	
		if ( isset( $where['moodle_course_id'] ) ) {
			$query_segments[] = " ( moodle_course_id = " . esc_sql( intval( $where['moodle_course_id'] ) ) . " ) ";
		}
	
		if ( isset( $where['shortname'] ) ) {
			$query_segments[] = " ( shortname = '" . esc_sql( $where['shortname'] ) . "' ) ";
		}
	
		if ( isset( $where['category_id'] ) ) {
			$query_segments[] = " ( category_id = " . esc_sql( intval( $where['category_id'] ) ) . " ) ";
		}
	
		if ( isset( $where['product_id'] ) ) {
			$query_segments[] = " ( product_id = " . esc_sql( intval( $where['product_id'] ) ) . " ) ";
		}
	
		if ( isset( $where['fullname'] ) ) {
			$query_segments[] = " ( fullname = '" . esc_sql( $where['fullname'] ) . "' ) ";
		}
	
		if ( isset( $where['startdate'] ) ) {
			$query_segments[] = " ( startdate = " . esc_sql( intval( $where['startdate'] ) ) . " ) ";
		}
	
		if ( isset( $where['enddate'] ) ) {
			$query_segments[] = " ( enddate = " . esc_sql( intval( $where['enddate'] ) ) . " ) ";
		}

		if ( isset( $where['ids'] ) && is_array( $where['ids'] ) ) {
			$ids = implode( ',', array_map( 'intval', $where['ids'] ) );
			$query_segments[] = "id IN ($ids)";
		}
		
		$table = $wpdb->prefix . Util::TABLES['course'];
		$query = "SELECT * FROM $table";
	
		if ( ! empty( $query_segments ) ) {
			$query .= " WHERE " . implode( ' AND ', $query_segments );
		}
	
		if ( isset( $where['limit'] ) && isset( $where['offset'] ) ) {
			$limit = esc_sql( intval( $where['limit'] ) );
			$offset = esc_sql( intval( $where['offset'] ) );
			$query .= " LIMIT $limit OFFSET $offset";
		}
	
		$results = $wpdb->get_results( $query, ARRAY_A );
	
		return $results;
	}
	
}