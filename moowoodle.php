<?php
/*
Plugin Name: moowoodle
Plugin URI: http://techmonastic.com/
Description: The MooWoodle plugin is an extention of WooCommerce that acts as a bridge between WordPress/Woocommerce and Moodle.
Author: Down Town
Version: 2.4
Tested up to: 5.4.2
Author URI: http://techmonastic.com/
*/

if ( ! class_exists( 'MooWoodle_Dependencies' ) )
	require_once trailingslashit(dirname(__FILE__)).'includes/class-moowoodle-dependencies.php';

require_once trailingslashit(dirname(__FILE__)).'includes/moowoodle-core-functions.php';
require_once trailingslashit(dirname(__FILE__)).'moowoodle-config.php';
if(!defined('ABSPATH')) exit; // Exit if accessed directly
if(!defined('MooWOODLE_PLUGIN_TOKEN')) exit;
if(!defined('MooWOODLE_TEXT_DOMAIN')) exit;

if( ! MooWoodle_Dependencies::wc_active_check() )
  add_action( 'admin_notices', 'woodle_wc_inactive_notice' );

// Migration at activation hook
register_activation_hook(__FILE__, 'moowoodle_option_migration_2_to_3');
// Update time migration
add_action( 'upgrader_process_complete', 'moowoodle_option_migration_2_to_3' );

if(!defined('MooWOODLE_PLUGIN_BASENAME')) 
	define('MooWOODLE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once trailingslashit(dirname(__FILE__)).'includes/class-moowoodle-install.php';
register_activation_hook( __FILE__, array( 'MooWoodle_Install', 'init' ) );

if( session_status() == PHP_SESSION_NONE ) {
	session_start(
		array('read_and_close' => true)
	);
}

if(!class_exists('MooWoodle')) {
	require_once( 'classes/class-moowoodle.php' );
	global $MooWoodle;
	
	$MooWoodle = new MooWoodle( __FILE__ );
	$GLOBALS['MooWoodle'] = $MooWoodle;
}
