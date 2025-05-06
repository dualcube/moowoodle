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

		// Retrieve and sanitize input
		$type        = sanitize_text_field( filter_input( INPUT_POST, 'type' ) ?: '' );
		$post_id     = absint( filter_input( INPUT_POST, 'post_id' ) ?: 0 );
		$items       = [];
		$selected_id = null;

		if ( $type === 'course' ) {
			$selected_id = get_post_meta( $post_id, 'linked_course_id', true );

			if ( $selected_id ) {
				$courses = MooWoodle()->course->get_course([
					'id' => $selected_id
				]);

				if ( ! empty( $courses ) ) {
					$items[] = $courses[0];
				}
			} else {
				$items = MooWoodle()->course->get_course([
					'product_id' => 0
				]);
			}
			wp_send_json_success( [
				'items'       => $items,
				'selected_id' => $selected_id,
			] );
		}

		wp_send_json_error( __( 'Invalid type', 'moowoodle' ) );
	}

	

	public function update_courses( $courses ) {
		foreach ( $courses as $course ) {
			// Skip site format courses
			if ( $course['format'] === 'site' ) {
				continue;
			}
	
			$moodle_course_id = (int) $course['id'];
	
			$args = [
				'moodle_course_id' => $moodle_course_id,
				'shortname'        => sanitize_text_field( $course['shortname'] ?? '' ),
				'category_id'      => (int) ( $course['categoryid'] ?? 0 ),
				'fullname'         => sanitize_text_field( $course['fullname'] ?? '' ),
				'startdate'        => (int) ( $course['startdate'] ?? 0 ),
				'enddate'          => (int) ( $course['enddate'] ?? 0 ),
			];
	
			$existing = $this->get_course([ 'moodle_course_id' => $moodle_course_id ]);
	
			if ( $existing ) {
				$this->update_course( $moodle_course_id, $args );
			} else {
				$args['created'] = time();
				$this->set_course( $args );
			}
	
			\MooWoodle\Util::increment_sync_count( 'course' );
		}
	}
	

	public function update_course( $moodle_course_id, $args ) {
		global $wpdb;
	
		if ( ! $moodle_course_id || empty( $args ) ) {
			return false;
		}
		$table = $wpdb->prefix . Util::TABLES['course'];
	
		return $wpdb->update(
			$table,
			$args,
			[ 'moodle_course_id' => $moodle_course_id ]
		);
	}
	
	public function set_course( $args ) {
		global $wpdb;
	
		if ( empty( $args ) || empty( $args['moodle_course_id'] ) ) {
			return false;
		}
	
		$table = $wpdb->prefix . Util::TABLES['course'];
	
		return $wpdb->insert(
			$table,
			$args
		);
	}
	
	/**
     * Get all course
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

        if ( isset( $where[ 'product_id' ] ) ) {
            $query_segments[] = " ( product_id = " . $where[ 'product_id' ] . " ) ";
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