<?php
global $MooWoodle;
$pro_sticker = '';
if ($MooWoodle->moowoodle_pro_adv) {
    $pro_sticker = '<span class="mw-pro-tag">Pro</span>';
}
// Include the JavaScript above in your plugin
wp_enqueue_script('moowoodle_all_course_tables', plugins_url('../../assets/admin/js/moowoodle-all-course-table.js', __FILE__), array('jquery'), '', true);
wp_enqueue_style('woocommerce_course_css', $MooWoodle->plugin_url . 'assets/admin/css/dataTables.min.css', array(), $MooWoodle->version);
$args = array(
    'non_filterable_column' => array(
        __('Actions', MOOWOODLE_TEXT_DOMAIN),
        __('Enrolled', MOOWOODLE_TEXT_DOMAIN),
        __('Date', MOOWOODLE_TEXT_DOMAIN)
    ),
    'lang' => array(
        'Search_Course' => __("Search Course", MOOWOODLE_TEXT_DOMAIN),
    )
);
wp_localize_script('moowoodle_all_course_tables', 'table_args', $args);
// from heading
$from_heading = apply_filters(
    'moowoodle_courses_heading',
    array(
        __("Course", MOOWOODLE_TEXT_DOMAIN),
        __("Short Name", MOOWOODLE_TEXT_DOMAIN),
        __("Product", MOOWOODLE_TEXT_DOMAIN),
        __("Category", MOOWOODLE_TEXT_DOMAIN),
        __("Enrolled", MOOWOODLE_TEXT_DOMAIN),
        __("Date", MOOWOODLE_TEXT_DOMAIN),
        __("Actions", 'moowoodlepro') . $pro_sticker,
    )
);
?>
<script src="<?php echo $MooWoodle->plugin_url . 'assets/admin/js/dataTables.min.js'; ?>"></script>
<script src="<?php echo $MooWoodle->plugin_url . 'assets/admin/js/moment.min.js'; ?>"></script>
<script src="<?php echo $MooWoodle->plugin_url . 'assets/admin/js/dataTables.dateTime.min.js'; ?>"></script>
<div class="mw-form-group">
    <div class="mw-input-content">
        <div class='mw-course-table-content '>
            <div class="moowoodle-table-fuilter"></div>
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
                                $moodle_url = '<a href="' . esc_url($MooWoodle->options_general_settings["moodle_url"]) . 'course/edit.php?id=' . $id . '">' . esc_html($course_name) . '</a>';
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
                                'post_status' => 'wc-completed'
                            ));
                            $count_enrolment = 0;
                            foreach ($customer_orders as $customer_order) {
                                $order = wc_get_order($customer_order->ID);
                                foreach ($order->get_items() as $enrolment) {
                                    $linked_course_id = get_post_meta($enrolment->get_product_id(), 'linked_course_id', true);
                                    if ($linked_course_id == $id) $count_enrolment++;
                                }
                            }
                            $enroled_user = $count_enrolment;
                            $date = wp_date('M j, Y', $course_startdate);
                            if ($course_enddate) {
                                $date .= ' - ' . wp_date('M j, Y  ', $course_enddate);
                            }
                            $actions = '';
                            $actions .= '<div class="moowoodle-course-actions ' . apply_filters('moowoodle_pro_sticker', ' mw-pro-popup-overlay ') . '"><form method="post"><input type="hidden" name="course_id" value=" ' . $course->ID . '"/><button name="sync_course" type="submit"  class=" button-primary" title="' . esc_attr('Sync Couse Data', 'moowoodlepro') . '" ><i class="dashicons dashicons-update"></i></button></form>';
                            if ($product) {
                                $actions .= '<form method="post">
                            <input type="hidden" name="course_id" value=" ' . $course->ID . '"/>
                            <button type="submit" name="update_product" class="button-secondary" title="' . esc_attr('Sync Course Data & Update Product', 'moowoodlepro') . '"><i class="dashicons dashicons-admin-links"></i></button>
                        </form>';
                            } else {
                                $actions .= '<form method="post">
                            <input type="hidden" name="course_id" value=" ' . $course->ID . '"/>
                            <button type="submit" name="create_product" class="button-secondary" title="' . esc_attr('Create Product', 'moowoodlepro') . '"><i class="dashicons dashicons-cloud-upload"></i></button>
                        </form>';
                            }
                            $table_body = '';
                    ?>
                            <tr>
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
        </div>
        <br>
        <p class="mw-sync-paragraph">
            <?php echo esc_html_e("Cannot find your course in this list?", MOOWOODLE_TEXT_DOMAIN); ?>
            <a href="<?php echo esc_url(get_site_url() . '/wp-admin/admin.php?page=moowoodle-synchronization'); ?>"><?php esc_html_e('Synchronize Moodle Courses from here.', MOOWOODLE_TEXT_DOMAIN); ?></a>
        </p>
    </div>