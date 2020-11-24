<?php

class MooWoodle_Admin {

	public $settings;
	
	public function __construct() {
		global $MooWoodle;

		//admin script and style
		add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_script'));

		add_action('moowoodle_admin_footer', array( &$this, 'moowoodle_admin_footer' ));
		add_filter( 'plugin_action_links_' . MooWOODLE_PLUGIN_BASENAME, array( &$this, 'add_action_links' ) );

		$this->load_class('settings');
		$this->settings = new MooWoodle_Settings();
	}

	function add_action_links( $links ) {
		global $MooWoodle;
		
		$links[] = '<a href="' . esc_url( get_admin_url(null, 'admin.php?page=moowoodle') ) . '">' . __( 'Settings', 'moowoodle' ) . '</a>';
		return $links;
  	}

	/**
	 * Load class file
	 *
	 * @access public
	 * @param string $class_name (default: null)
   * @return void
	 */
	function load_class($class_name = '') {
	  global $MooWoodle;
	  
		if ('' != $class_name) {
			require_once ($MooWoodle->plugin_path . '/admin/class-' . esc_attr($MooWoodle->token) . '-' . esc_attr($class_name) . '.php');
		} // End If Statement
	}// End load_class()

	public function moowoodle_admin_footer() {
		global $MooWoodle;

		?>
	    <div style="clear: both"></div>
	    <div id="dc-admin-footer">
      		<?php _e('Powered by', 'moowoodle'); ?> <a href="http://dualcube.com" target="_blank"><img src="<?php echo $MooWoodle->plugin_url.'/assets/images/dualcube.png'; ?>"></a><?php _e('Dualcube', 'moowoodle'); ?> &copy; <?php echo date('Y');?>
    	</div>
    	<?php
	}

	

        /**
         * Admin Scripts
         */
        public function enqueue_admin_script() {
            global $MooWoodle;
            $screen = get_current_screen();
            // Enqueue admin script and stylesheet from here
            if ($screen->id == 'toplevel_page_moowoodle' ) :
                wp_enqueue_style( 'wp-color-picker' );                wp_enqueue_script('catalog_admin_js', $MooWoodle->plugin_url . 'assets/admin/js/admin.js', array('jquery'), $MooWoodle->version, true);
                wp_enqueue_style('catalog_admin_css', $MooWoodle->plugin_url . 'assets/admin/css/admin.css', array(), $MooWoodle->version);

                wp_enqueue_style('font-awesome-solid', $MooWoodle->plugin_url . 'assets/fontawesome/css/solid.min.css', array(), $MooWoodle->version);

                wp_enqueue_style('font-awesome-brands', $MooWoodle->plugin_url . 'assets/fontawesome/css/brands.min.css', array(), $MooWoodle->version);

                wp_enqueue_style('font-awesome', $MooWoodle->plugin_url . 'assets/fontawesome/css/fontawesome.min.css', array(), $MooWoodle->version);

            endif;
        }
	
}