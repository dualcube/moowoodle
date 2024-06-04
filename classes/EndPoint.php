<?php

namespace MooWoodle;

class EndPoint {
    private $endpoint_slug = 'my-courses';
	
	public function __construct() {
		Util::log("endpoints");
		// define 'my_course' table enpoint.
		add_action( 'init', [ &$this, 'register_my_courses_endpoint' ] );

		// Resister 'my_course' end point page in WooCommerce 'my_account'.
		add_filter( 'woocommerce_account_menu_items', [ &$this, 'my_courses_page_link' ] );

		// Put endpoint containt. 
		add_action('woocommerce_account_' . $this->endpoint_slug . '_endpoint', [ &$this, 'add_my_courses_endpoint' ] );
    }

	/**
	 *Adds my-courses endpoints table heade
	 * @return void
	 */
	public function register_my_courses_endpoint() {	
		add_rewrite_endpoint( $this->endpoint_slug, EP_ROOT | EP_PAGES );
		flush_rewrite_rules();
	}

	/**
	 * resister my course to my-account WooCommerce menu.
	 * @param array $menu_links 
	 * @return array
	 */
	public function my_courses_page_link( $menu_links ) {

		$menu_name     = __( 'My Courses', 'moowoodle' );
		$menu_link     = [ $this->endpoint_slug => $menu_name ];
		$menu_priority = MooWoodle()->setting->get_setting( 'my_courses_priority' );

		if( ! $menu_priority ) {
			$menu_priority = 0;
		}

		// Merge mycourse menu in priotity position
		$menu_links = array_slice( $menu_links, 0, $menu_priority + 1, true )
					+ $menu_link
		 			+ array_slice( $menu_links, $menu_priority + 1, NULL, true );

		return $menu_links;
	}

	/**
	 * Add meta box panal.
	 * @return void
	 */
	public function add_my_courses_endpoint() {
		$customer = wp_get_current_user();

		// Get all orders of customer
		$customer_orders = wc_get_orders([
			'numberposts' => -1,
			'orderby' 	  => 'date',
			'order'       => 'DESC',
			'type'        => 'shop_order',
			'status'      => 'wc-completed',
			'customer_id' => $customer->ID,
		]);

		// Define the columns for table
		$table_heading = [
			__( "Course Name", 'moowoodle' ),
			__( "Moodle User Name", 'moowoodle' ),
			__( "Enrolment Date", 'moowoodle' ),
			__( "Course Link", 'moowoodle' ),
		];

		$password = get_user_meta( $customer->ID, 'moowoodle_moodle_user_pwd', true );

		// Add extra column for password.
		if ( $password ) {
			array_splice( $table_heading, 2, 0, __( "Password (First Time use Only)", 'moowoodle' ) );
		}
		
		Util::get_template( 'endpoints/my-course.php', [
			'table_heading'   => $table_heading,
			'customer_orders' => $customer_orders,
			'customer' 		  => $customer,
			'password' 		  => $password,
		]);
		
		// load css for admin panel.
		add_action( 'wp_enqueue_scripts', [ $this, 'frontend_styles' ] );
	}

	/**
	 * Add frontend style in mycourse page
	 * @access public
	 * @return void
	 */
	public function frontend_styles() {
		$suffix = defined( 'MOOWOODLE_SCRIPT_DEBUG' ) && MOOWOODLE_SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'frontend_css', MOOWOODLE_PLUGIN_URL . 'assets/frontend/css/frontend' . $suffix . '.css', array(), MOOWOODLE_PLUGIN_VERSION );
	}

}
