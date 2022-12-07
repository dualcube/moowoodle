<?php

class MooWoodle_Endpoints {
	
  public $endpoint_slug;
  public $table_heading;
  function __construct() {	
    $this->endpoint_slug = 'my-courses';

    $this->table_heading = array( __( "Course Name", 'moowoodle' ),
                                  __( "Moodle User Name", 'moowoodle' ),
                                  __( "Password (First Time use Only)", 'moowoodle' ),
                                  __( "Enrolment Date", 'moowoodle' ),
                                  __( "Course Link", 'moowoodle' )    
                                );


    $this->add_my_courses_endpoint();
  	add_filter ( 'woocommerce_account_menu_items', array($this, 'my_courses_page_link' ) );
		add_action( 'woocommerce_account_' . $this->endpoint_slug . '_endpoint', array($this, 'woocommerce_account_my_courses_endpoint' ) );
    add_action( 'wp_enqueue_scripts', array( &$this, 'frontend_styles' ) );
    
	}

  //Adds my-courses endpoints
  function add_my_courses_endpoint() {
    add_rewrite_endpoint( $this->endpoint_slug, EP_ROOT | EP_PAGES );
    flush_rewrite_rules();
  }

  //Adds the menu item to my-account WooCommerce menu 
  function my_courses_page_link( $menu_links ){ 
    global $MooWoodle;
    $name = __( 'My Courses', 'moowoodle' );
    $new = array( $this->endpoint_slug => $name );
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
    $customer = wp_get_current_user();
    $customer_orders = get_posts( array(
      'numberposts' => -1,
      'meta_key' => '_customer_user',
      'orderby' => 'date',
      'order' => 'DESC',
      'meta_value' => $customer->ID,
      'post_type' => 'shop_order',
      'post_status' => 'wc-completed'
    ) );
    $pwd = get_user_meta( $customer->ID , 'moowoodle_moodle_user_pwd', true );
    
    if ( count( $customer_orders ) > 0 ) {
      ?> 
      <p> 
        <div class="auto">
          <table class="table table-bordered responsive-table moodle-linked-courses widefat">
            <thead>
              <tr>
                <?php
                foreach ( $this->table_heading as $key_heading => $value_heading ) {
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
              foreach ( $customer_orders as $customer_order ) {
                $order = wc_get_order( $customer_order->ID );
                foreach ( $order->get_items() as $enrolment ) {
                  $linked_course_id = get_post_meta( $enrolment->get_product_id(), 'linked_course_id', true );
                  $course_link = get_moowoodle_course_url( $linked_course_id, 'View' );
                  $enrolment_date = get_post_meta( $order->get_id(), 'moodle_user_enrolment_date', true );
                  $product = wc_get_product($enrolment->get_product_id());
                  if ($product) continue;
                  ?>
                  <tr>
                    <td>
                      <?php _e( get_the_title( $enrolment->get_product_id() ) ) ?>
                    </td>
                    <td>
                      <?php _e( $customer->user_login ); ?> 
                    </td>
                    <td>
                      <?php _e( $pwd );?>
                    </td>
                    <td>
                      <?php
                        if ( ! empty( $enrolment_date ) ) {
                          _e( get_date_from_gmt( date( 'Y-m-d H:i:s', $enrolment_date ) ) );
                        }                      
                      ?>
                    </td>
                    <td>
                      <?php 
                      echo '<button type="button" class="button-tri">' . $course_link . '<i class="fas fa-eye"></i>' . '</button>';
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
        <h3><?php _e( 'You have no Course.', 'moowoodle') ?></h3>
        <h3><?php _e( 'Kindly purchase a Course and come back here to see your course.', 'moowoodle') ?></h3>
        <?php
      }
  }

  public function frontend_styles() {
      global $MooWoodle;
      $suffix = defined( 'MOOWOODLE_SCRIPT_DEBUG' ) && MOOWOODLE_SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_style( 'frontend_css',  $MooWoodle->plugin_url . 'assets/frontend/css/frontend' . $suffix . '.css', array(), $MooWoodle->version );
  }
}