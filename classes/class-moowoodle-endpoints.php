<?php
class MooWoodle_Endpoints {
	public $endpoint_slug;
	public $table_heading;
	function __construct() {
		$this->endpoint_slug = 'my-courses';
		$this->table_heading = array(
			__("Course Name", 'moowoodle'),
			__("Moodle User Name", 'moowoodle'),
			__("Password (First Time use Only)", 'moowoodle'),
			__("Enrolment Date", 'moowoodle'),
			__("Course Link", 'moowoodle'),
		);
		$this->add_my_courses_endpoint();
		add_filter('woocommerce_account_menu_items', array($this, 'my_courses_page_link'));
		add_action('woocommerce_account_' . $this->endpoint_slug . '_endpoint', array($this, 'woocommerce_account_my_courses_endpoint'));
		add_action('wp_enqueue_scripts', array(&$this, 'frontend_styles'));
	}
	//Adds my-courses endpoints
	function add_my_courses_endpoint() {
		add_rewrite_endpoint($this->endpoint_slug, EP_ROOT | EP_PAGES);
		flush_rewrite_rules();
	}
	//Adds the menu item to my-account WooCommerce menu
	function my_courses_page_link($menu_links) {
		global $MooWoodle;
		$name = __('My Courses', 'moowoodle');
		$new = array($this->endpoint_slug => $name);
		$display_settings = $MooWoodle->options_display_settings;
		if (isset($display_settings['my_courses_priority'])) {
			$priority_below = $display_settings['my_courses_priority'];
		} else {
			$priority_below = 0;
		}
		$menu_links = array_slice($menu_links, 0, $priority_below + 1, true)
		 + $new
		 + array_slice($menu_links, $priority_below + 1, NULL, true);
		return $menu_links;
	}
	function woocommerce_account_my_courses_endpoint() {
		global $MooWoodle;
		$customer = wp_get_current_user();
		$args = array(
			'numberposts' => -1,
			'orderby' => 'date',
			'order' => 'DESC',
			'post_type' => 'shop_order',
			'post_status' => 'wc-completed',
			'post_author' => $customer->ID,
			// 'meta_query' => array(
            //     array(
            //         'key' => '_customer_user',
            //         'value' => $customer->ID
            //     ),
            // ),
		);
		if($MooWoodle->hpos_is_enabled){
			// $args = wp_parse_args($args, array('customer_id' => $customer->ID));
			// unset($args['meta_query']);
			$query = new WC_Order_Query( apply_filters( 'moowoodle_my_courses_endpoint_get_orders_query_args', $args ) );
			$customer_orders = $query->get_orders();
		}else{
			$args = wp_parse_args($args, array('fields' => 'ids'));
			$order_ids = get_posts( apply_filters( 'moowoodle_my_courses_endpoint_get_orders_query_args', $args ) );
			foreach($order_ids as $id){
				$customer_orders[] = wc_get_order($id);
			}
		}
		$pwd = get_user_meta($customer->ID, 'moowoodle_moodle_user_pwd', true);
		if (count($customer_orders) > 0) {
			?>
      <p>
      <div class="auto">
        <table class="table table-bordered responsive-table moodle-linked-courses widefat">
          <thead>
            <tr>
              <?php
foreach ($this->table_heading as $key_heading => $value_heading) {
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
foreach ($customer_orders as $order) {
				$unenrolled_course = $order->get_meta( '_course_unenroled', true);
				$unenrolled_courses[] = null;
				if ($unenrolled_course != null) {
					$unenrolled_courses = str_contains($unenrolled_course, ',') ? explode(',', $unenrolled_course) : array($unenrolled_course);
				}
				foreach ($order->get_items() as $enrolment) {
					$moodle_course_id = get_post_meta($enrolment->get_product_id(), 'moodle_course_id', true);
					$course_link = get_moowoodle_course_url($moodle_course_id, 'View');
					$enrolment_date = $order->get_meta( 'moodle_user_enrolment_date', true);
					$linked_course_id = get_post_meta($enrolment->get_product_id(), 'linked_course_id', true);
					if (!$linked_course_id || in_array($moodle_course_id, $unenrolled_courses)) {
						continue;
					}
					
					?>
                <tr>
                  <td>
                    <?php _e(get_the_title($enrolment->get_product_id()))?>
                  </td>
                  <td>
                    <?php _e($customer->user_login);?>
                  </td>
                  <td>
                    <?php _e($pwd);?>
                  </td>
                  <td>
                    <?php
if (!empty($enrolment_date)) {
						_e(get_date_from_gmt(date('M j, Y-H:i', $enrolment_date)));
					}
					?>
                  </td>
                  <td>
                    <?php
echo '<button type="button" class="button-tri">' . apply_filters('moodle_course_view_url', $course_link, $moodle_course_id) . '<i class="fas fa-eye"></i>' . '</button>';
					?>
                  </td>
                </tr>
            <?php
}
			}
			?>
          </tbody>
        </table>
      </div>
      </p>
    <?php
} else {
			?>
      <h3><?php _e('You have no Course.', 'moowoodle')?></h3>
      <h3><?php _e('Kindly purchase a Course and come back here to see your course.', 'moowoodle')?></h3>
<?php
}
	}
	public function frontend_styles() {
		global $MooWoodle;
		$suffix = defined('MOOWOODLE_SCRIPT_DEBUG') && MOOWOODLE_SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style('frontend_css', $MooWoodle->plugin_url . 'assets/frontend/css/frontend' . $suffix . '.css', array(), $MooWoodle->version);
	}
}
