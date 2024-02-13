<?php
class MooWoodle_Product_Data_Tabs {
	public function __construct() {
		add_action('woocommerce_process_product_meta', array(&$this, 'save_product_meta_data'));
		add_filter('woocommerce_product_data_tabs', array(&$this, 'moowoodle_linked_course_tab'), 99, 1);
		add_action('woocommerce_product_data_panels', array(&$this, 'moowoodle_linked_course_panals'));
	}
	/**
	 * Creates custom tab for product types.
	 *
	 * @access public
	 * @param array $product_data_tabs
	 * @return void
	 */
	public function moowoodle_linked_course_tab($product_data_tabs) {
		$product_data_tabs['moowoodle'] = array(
			'label' => __('Moodle Linked Course', 'moowoodle'), // translatable
			'target' => 'moowoodle_course_link_tab', // translatable
		);
		return $product_data_tabs;
	}
	/**
	 * Add meta box panal.
	 *
	 * @access public
	 * @return void
	 */
	public function moowoodle_linked_course_panals() {
		echo '<div id="moowoodle_course_link_tab" class="panel woocommerce_options_panel">';
		global $post;
		$linked_course_id = get_post_meta($post->ID, 'linked_course_id', true);
		$courses = get_posts(array('post_type' => 'course', 'numberposts' => -1, 'post_status' => 'publish'));
		?>
		<p>
			<label for="courses"><?php esc_html_e('Linked Course', 'moowoodle');?></label>
			<select id="courses-select" name="course_id">
				<option value="0"><?php esc_html_e('Select course...', 'moowoodle');?></option>
				<?php
if (!empty($courses)) {
			foreach ($courses as $course) {
				$id = $course->ID;
				$course_short_name = get_post_meta($course->ID, '_course_short_name', true);
				$category_id = get_post_meta($course->ID, '_category_id', true);
				$term_id = moowoodle_get_term_by_moodle_id($category_id, 'course_cat', 'moowoodle_term');
				$course_category_path = get_term_meta($term_id, '_category_path', true);
				$category_ids = explode('/', $course_category_path);
				$course_path = array();
				if (!empty($category_ids)) {
					foreach ($category_ids as $cat_id) {
						if (!empty($cat_id)) {
							$term_id = moowoodle_get_term_by_moodle_id(intval($cat_id), 'course_cat', 'moowoodle_term');
							$term = get_term($term_id, 'course_cat');
							$course_path[] = $term->name;
						}
					}
				}
				$course_name = '';
				if (!empty($course_path)) {
					$course_name = implode(' / ', $course_path);
					$course_name .= ' - ';
				}
				$course_name .= $course->post_title;
				if (!empty($id)) {
					?>
							<option value="<?php echo esc_attr($id); ?>" <?php if (!empty($linked_course_id) && $id == $linked_course_id) {
						echo 'selected="selected"';
					}
					?>><?php echo $course_name;
					if (!empty($course_short_name)) {
						echo " ( " . esc_html($course_short_name) . " )";
					}
					?></option>
				<?php
}
			}
		}
		?>
			</select>
		</p>
		<?php
echo esc_html_e("Cannot find your course in this list?", "moowoodle");
		?>
		<a href="<?php echo esc_url(get_site_url()) ?>/wp-admin/admin.php?page=moowoodle-synchronization" target="_blank"><?php esc_html_e('Synchronize Moodle Courses from here.', 'moowoodle');?></a>
<?php
// Nonce field (for security)
		echo '<input type="hidden" name="product_meta_nonce" value="' . wp_create_nonce() . '">';
		echo '</div>';
	}
	/**
	 * Save product meta.
	 *
	 * @access public
	 * @param int $post_id
	 * @return void
	 */
	public function save_product_meta_data($post_id) {
		// Security check
		if (filter_input(INPUT_POST, 'product_meta_nonce', FILTER_DEFAULT) === null) {
			return $post_id;
		}
		//Verify that the nonce is valid.
		if (filter_input(INPUT_POST, 'product_meta_nonce', FILTER_DEFAULT) === null) {
			return $post_id;
		}
		if (filter_input(INPUT_POST, 'edit_product', FILTER_DEFAULT) === null) {
			return $post_id;
		}
		$course_id = filter_input(INPUT_POST, 'course_id', FILTER_DEFAULT);
		if ($course_id !== null) {
			update_post_meta($post_id, 'linked_course_id', wp_kses_post($course_id));
			update_post_meta($post_id, '_sku', 'course-' . get_post_meta($course_id, '_sku'));
			update_post_meta($post_id, 'moodle_course_id', get_post_meta($course_id, 'moodle_course_id'));
		}
	}
}
