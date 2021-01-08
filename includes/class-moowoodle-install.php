<?php
class MooWoodle_Install {
	
	/**
	 * Initialize installation.
	 *
	 * @access public
	 * @return void
	 */
	public static function init() {
		self::check_version();
	}
	
	/**
	 * Check plugin and database version.
	 *
	 * @access public
	 * @return void
	 */
	public static function check_version() {
		if ( get_option( 'woodle_version' ) != MOOWOODLE_PLUGIN_VERSION ) {
			self::install();
			do_action( 'moowoodle_updated' );
		}
	}
	
	/**
	 * Install plugin.
	 *
	 * @access public
	 * @return void
	 */
	public static function install() {
		self::create_options();
		// self::create_tables();
	}
	
	/**
	 * Update options.
	 *
	 * @access public
	 * @return void
	 */
	public static function create_options() {
		add_option( 'moowoodle_version', MOOWOODLE_PLUGIN_VERSION );
		
		update_option( 'woocommerce_registration_generate_username', 'no' );
		update_option( 'woocommerce_enable_guest_checkout', 'no' );
	}	
}