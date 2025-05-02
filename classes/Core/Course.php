<?php

namespace MooWoodle\Core;

class Course {
	public function __construct() {
		// Add Link Moodle Course in WooCommerce edit product tab.
		add_filter( 'woocommerce_product_data_tabs', [ &$this, 'moowoodle_linked_course_tab' ], 99, 1 );
		add_action( 'woocommerce_product_data_panels', [ &$this, 'moowoodle_linked_course_panals' ] );
		add_action( 'wp_ajax_get_linked_items', [ $this, 'ajax_get_linked_items' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );

	}
	public function enqueue_admin_assets($hook) {
		if ($hook !== 'post.php' && $hook !== 'post-new.php') {
			return;
		}
		
		// Use dynamic version from MooWoodle class
		$version = MooWoodle()->version;
	
		wp_enqueue_style(
			'moowoodle-linked-panel-css',
			MooWoodle()->plugin_url . 'assets/css/linked-panel.css',
			[],
			$version
		);
		
		wp_enqueue_script(
			'moowoodle-linked-panel-js',
			MooWoodle()->plugin_url . 'assets/js/linked-panel.js',
			['jquery'],
			$version,
			true
		);
	
		wp_localize_script('moowoodle-linked-panel-js', 'moowoodle', [
			'ajaxurl'     => admin_url('admin-ajax.php'),
			'select_text' => __('Select an item...', 'moowoodle'),
			'khali_dabba' => MooWoodle()->util->is_khali_dabba(),
		]);
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
			'label'  => __('Moodle Linked Course or Cohort', 'moowoodle'),
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
		$post_id = $post->ID;
		$nonce = wp_create_nonce('moowoodle_meta_nonce');
	
		$linked_course_id = get_post_meta($post_id, 'linked_course_id', true);
		$linked_cohort_id = get_post_meta($post_id, 'linked_cohort_id', true);
	
		$default_type = '';
		if ($linked_course_id) {
			$default_type = 'course';
		} elseif ($linked_cohort_id) {
			$default_type = 'cohort';
		}
	
		$pro_active = MooWoodle()->util->is_khali_dabba();
		?>
		<div id="moowoodle_course_link_tab" class="panel">
			<p class="form-field moowoodle-link-type-field">
				<label><?php esc_html_e('Link Type', 'moowoodle'); ?></label><br>
				<span class="moowoodle-radio-group">
					<label class="moowoodle-radio-option">
						<input type="radio" name="link_type" value="course" <?php checked($default_type, 'course'); ?>>
						<?php esc_html_e('Course', 'moowoodle'); ?>
					</label>
					<label class="moowoodle-radio-option cohort">
						<input type="radio" name="link_type" value="cohort"
							<?php checked($default_type, 'cohort'); ?>
							<?php echo !$pro_active ? 'disabled' : ''; ?>>
						<?php esc_html_e('Cohort', 'moowoodle'); ?>
						<?php if (!$pro_active): ?>
							<span>Pro</span>
						<?php endif; ?>
					</label>
				</span>
			</p>
	
			<p id="dynamic-link-select" class="form-field <?php echo $default_type ? 'show' : ''; ?>">
				<label for="linked_item"><?php esc_html_e('Select Item', 'moowoodle'); ?></label>
				<select id="linked_item" name="linked_item">
					<option value=""><?php esc_html_e('Select an item...', 'moowoodle'); ?></option>
				</select>
			</p>
	
			<p>
				<span>
					<?php esc_html_e("Can't find your course or cohort?", "moowoodle"); ?>
					<a href="<?php echo esc_url(admin_url('admin.php?page=moowoodle-synchronization')); ?>" target="_blank">
						<?php esc_html_e('Synchronize Moodle data from here.', 'moowoodle'); ?>
					</a>
				</span>
			</p>
	
			<input type="hidden" name="moowoodle_meta_nonce" value="<?php echo esc_attr($nonce); ?>">
			<input type="hidden" name="product_meta_nonce" value="<?php echo wp_create_nonce(); ?>">
			<input type="hidden" id="post_ID" value="<?php echo esc_attr($post_id); ?>">
		</div>
	<?php
	}
	
	public function ajax_get_linked_items() {
		// Verify nonce
		if ( ! check_ajax_referer( 'moowoodle_meta_nonce', 'nonce', false ) ) {
			wp_send_json_error( 'Invalid nonce' );
			return;
		}
	
		global $wpdb;
		$type     = filter_input( INPUT_POST, 'type', FILTER_SANITIZE_STRING );
		$post_id  = filter_input( INPUT_POST, 'post_id', FILTER_VALIDATE_INT );
		$items    = [];
		$selected_id = null;
	
		if ( $type === 'course' ) {
			$selected_id = get_post_meta( $post_id, 'linked_course_id', true );
	
			if ( $selected_id ) {
				$row = $wpdb->get_row( $wpdb->prepare(
					"SELECT id, fullname AS name FROM {$wpdb->prefix}moowoodle_courses WHERE id = %d",
					$selected_id
				) );
				if ( $row ) {
					$items[] = $row;
				}
			} else {
				$items = $wpdb->get_results("
					SELECT id, fullname AS name FROM {$wpdb->prefix}moowoodle_courses 
					WHERE id NOT IN (
						SELECT meta_value FROM {$wpdb->postmeta} 
						WHERE meta_key = 'linked_course_id'
					)
				");
			}
	
			wp_send_json_success([
				'items'       => $items,
				'selected_id' => $selected_id,
			]);
		}
	
		// If type is not 'course', do nothing — Pro plugin may handle it
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
	 * Get the full course data from the course table by course ID.
	 *
	 * @param int $course_id The internal course ID (primary key of wp_moowoodle_courses).
	 * @return object|null Returns course data object if found, null otherwise.
	 */
	public function get_course( $course_id ) {
		global $wpdb;

		if ( ! $course_id || ! is_numeric( $course_id ) ) {
			return null;
		}

		$table_name = "{$wpdb->prefix}moowoodle_courses";

		$course = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM $table_name WHERE id = %d",
			$course_id
		) );

		return $course ?: null;
	}
	
	// Function to fetch course from wp_moowoodle_courses table based on Moodle course ID
	public static function get_course_by_mmodle_course_id( $moodle_course_id ) {
		global $wpdb;

		$table = $wpdb->prefix . 'moowoodle_courses';

		// Fetch course data linked to Moodle course ID
		$course = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $table WHERE moodle_course_id = %d",
				$moodle_course_id
			)
		);

		// Return course data if found, otherwise return null
		return $course ? $course : null;
	}

	
}