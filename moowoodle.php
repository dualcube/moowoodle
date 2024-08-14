<?php
/**
 * Plugin Name: MooWoodle
 * Plugin URI: https://dualcube.com/
 * Description: The MooWoodle plugin is an extention of WooCommerce that acts as a bridge between WordPress/Woocommerce and Moodle.
 * Author: DualCube
 * Version: 3.1.7
 * Author URI: https://dualcube.com/
 * Requires at least: 5.0
 * Tested up to: 6.4.3
 * WC requires at least: 8.2.2
 * WC tested up to: 8.5.2
 *
 * Text Domain: moowoodle
 * Domain Path: /languages/
 */
if (!class_exists('MooWoodle_Dependencies')) {
	require_once trailingslashit(dirname(__FILE__)) . 'includes/class-moowoodle-dependencies.php';
}

require_once trailingslashit(dirname(__FILE__)) . 'includes/moowoodle-core-functions.php';
require_once trailingslashit(dirname(__FILE__)) . 'moowoodle-config.php';
if (!defined('ABSPATH')) {
	exit;
}
// Exit if accessed directly
if (!defined('MOOWOODLE_PLUGIN_TOKEN')) {
	exit;
}

if (!MooWoodle_Dependencies::woocommerce_active_check()) {
	add_action('admin_notices', 'moowoodle_alert_notice');
}

/**
 * Plugin page links
 */
function moowoodle_plugin_links($links) {
	$plugin_links = array(
		'<a href="' . admin_url('admin.php?page=moowoodle-settings') . '">' . __('Settings', 'moowoodle') . '</a>',
		'<a href="' . MOOWOODLE_SUPPORT_URL . '">' . __('Support', 'moowoodle') . '</a>',
	);
	$links = array_merge($plugin_links, $links);
	if (apply_filters('moowoodle_upgrage_to_pro', true)) {
		$links[] = '<a href="' . MOOWOODLE_PRO_SHOP_URL . '" target="_blank" style="font-weight: 700;background: linear-gradient(110deg, rgb(63, 20, 115) 0%, 25%, rgb(175 59 116) 50%, 75%, rgb(219 75 84) 100%);-webkit-background-clip: text;-webkit-text-fill-color: transparent;">' . __('Upgrade to Pro', 'moowoodle') . '</a>';
	}
	return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'moowoodle_plugin_links');
if (!defined('MOOWOODLE_PLUGIN_BASENAME')) {
	define('MOOWOODLE_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

require_once trailingslashit(dirname(__FILE__)) . 'includes/class-moowoodle-install.php';
register_activation_hook(__FILE__, array('MooWoodle_Install', 'init'));
if (!is_admin()) {
	if (session_status() == PHP_SESSION_NONE) {
		session_start(
			array('read_and_close' => true)
		);
	}
}
if (!class_exists('MooWoodle') && MooWoodle_Dependencies::woocommerce_active_check()) {
	require_once 'classes/class-moowoodle.php';
	global $MooWoodle;
	$MooWoodle = new MooWoodle(__FILE__);
	$GLOBALS['MooWoodle'] = $MooWoodle;
} elseif (filter_input(INPUT_POST, 'page', FILTER_DEFAULT) == 'moowoodle-settings') {
	?>
	<div style="text-align: center; padding: 20px; height: 100%">
        <h2><?php echo __('Warning: Activate WooCommerce and Verify Moowoodle Files', 'moowoodle'); ?></h2>
        <p><?php echo __('To access Moowoodle, please follow these steps:', 'moowoodle'); ?></p>
        <ol style="text-align: left; margin-left: 40px;">
            <li><?php echo __('Activate WooCommerce on your <a href="', 'moowoodle') . home_url() . '/wp-admin/plugins.php'; ?>"><?php echo __('website', 'moowoodle'); ?></a><?php echo __(', if it\'s not already activated.', 'moowoodle'); ?></li>
            <li><?php echo __('Ensure that all Moowoodle files are present in your WordPress installation.', 'moowoodle'); ?></li>
            <li><?php echo __('If you suspect any missing files, consider reinstalling Moowoodle to resolve the issue.', 'moowoodle'); ?></li>
        </ol>
        <p><?php echo __('After completing these steps, refresh this page to proceed.', 'moowoodle'); ?></p>
    </div>
    <?php
}
add_action ( 'before_woocommerce_init', function () {  
    if ( class_exists ( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility ( 'custom_order_tables', WP_CONTENT_DIR.'/plugins/moowoodle/moowoodle.php', true );  
    } 
});
