<?php

namespace MooWoodle;

class EndPoint {
	private $endpoint = 'my-courses';

	public function __construct() {
		add_action( 'init', [ $this, 'register_endpoint' ] );
		add_filter( 'woocommerce_account_menu_items', [ $this, 'add_menu_item' ] );
		add_action( 'woocommerce_account_' . $this->endpoint . '_endpoint', [ $this, 'render_view' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	public function register_endpoint() {
		add_rewrite_endpoint( $this->endpoint, EP_ROOT | EP_PAGES );
		flush_rewrite_rules();
	}

	public function add_menu_item( $menu ) {
		$position = (int) MooWoodle()->setting->get_setting( 'my_courses_priority' ) ?: 0;

		return array_merge(
			array_slice( $menu, 0, $position + 1, true ),
			[ $this->endpoint => __( 'My Courses', 'moowoodle' ) ],
			array_slice( $menu, $position + 1, null, true )
		);
	}

	public function render_view() {
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
