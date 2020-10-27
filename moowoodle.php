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

if ( ! class_exists( 'DC_Woodle_Dependencies' ) )
	require_once trailingslashit(dirname(__FILE__)).'includes/class-dc-woodle-dependencies.php';

require_once trailingslashit(dirname(__FILE__)).'includes/dc-woodle-core-functions.php';
require_once trailingslashit(dirname(__FILE__)).'moowoodle-config.php';
if(!defined('ABSPATH')) exit; // Exit if accessed directly
if(!defined('DC_WOODLE_PLUGIN_TOKEN')) exit;
if(!defined('DC_WOODLE_TEXT_DOMAIN')) exit;

if( ! DC_Woodle_Dependencies::wc_active_check() )
  add_action( 'admin_notices', 'woodle_wc_inactive_notice' );


if(!defined('DC_WOODLE_PLUGIN_BASENAME')) 
	define('DC_WOODLE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once trailingslashit(dirname(__FILE__)).'includes/class-dc-woodle-install.php';
register_activation_hook( __FILE__, array( 'DC_Woodle_Install', 'init' ) );

if( session_status() == PHP_SESSION_NONE ) {
	session_start(
		array('read_and_close' => true)
	);
}

if(!class_exists('DC_Woodle')) {
	require_once( 'classes/class-dc-woodle.php' );
	global $DC_Woodle;
	
	$DC_Woodle = new DC_Woodle( __FILE__ );
	$GLOBALS['DC_Woodle'] = $DC_Woodle;
}

echo esc_html__( 'Username : ', 'dc-woodle' ) . esc_html__( $user_details->data->user_login ) . '<br><br>';
