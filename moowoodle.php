<?php
/*
Plugin Name: MooWoodle
Plugin URI: http://dualcube.com
Description: The MooWoodle plugin is an extention of WooCommerce that acts as a bridge between WordPress/Woocommerce and Moodle.
Author: Dualcube
Version: 1.0.0
Author URI: http://dualcube.com
*/

if ( ! class_exists( 'DC_Woodle_Dependencies' ) )
	require_once 'includes/class-dc-woodle-dependencies.php';

if( ! DC_Woodle_Dependencies::wc_active_check() )
  add_action( 'admin_notices', 'woodle_wc_inactive_notice' );

require_once 'includes/dc-woodle-core-functions.php';
require_once 'config.php';

if(!defined('ABSPATH')) exit; // Exit if accessed directly
if(!defined('DC_WOODLE_PLUGIN_TOKEN')) exit;
if(!defined('DC_WOODLE_TEXT_DOMAIN')) exit;
if(!defined('DC_WOODLE_PLUGIN_BASENAME')) 
	define('DC_WOODLE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once 'includes/class-dc-woodle-install.php';
register_activation_hook( __FILE__, array( 'DC_Woodle_Install', 'init' ) );

if( session_status() == PHP_SESSION_NONE ) {
	session_start();
}

if(!class_exists('DC_Woodle')) {
	require_once( 'classes/class-dc-woodle.php' );
	global $DC_Woodle;
	
	$DC_Woodle = new DC_Woodle( __FILE__ );
	$GLOBALS['DC_Woodle'] = $DC_Woodle;
}
?>
