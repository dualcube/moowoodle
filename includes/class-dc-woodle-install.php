<?php
class DC_Woodle_Install {
	
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
		if ( get_option( 'woodle_version' ) != DC_WOODLE_PLUGIN_VERSION || get_option( 'woodle_db_version' ) != DC_WOODLE_DB_VERSION ) {
			self::install();
			do_action( 'woodle_updated' );
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
		self::create_tables();
	}
	
	/**
	 * Update options.
	 *
	 * @access public
	 * @return void
	 */
	public static function create_options() {
		update_option( 'woodle_version', DC_WOODLE_PLUGIN_VERSION );
		update_option( 'woodle_db_version', DC_WOODLE_DB_VERSION );
		
		update_option( 'woocommerce_registration_generate_username', 'no' );
		update_option( 'woocommerce_enable_guest_checkout', 'no' );
	}
	
	/**
	 * Create required database tables.
	 *
	 * @access public
	 * @return void
	 */
	public static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		dbDelta( self::get_schema() );
	}
	
	/**
	 * Initialize installation
	 *
	 * @access private
	 * @return string
	 */
	private static function get_schema() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		return "CREATE TABLE {$wpdb->prefix}woodle_termmeta (
							meta_id bigint(20) NOT NULL auto_increment,
							woodle_term_id bigint(20) NOT NULL,
							meta_key varchar(255) NULL,
							meta_value longtext NULL,
							PRIMARY KEY  (meta_id),
							KEY woodle_term_id (woodle_term_id),
							KEY meta_key (meta_key)
						) $collate;";
	}
}