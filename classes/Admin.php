<?php

namespace MooWoodle;

class Admin {
	
	public function __construct() {
		// Register submenu for admin menu
		add_action('admin_menu', array($this, 'add_submenu'));
	}

    public static function add_menu() {
        do_action('before_moodle_load');
        if(is_admin()){
            add_menu_page(
                'MooWoodle',
                'MooWoodle',
                'manage_options',
                'moowoodle',
                [ Admin::class, 'create_settings_page' ],
                esc_url(MOOWOODLE_PLUGIN_URL) . 'src/assets/images/moowoodle.png',
                50
		    );
        }
    }

	/**
	 * Add Option page
	 */
	public function add_submenu() {
		// Array contain moowoodle submenu
		$submenus = [
			"all-courses" => [
				'name' 	 => __("All Courses", 'moowoodle'),
				'subtab' => ''
			],
			"manage-enrolment" => [
				'name'   => __("Manage Enrolment", 'moowoodle') . MOOWOOLE_PRO_STICKER,
				'subtab' => ''
			],
			"settings" => [
				'name'   => __("Settings", 'moowoodle'),
				'subtab' => 'connection'
			],
			"synchronization" => [
				'name'   => __("Synchronization", 'moowoodle'),
				'subtab' => 'synchronize-datamap'
			],
		];

		// Register all submenu
		foreach ( $submenus as $slug => $submenu ) {
			// prepare subtab if subtab is exist
			$subtab = '';

			if ( $submenu[ 'subtab' ] ) {
				$subtab = '&sub-tab=' . $submenu[ 'subtab' ];
			}

			add_submenu_page(
				'moowoodle',
				$submenu['name'],
				$submenu['name'],
				'manage_options',
				'moowoodle#&tab=' . $slug . $subtab,
				'_-return_null'
			);
		}
		
		remove_submenu_page('moowoodle', 'moowoodle');
		
		wp_enqueue_style(
			'moowoodle_admin_css',
			MOOWOODLE_PLUGIN_URL . 'build/index.css', array(),
			MOOWOODLE_PLUGIN_VERSION
		);

		wp_enqueue_script(
			'mwd-build-admin-frontend',
			MOOWOODLE_PLUGIN_URL . 'build/index.js',
			['wp-element', 'wp-i18n'],
			time(),
			true
		);

		// Get all tab setting's database value
        $settings_databases_value = [];

        $tabs_names = [
			'connection',
			'system',
			'user-information',
			'display',
			'sso',
			'notification',
			'synchronize-datamap',
			'synchronize-shcedule-course',
			'synchronize-shcedule-user'
		];

        foreach( $tabs_names as $tab_name ) {
			$option_name = str_replace( '-', '_', 'moowoodle_' . $tab_name . '_settings' );
            $settings_databases_value[ $tab_name ] = (object) MooWoodle()->setting->get_option( $option_name );
        }

		wp_localize_script(
			'mwd-build-admin-frontend',
			'appLocalizer',
			[
				'apiUrl' => untrailingslashit( get_rest_url() ),
				'side_banner_img' => esc_url(plugins_url()) .'/moowoodle/assets/images/logo-moowoodle-pro.png',
				'porAdv' => MOOWOODLE_PRO_ADV,
				'preSettings' => $settings_databases_value,
				'MW_Log' => MW_LOGS.'/error.txt',
				'rest_url' => esc_url_raw(rest_url()),
                'nonce'	=> wp_create_nonce('wp_rest'),
				'pro_sticker' => MOOWOOLE_PRO_STICKER,
				'pro_popup_overlay' => MOOWOODLE_PRO_ADV ? ' mw-pro-popup-overlay ' : '',
				'shop_url' => MOOWOODLE_PRO_SHOP_URL,
				'manage_enrolment_img_url' => esc_url(plugins_url())."/moowoodle/assets/images/manage-enrolment.jpg",
				'lang' => [
					'warning_to_force_checked' => esc_html__('The \'Sync now\' option requires \'Moodle Courses\' to be enabled.', 'moowoodle'),
					'Copy' => 'Copy',
					'Copied' => 'Copied',
				],
			],
		);

		do_action('moowoodle_upgrade_to_pro_admin_menu_hide');

		if ( MOOWOODLE_PRO_ADV ) {
			add_submenu_page(
				'moowoodle',
				__("Upgrade to Pro", 'moowoodle'),
				'<div class="upgrade-to-pro"><i class="dashicons dashicons-awards"></i>' . esc_html__("Upgrade to Pro", 'moowoodle') . '</div> ',
				'manage_options',
				'',
				array($this, 'handle_external_redirects')
			);
		}
	}

	// create the root page for load react.
	public static function create_settings_page() {
		
		$page = filter_input(INPUT_GET, 'page', FILTER_DEFAULT) !== null ? filter_input(INPUT_GET, 'page', FILTER_DEFAULT) : '';?>
		<div class="mw-admin-dashbord <?php echo $page; ?>">
			<div class="mw-general-wrapper" id ="moowoodle_root">
				<?php
				if (filter_input(INPUT_GET, 'page', FILTER_DEFAULT) == 'moowoodle' && !did_action( 'woocommerce_loaded' ) ) {
					?>
					<a href="javascript:history.back()"><?php echo __("Go Back","moowoodle");?></a>
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
					return;
				}
				?>
        	</div>
      	</div>
      	<?php
	}

	// Upgrade to pro redirection
	public function handle_external_redirects() {
		wp_redirect( esc_url( MOOWOODLE_PRO_SHOP_URL ) );
		die;
	}
}
