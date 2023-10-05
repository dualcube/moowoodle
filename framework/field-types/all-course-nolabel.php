<?php
global $MooWoodle;
$pro_sticker = $pro_popup_overlay = '';
if ($MooWoodle->moowoodle_pro_adv) {
	$pro_sticker = '<span class="mw-pro-tag">Pro</span>';
	$pro_popup_overlay = ' mw-pro-popup-overlay ';
}
// Include the JavaScript above in your plugin
wp_enqueue_script('moowoodle_all_course_tables', plugins_url('../../assets/admin/js/moowoodle-all-course-table.js', __FILE__), array('jquery'), '', true);
wp_enqueue_style('woocommerce_course_css', $MooWoodle->plugin_url . 'assets/admin/css/dataTables.min.css', array(), $MooWoodle->version);
// from heading
$from_heading = apply_filters(
    'moowoodle_courses_heading',
    array(
        '<label hidden><span>' . __("Select All", 'moowoodle') . '</span></label><input type="checkbox" class="bulk-action-select-all" name="bulk_action_seclect_all">',
        __("Course", 'moowoodle'),
        __("Short Name", 'moowoodle'),
        __("Product", 'moowoodle'),
        __("Category", 'moowoodle'),
        __("Enrolled", 'moowoodle'),
        __("Start Date - End Data", 'moowoodle'),
        __("Actions", 'moowoodle') . $pro_sticker,
    )
);
$args = array(
    'from_heading' => $from_heading,
	'non_filterable_column' => array(
		__('Actions', 'moowoodle'),
		__('Enrolled', 'moowoodle'),
		__('Date', 'moowoodle'),
        __('Select All', 'moowoodle'),
	),
    'non_sortable_column' => array(
        __('Actions', 'moowoodle'),
        __('Select All', 'moowoodle'),
    ),
    // 'lang' => array(
    // 	'select_bulk_action' => '',
    // 	'pro_popup_class' => '',
    // 	'bulk_actions' => '',
    // 	'sync_course' => '',
    // 	'create_product' => '',
    // 	'update_product' => '',
    // 	'apply' => '',
    // 	'pro_sticker' => '',
    // 	'Search_Course' => '',
    // )
);
wp_localize_script('moowoodle_all_course_tables', 'table_args', $args);
if ($_SERVER['REQUEST_METHOD'] === 'POST')
            if(isset($_POST['mw_bulk_apply_btn']))
            if(isset($_POST['bulk_action_seclect_course_id'])){
                print_r($_POST['bulk_action_seclect_course_id']);die;
            }
?>
<script src="<?php echo $MooWoodle->plugin_url . 'assets/admin/js/dataTables.min.js'; ?>"></script>
<script src="<?php echo $MooWoodle->plugin_url . 'assets/admin/js/moment.min.js'; ?>"></script>
<script src="<?php echo $MooWoodle->plugin_url . 'assets/admin/js/dataTables.dateTime.min.js'; ?>"></script>
<div class="mw-input-content">
    <div class='mw-course-table-content '>
        <div class="moowoodle-table-fuilter"></div>
        <div class="search-bulk-action">
            <div class="<?php echo  $pro_popup_overlay; ?> mw-filter-bulk">
                <label for="bulk-action-selector-top" class="screen-reader-text">
                    <?php echo __('Select bulk action', 'moowoodle'); ?>
                </label>
                <select name="action" id="bulk-action-selector-top">
                    <option value="-1"><?php echo __('Bulk Actions', 'moowoodle') ?></option>
                    <option value="sync_courses"><?php echo __('Sync Course', 'moowoodle') ?></option>
                    <option value="sync_create_product"><?php echo __('Create Product', 'moowoodle') ?></option>
                    <option value="sync_update_product"><?php echo __('Update Product', 'moowoodle') ?></option>
                </select>
                <button class="button-secondary bulk-action-select-apply" name="bulk-action-apply" type="button"><?php echo __('Apply', 'moowoodle') ?></button>
                <?php echo $pro_sticker ?>
            </div>
            <div class="mw-header-search-section">
                <label class="moowoodle-course-search">
                    <i class="dashicons dashicons-search"></i>
                </label><input type="search" class="moowoodle-search-input" placeholder="<?php echo __("Search Course", 'moowoodle'); ?>" aria-controls="moowoodle_table">
            </div>
        </div>
        </div>
        <table id="moowoodle_table" class="table table-bordered responsive-table moodle-linked-courses widefat">
            <thead>
                <tr>
                    <?php
foreach ($from_heading as $key_heading => $value_heading) {
	?>
                        <th>
                            <?php echo $value_heading; ?>
                        </th>
                    <?php
}
?>
                </tr>
            </thead>
            <tbody>
                <?php
$courses = get_posts(array('post_type' => 'course', 'numberposts' => -1, 'post_status' => 'publish'));
if (!empty($courses)) {
	foreach ($courses as $course) {
		$id = get_post_meta($course->ID, 'moodle_course_id', true);
		$course_short_name = get_post_meta($course->ID, '_course_short_name', true);
		$product = get_posts(array('post_type' => 'product', 'numberposts' => -1, 'post_status' => 'publish', 'name' => $course_short_name));
		$course_startdate = get_post_meta($course->ID, '_course_startdate', true);
		$course_enddate = get_post_meta($course->ID, '_course_enddate', true);
		$course_name = $course->post_title;
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
		if (!empty($course_path)) {
			$course_path = implode(' / ', $course_path);
		}
		$term = get_term_by('id', $term_id, 'course_cat');
		$catagory_name = '';
		if (!empty($term)) {
			$catagory_name = '<a href="' . esc_url(admin_url('edit.php?course_cat=' . $term->slug . '&post_type=course')) . '">' . esc_html($course_path) . '</a>';
		}
		$moodle_url = '';
		if ($id) {
			$moodle_url = '<a href="' . esc_url($MooWoodle->options_general_settings["moodle_url"]) . 'course/edit.php?id=' . $id . '" target="_blank">' . esc_html($course_name) . '</a>';
		}
		$product_name = '';
		if ($product) {
			$product_name = '<a href="' . esc_url(admin_url() . 'post.php?post=' . $product[0]->ID . '&action=edit') . '">' . esc_html($product[0]->post_title) . '</a>';
		}
		$enroled_user = '';
		$customer_orders = get_posts(array(
			'numberposts' => -1,
			'orderby' => 'date',
			'order' => 'DESC',
			'post_type' => 'shop_order',
			'post_status' => 'wc-completed',
		));
		$count_enrolment = 0;
		foreach ($customer_orders as $customer_order) {
			$order = wc_get_order($customer_order->ID);
			foreach ($order->get_items() as $enrolment) {
				$linked_course_id = get_post_meta($enrolment->get_product_id(), 'linked_course_id', true);
				if ($linked_course_id == $id) {
					$count_enrolment++;
				}

			}
		}
		$enroled_user = $count_enrolment;
		$date = wp_date('M j, Y', $course_startdate);
		if ($course_enddate) {
			$date .= ' - ' . wp_date('M j, Y  ', $course_enddate);
		}
		$actions = '';
		$actions .= '<div class="moowoodle-course-actions ' . $pro_popup_overlay . '"><input type="hidden" name="course_id" value=" ' . $course->ID . '"/><button type="button" name="sync_courses" class="sync-single-course button-primary" title="' . esc_attr('Sync Couse Data', 'moowoodle') . '" ' . ' ><i class="dashicons dashicons-update"></i></button>';
		if ($product) {
			$actions .= '
                        <button type="button" name="sync_update_product" class="update-existed-single-product button-secondary" title="' . esc_attr('Sync Course Data & Update Product', 'moowoodle') . '" ' . '><i class="dashicons dashicons-admin-links"></i></button>
                    </div>';
		} else {
			$actions .= '
                        <button type="button" name="sync_create_product" class="create-single-product button-secondary" title="' . esc_attr('Create Product', 'moowoodle') . '" ' . '><i class="dashicons dashicons-cloud-upload"></i></button>
                    </div>';
		}
		$table_body = '';
		?>
                        <tr>
                            <td>
                                <input type="checkbox" class="bulk-action-checkbox" name="bulk_action_seclect_course_id[]" value="<?php echo $course->ID;?>">
                            </td>
                            <td>
                                <?php echo $moodle_url; ?>
                            </td>
                            <td>
                                <?php echo esc_html($course_short_name); ?>
                            </td>
                            <td>
                                <?php echo $product_name; ?>
                            </td>
                            <td>
                                <?php echo $catagory_name; ?>
                            </td>
                            <td>
                                <?php echo esc_html($enroled_user); ?>
                            </td>
                            <td>
                                <?php echo esc_html($date); ?>
                            </td>
                            <td>
                                <?php echo $actions; ?>
                            </td>
                            <?php echo apply_filters('moowoodle_courses_body', $table_body, $course, $product); ?>
                        </tr>
                <?php
}
}
?>
            </tbody>
        </table>
    <br>
    <p class="mw-sync-paragraph">
        <?php echo esc_html_e("Cannot find your course in this list?", 'moowoodle'); ?>
        <a href="<?php echo esc_url(get_site_url() . '/wp-admin/admin.php?page=moowoodle-synchronization'); ?>"><?php esc_html_e('Synchronize Moodle Courses from here.', 'moowoodle');?></a>
    </p>
</div>