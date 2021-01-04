<?php
/**
 * WC Dependency Checker
 *
 */
class MooWoodle_Dependencies {
	private static $active_plugins;
	
	/**
	 * Initiaze dependency checking.
	 *
	 * @access public
	 * @return void
	 */
	public static function init() {
		self::$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() )
			self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}
	
	/**
	 * Check whether woocommerce plugin is active.
	 *
	 * @access public
	 * @return bool
	 */
	public static function woocommerce_active_check() {
		if ( ! self::$active_plugins ) self::init();
		return in_array( 'woocommerce/woocommerce.php', self::$active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', self::$active_plugins );
		return false;
	}

	public static function  moowoodle_pro_active_check(){
		if ( ! self::$active_plugins ) self::init();

		return in_array( 'moowoodle-pro/moowoodle-pro.php', self::$active_plugins ) || array_key_exists( 'moowoodle-pro/moowoodle-pro.php', self::$active_plugins );
	}
}