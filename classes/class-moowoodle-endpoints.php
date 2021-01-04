<?php

class MooWoodle_Endpoints {
	
	function __construct() {		
		add_action( 'init', 'add_my_courses_endpoint' );
		add_filter ( 'woocommerce_account_menu_items', 'my_courses_page_link' );
		add_action( 'woocommerce_account_my-courses_endpoint', 'woocommerce_account_my_courses_endpoint' );
	}
}

//Adds my-courses endpoints
function add_my_courses_endpoint() {
  add_rewrite_endpoint( 'my-courses', EP_ROOT | EP_PAGES );
  flush_rewrite_rules();
}

//Adds the menu item to my-account WooCommerce menu 
function my_courses_page_link( $menu_links ){
 
  global $MooWoodle;
  $new = array( 'my-courses' => 'My Courses' );
  $display_settings = $MooWoodle->options_display_settings;
  if ( isset( $display_settings[ 'my_courses_priority' ] ) ) {
    $priority_below = $display_settings[ 'my_courses_priority' ];
  } else {
    $priority_below = 0;
  }

  $menu_links = array_slice( $menu_links, 0, $priority_below + 1, true ) 
    + $new 
    + array_slice( $menu_links, $priority_below + 1, NULL, true ); 
  
  return $menu_links; 
}

function woocommerce_account_my_courses_endpoint() {
  global $current_user;
  $i = 0;
  $customer = get_current_user_id();
  $customer_orders = get_posts( array(
    'numberposts' => -1,
    'meta_key' => '_customer_user',
    'orderby' => 'date',
    'order' => 'DESC',
    'meta_value' => $customer,
    'post_type' => 'shop_order',
    'post_status' => 'any'
  ) );
  
  if ( count( $customer_orders ) > 0 ) {
        ?> <p> 
        <div class="instraction-tri">
          <p><?php _e( 'Use this username and password for first time login to your moodle site.', 'moowoodle') ?></p>
          <p><?php _e( 'Username : ', 'moowoodle') . _e( $current_user->user_login ) ?></p>
          <p><?php _e( 'Password : 1Admin@23', 'moowoodle') ?></p>
          <p><?php _e( 'To enroll and access your course please click on the course link given below:', 'moowoodle') ?></p>
        </div>
      </p> 
      <?php
      foreach ( $customer_orders as $customer_order ) {
        $order = wc_get_order( $customer_order->ID );
        foreach ( $order->get_items() as $enrolment ) {
          $course_id = get_post_meta( $enrolment->get_product_id(), 'moodle_course_id', true );
          
          $enrollment_data = array();
          $enrollment_data[ 'course_name' ] =  get_the_title( $enrolment->get_product_id() );
          $course_id_meta = get_post_meta( $enrolment->get_product_id() , 'moodle_course_id', true );

          $linked_course_id_meta = get_post_meta( $enrolment->get_product_id(), 'linked_course_id', true );
          $linked_course_id = ! empty( $linked_course_id_meta ) ? $linked_course_id_meta : '';
          
          $enrollment_list[] = get_moowoodle_course_url( $linked_course_id, $enrollment_data[ 'course_name' ] );
          if ( $order->get_status() == 'completed' ) {
            ?> <p> <?php echo '<button type="button" class="button-tri">' . $enrollment_list[ $i ] . '</button> <br>'; ?> </p> <?php 
          } else {
            ?> <p> <?php echo '<div class="payment-tri">' . esc_html_e("You can not access your course : ", 'moowoodle') . esc_html( $enrollment_data[ 'course_name' ] ) . esc_html_e( " ( Payment ", 'moowoodle' ) . $order->get_status() . ' ) </div>'; ?> </p> <?php 
          }
          $i++;

          $enrollment_data_arr[] = $enrollment_data;
        }
      }
    } else {
      ?>
      <h3><?php _e( 'You have no Course.', 'moowoodle') ?></h3>
      <h3><?php _e( 'Kindly purchase a Course and come back here to see your course.', 'moowoodle') ?></h3>
      <?php
    }
}