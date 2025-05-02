<?php

namespace MooWoodle;

class EndPoint {
	private $endpoint = 'my-courses';

	public function __construct() {
		// Register custom endpoint and hooks.
		add_action( 'init', [ $this, 'register_endpoint' ] );
		add_filter( 'woocommerce_account_menu_items', [ $this, 'add_menu_item' ] );
		add_action( 'woocommerce_account_' . $this->endpoint . '_endpoint', [ $this, 'render_view' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Registers the custom WooCommerce endpoint.
	 */
	public function register_endpoint() {
		add_rewrite_endpoint( $this->endpoint, EP_ROOT | EP_PAGES );
		flush_rewrite_rules();
	}

	/**
	 * Adds 'My Courses' link to WooCommerce My Account menu.
	 */
	public function add_menu_item( $menu ) {
		$priority = (int) MooWoodle()->setting->get_setting( 'my_courses_priority' ) ?: 0;

		return array_merge(
			array_slice( $menu, 0, $priority + 1, true ),
			[ $this->endpoint => __( 'My Courses', 'moowoodle' ) ],
			array_slice( $menu, $priority + 1, null, true )
		);
	}

	/**
	 * Renders the endpoint content on the My Account page.
	 */
	public function render_view() {
		if ( is_account_page() ) {
			echo '<div id="moowoodle-my-course"></div>';
		}
	}

	/**
	 * Enqueues frontend scripts only on the account page.
	 */
	public function enqueue_assets() {
		if ( is_account_page() ) {
			FrontendScripts::load_scripts();
			FrontendScripts::enqueue_script( 'moowoodle-my-courses-script' );
			FrontendScripts::localize_scripts( 'moowoodle-my-courses-script' );
		}
	}
}
