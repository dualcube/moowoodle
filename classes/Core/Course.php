<?php

namespace MooWoodle\Core;

use MooWoodle\Util as util;

class Course {
	public function __construct() {
		// Add Link Moodle Course in WooCommerce edit product tab.
		add_filter( 'woocommerce_product_data_tabs', [ &$this, 'moowoodle_linked_course_tab' ], 99, 1 );
		add_action( 'woocommerce_product_data_panels', [ &$this, 'moowoodle_linked_course_panals' ] );
		add_action( 'wp_ajax_get_linked_items', [ $this, 'get_moowoodle_linkable_items' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );

	}
	
	public function enqueue_admin_assets() {

		\MooWoodle\FrontendScripts::admin_load_scripts();
		\MooWoodle\FrontendScripts::enqueue_script( 'moowoodle-linked-panel-js' );
		\MooWoodle\FrontendScripts::enqueue_style( 'moowoodle-linked-panel-css' );
		\MooWoodle\FrontendScripts::localize_scripts( 'moowoodle-linked-panel-js' );

	}
	

	/**
	 * Creates custom tab for product types.
	 * @param array $product_data_tabs
	 * @return array
	 */
	public function moowoodle_linked_course_tab( $product_data_tabs ) {
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
	public function moowoodle_linked_course_panals() {
		global $post;

		// Get linked course and cohort IDs
		$linked_course_id = get_post_meta( $post->ID, 'linked_course_id', true );
		$linked_cohort_id = get_post_meta( $post->ID, 'linked_cohort_id', true );

		// Determine the default link type (course or cohort)
		$default_type = $linked_course_id ? 'course' : ( $linked_cohort_id ? 'cohort' : '' );

		// Check if Pro version is active
		$pro_active = MooWoodle()->util->is_khali_dabba();
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
						<input type="radio" name="link_type" value="cohort"
							<?php checked( $default_type, 'cohort' ); ?>
							<?php echo ! $pro_active ? 'disabled' : ''; ?>>
						<?php esc_html_e( 'Cohort', 'moowoodle' ); ?>
						<?php if ( ! $pro_active ) : ?>
							<span>Pro</span>
						<?php endif; ?>
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
	 * Handle request to fetch linkable items (courses/cohorts) for product linking.
	 *
	 * @return void
	 */
	public function get_moowoodle_linkable_items() {
		// Verify nonce
		if ( ! check_ajax_referer( 'moowoodle_meta_nonce', 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid nonce', 'moowoodle' ) );
			return;
		}
	
		global $wpdb;
	
		// Retrieve and sanitize input
		$type        = sanitize_text_field( filter_input( INPUT_POST, 'type' ) ?: '' );
		$post_id     = absint( filter_input( INPUT_POST, 'post_id' ) ?: 0 );
		$items       = [];
		$selected_id = null;
	
		if ( $type === 'course' ) {
			$selected_id = get_post_meta( $post_id, 'linked_course_id', true );
	
			if ( $selected_id ) {
				$item = $wpdb->get_row(
					$wpdb->prepare(
						"SELECT id, fullname AS name FROM {$wpdb->prefix}moowoodle_courses WHERE id = %d",
						$selected_id
					)
				);
	
				if ( $item ) {
					$items[] = $item;
				}
			} else {
				$items = $wpdb->get_results(
					"SELECT id, fullname AS name 
					 FROM {$wpdb->prefix}moowoodle_courses 
					 WHERE id NOT IN (
						 SELECT meta_value 
						 FROM {$wpdb->postmeta} 
						 WHERE meta_key = 'linked_course_id'
					 )"
				);
			}
	
			wp_send_json_success( [
				'items'       => $items,
				'selected_id' => $selected_id,
			] );
		}
	
		wp_send_json_error( __( 'Invalid type', 'moowoodle' ) );
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
	
		$table = $wpdb->prefix . Util::TABLES['course'];
	
		// Skip site format courses
		if ( $course['format'] === 'site' ) {
			return false;
		}
	
		$moodle_course_id = (int) $course['id'];
	
		// Prepare data for insertion or update
		$data = [
			'moodle_course_id' => $moodle_course_id,
			'shortname'        => sanitize_text_field( $course['shortname'] ?? '' ),
			'category_id'      => (int) ( $course['categoryid'] ?? 0 ),
			'fullname'         => sanitize_text_field( $course['fullname'] ?? '' ),
			'startdate'        => (int) ( $course['startdate'] ?? 0 ),
			'enddate'          => (int) ( $course['enddate'] ?? 0 ),
		];
	
		// Check if the course already exists
		$existing_course = $this->get_course([
			'moodle_course_id' => $course['id']
		] );
	
		if ( $existing_course ) {
			// Course exists, so update it
			$wpdb->update( $table, $data, [ 'moodle_course_id' => $moodle_course_id ] );
		} else {
			// Course doesn't exist, insert new course
			$data['created'] = time();
			$wpdb->insert( $table, $data );
		}
	
		return $moodle_course_id;
	}
	
	
	
	
	/**
	 * Get the full course data from the course table by internal ID or Moodle course ID.
	 *
	 * @param int $id The course ID.
	 * @param bool $is_moodle_id Whether the ID is a Moodle course ID (true) or internal ID (false).
	 * @return object|null Course data if found, null otherwise.
	 */
	// public function get_course( $id, $is_moodle_id = false ) {
	// 	global $wpdb;

	// 	$id = (int) $id;
	// 	if ( $id <= 0 ) {
	// 		return null;
	// 	}

	// 	$table = $wpdb->prefix . Util::TABLES['course'];
	// 	$column = $is_moodle_id ? 'moodle_course_id' : 'id';

	// 	return $wpdb->get_row(
	// 		$wpdb->prepare(
	// 			"SELECT * FROM $table WHERE $column = %d",
	// 			$id
	// 		)
	// 	);
	// }


	/**
     * Get all rules
     * @return array|object|null
     */
    public static function get_course( $where ) {
        global $wpdb;

        // Store query segment
        $query_segments = []; 

        // User Query
        if ( isset( $where[ 'id' ] ) ) {
            $query_segments[] = " ( id = " . $where[ 'id' ] . " ) ";
        }

        // Role Query
        if ( isset( $where[ 'moodle_course_id' ] ) ) {
            $query_segments[] = " ( moodle_course_id = " . $where[ 'moodle_course_id' ] . " ) ";
        }

        // Product Query
        if ( isset( $where[ 'shortname' ] ) ) {
            $query_segments[] = " ( shortname = " . $where[ 'shortname' ] . " ) ";
        }
        
        // Category Query
        if ( isset( $where[ 'category_id' ] ) ) {
            $query_segments[] = " ( category_id = " . $where[ 'category_id' ] . " ) ";
        }

        if ( isset( $where[ 'fullname' ] ) ) {
            $query_segments[] = " ( fullname = " . $where[ 'fullname' ] . " ) ";
        }

        if ( isset( $where[ 'startdate' ] ) ) {
            $query_segments[] = " ( startdate = " . $where[ 'startdate' ] . " ) ";
        }

        if ( isset( $where[ 'enddate' ] ) ) {
            $query_segments[] = " ( enddate = " . $where[ 'enddate' ] . " ) ";
        }

        // get the table
        $table = $wpdb->prefix . Util::TABLES['course'];

        // Base query
        $query = "SELECT * FROM $table";

        // Join the query parts with 'AND'
        $where_query = implode( ' AND ', $query_segments );

        if ( $where_query ) {
            $query .= " WHERE $where_query";
        }

        // Get all rows
        $results = $wpdb->get_results( $query, ARRAY_A );

        return $results;
    }

}