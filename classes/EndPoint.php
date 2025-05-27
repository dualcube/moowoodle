<?php

namespace MooWoodle;

class EndPoint {
	private $endpoint = 'my-courses';

	public function __construct() {
		add_action( 'init', [ $this, 'register_endpoint' ] );
		add_filter( 'woocommerce_account_menu_items', [ $this, 'register_my_courses_tab' ] );
		add_action( 'woocommerce_account_' . $this->endpoint . '_endpoint', [ $this, 'render_my_courses_tab_content' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	public function register_endpoint() {
		add_rewrite_endpoint( $this->endpoint, EP_ROOT | EP_PAGES );
		flush_rewrite_rules();
	}

	/**
	 * Add "My Courses" menu item to WooCommerce My Account menu.
	 *
	 * @param array $menu The existing account menu items.
	 * @return array Modified menu items with My Courses.
	 */
	public function register_my_courses_tab( $menu ) {
		$position = (int) MooWoodle()->setting->get_setting( 'my_courses_priority' ) ?: 0;
	
		return array_merge(
			array_slice( $menu, 0, $position + 1, true ),
			[ $this->endpoint => __( 'My Courses', 'moowoodle' ) ],
			array_slice( $menu, $position + 1, null, true )
		);
	}
	
	/**
	 * Render the MooWoodle course section on the customer's My Account page.
	 *
	 * This outputs a container div which can be used by JavaScript (e.g., React)
	 * to dynamically load and display the user's enrolled courses.
	 *
	 * @return void
	 */
	public function render_my_courses_tab_content() {
		if ( is_account_page() ) {
			echo '<div id="moowoodle-my-course"></div>';
		}
	}
	

	public function enqueue_assets() {
		if ( is_account_page() ) {
			FrontendScripts::load_scripts();
			FrontendScripts::enqueue_script( 'moowoodle-my-courses-script' );
			FrontendScripts::localize_scripts( 'moowoodle-my-courses-script' );
		}
	}
}
