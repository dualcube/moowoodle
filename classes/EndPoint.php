<?php

namespace MooWoodle;

class EndPoint {
    private $endpoint_slug_course = 'my-courses';
	
	public function __construct() {

		// register enpoints.
		add_action( 'init', [ &$this, 'initialize_custom_endpoints' ] );

		add_filter( 'woocommerce_account_menu_items', [ &$this, 'add_my_courses_menu' ] );

		// Put endpoint containt. 
		add_action('woocommerce_account_' . $this->endpoint_slug_course . '_endpoint', [ &$this, 'load_my_courses_account_endpoint' ] );
    }

	/**
	 *Adds my-courses endpoints table heade
	 * @return void
	 */
	public function initialize_custom_endpoints() {
		add_rewrite_endpoint( $this->endpoint_slug_course, EP_ROOT | EP_PAGES );
		flush_rewrite_rules();
	}

	/**
	 * Register custom menu items to the My Account WooCommerce menu.
	 *
	 * @param array $menu_links Existing WooCommerce account menu links.
	 * @return array Modified menu links.
	 */
	public function add_my_courses_menu( $menu_links ) {
		$slug = $this->endpoint_slug_course;
		$name = __( 'My Courses', 'moowoodle' );
		$priority = MooWoodle()->setting->get_setting( 'my_courses_priority' ) ?: 0;
	
		return array_slice( $menu_links, 0, $priority + 1, true )
			 + [ $slug => $name ]
			 + array_slice( $menu_links, $priority + 1, null, true );
	}
	

	/**
	 * Add meta box panal.
	 * @return void
	 */
	public function load_my_courses_account_endpoint() {
		wp_enqueue_script(
			'moowoodle-myaccount-mycourse-script',
			MOOWOODLE_PLUGIN_URL . 'build/blocks/MyCourses/index.js',
			['wp-element', 'wp-i18n', 'react-jsx-runtime'],
			time(),
			true
		);

		wp_localize_script(
			'moowoodle-myaccount-mycourse-script',
			'appLocalizer',
			[
				'apiUrl'          => untrailingslashit( get_rest_url() ),
				'restUrl'         => 'moowoodle/v1',
				'nonce'           => wp_create_nonce('wp_rest'),
				'moodle_site_url' => MooWoodle()->setting->get_setting( 'moodle_url' ),
			]
		);
		
		echo '<div id="moowoodle-my-course"></div>';
		
	}

}
