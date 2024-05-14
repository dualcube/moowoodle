<?php

namespace MooWoodle;

class Admin {
	
	public function __construct() {
		// Register submenu for admin menu
		add_action( 'admin_menu', [ &$this, 'add_submenu' ] );
		add_action( 'admin_enqueue_scripts', [ &$this, 'enqueue_admin_script' ] );
	}

	/**
	 * Add moowoodle menu in admin dashboard.
	 * @return void
	 */
    public static function add_menu() {
        if( is_admin() ) {
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
				'subtab' => 'general'
			],
			"synchronization" => [
				'name'   => __("Synchronization", 'moowoodle'),
				'subtab' => 'synchronize-user'
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

		// Register upgrade to pro submenu page.
		if ( ! Util::is_pro_active() ) {
			add_submenu_page(
				'moowoodle',
				__("Upgrade to Pro", 'moowoodle'),
				'<div class="upgrade-to-pro"><i class="dashicons dashicons-awards"></i>' . esc_html__("Upgrade to Pro", 'moowoodle') . '</div> ',
				'manage_options',
				'',
				array($this, 'handle_external_redirects')
			);
		}
		
		remove_submenu_page('moowoodle', 'moowoodle');
	}

	/**
     * Enqueue JavaScript for admin fronend page and localize script.
     * @return void
     */
	public function enqueue_admin_script() {
		wp_enqueue_style(
			'moowoodle_admin_css',
			MOOWOODLE_PLUGIN_URL . 'build/index.css', array(),
			MOOWOODLE_PLUGIN_VERSION
		);

		wp_enqueue_script(
			'moowoodle-admin-script',
			MOOWOODLE_PLUGIN_URL . 'build/index.js',
			['wp-element', 'wp-i18n'],
			time(),
			true
		);

		// Get all tab setting's database value
        $settings_databases_value = [];

        $tabs_names = [
			'general',
			'display',
			'sso',
			'log',
			'notification',
			'synchronize-course',
			'synchronize-user'
		];

        foreach( $tabs_names as $tab_name ) {
			$option_name = str_replace( '-', '_', 'moowoodle_' . $tab_name . '_settings' );
            $settings_databases_value[ $tab_name ] = (object) MooWoodle()->setting->get_option( $option_name );
        }

		wp_localize_script(
			'moowoodle-admin-script',
			'appLocalizer',
			[
				'apiUrl' 	  => untrailingslashit( get_rest_url() ),
                'nonce'		  => wp_create_nonce('wp_rest'),
				'preSettings' => $settings_databases_value,
				'pro_active'  => Util::is_pro_active(),
				'pro_sticker' => MOOWOOLE_PRO_STICKER,
				'shop_url'    => MOOWOODLE_PRO_SHOP_URL,
				'accountmenu' => wc_get_account_menu_items(),
				'log_url'     => get_site_url( null, str_replace( ABSPATH, '', MOOWOODLE_LOGS ) ),
			],
		);
	}

	/**
     * Admin frontend react page.
	 * If plugin is not active it render activation page.
     * @return void
     */
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

	/**
	 * Redirct to pro shop url.
	 * @return never
	 */
	public function handle_external_redirects() {
		wp_redirect( esc_url( MOOWOODLE_PRO_SHOP_URL ) );
		die;
	}
}
