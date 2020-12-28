<?php
/*
Plugin Name: MooWoodle
Plugin URI: http://techmonastic.com/
Description: The MooWoodle plugin is an extention of WooCommerce that acts as a bridge between WordPress/Woocommerce and Moodle.
Author: Down Town
Version: 2.4
Tested up to: 5.4.2
Author URI: http://techmonastic.com/
Text Domain: moowoodle
Domain Path: /languages/
*/

if ( ! class_exists( 'MooWoodle_Dependencies' ) )
	require_once trailingslashit( dirname( __FILE__ ) ) . 'includes/class-moowoodle-dependencies.php';

require_once trailingslashit( dirname( __FILE__ ) ) . 'includes/moowoodle-core-functions.php';
require_once trailingslashit( dirname( __FILE__ ) ) . 'moowoodle-config.php';
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! defined( 'MOOWOODLE_PLUGIN_TOKEN' ) ) exit;
if ( ! defined( 'MOOWOODLE_TEXT_DOMAIN' ) ) exit;

if ( ! MooWoodle_Dependencies::woocommerce_active_check() )
  add_action( 'admin_notices', 'moowoodle_alert_notice' );

/**
* Plugin page links
*/
function moowoodle_plugin_links( $links ) {	
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=moowoodle-settings' ) . '">' . __( 'Settings', MOOWOODLE_TEXT_DOMAIN ) . '</a>',
		'<a href="https://wordpress.org/support/plugin/moowoodle/">' . __( 'Support', MOOWOODLE_TEXT_DOMAIN ) . '</a>',			
	);	
	$links = array_merge( $plugin_links, $links );
	if ( apply_filters( 'moowoodle_free_active', true ) ) {
        $links[] = '<a href="https://dualcube.com/shop/" target="_blank">' . __( 'Upgrade to Pro', MOOWOODLE_TEXT_DOMAIN ) . '</a>';
    }
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'moowoodle_plugin_links' );

// Migration at activation hook
register_activation_hook( __FILE__, 'moowoodle_option_migration_2_to_3' );
// Update time migration
add_action( 'upgrader_process_complete', 'moowoodle_option_migration_2_to_3' );

if ( ! defined( 'MOOWOODLE_PLUGIN_BASENAME' ) ) 
	define( 'MOOWOODLE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once trailingslashit( dirname( __FILE__ ) ) . 'includes/class-moowoodle-install.php';
register_activation_hook( __FILE__, array( 'MooWoodle_Install', 'init' ) );

if ( session_status() == PHP_SESSION_NONE ) {
	session_start(
		array( 'read_and_close' => true )
	);
}

if ( ! class_exists( 'MooWoodle' ) ) {
	require_once( 'classes/class-moowoodle.php' );
	global $MooWoodle;
	
	$MooWoodle = new MooWoodle( __FILE__ );
	$GLOBALS[ 'MooWoodle' ] = $MooWoodle;
}
