<?php

class DC_Woodle_Admin {

	public $settings;
	
	public function __construct() {
		global $DC_Woodle;

		//admin script and style
		add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_script'));

		add_action('dualcube_admin_footer_for_dc_woodle', array( &$this, 'dualcube_admin_footer_for_dc_woodle' ));
		add_filter( 'plugin_action_links_' . DC_WOODLE_PLUGIN_BASENAME, array( &$this, 'add_action_links' ) );

		$this->load_class('settings');
		$this->settings = new DC_Woodle_Settings();
	}

	function add_action_links( $links ) {
		global $DC_Woodle;
		
		$links[] = '<a href="' . esc_url( get_admin_url(null, 'admin.php?page=dc-woodle') ) . '">' . __( 'Settings', 'dc-woodle' ) . '</a>';
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
	  global $DC_Woodle;
	  
		if ('' != $class_name) {
			require_once ($DC_Woodle->plugin_path . '/admin/class-' . esc_attr($DC_Woodle->token) . '-' . esc_attr($class_name) . '.php');
		} // End If Statement
	}// End load_class()

	public function dualcube_admin_footer_for_dc_woodle() {
		global $DC_Woodle;

		?>
	    <div style="clear: both"></div>
	    <div id="dc-admin-footer">
      		<?php _e('Powered by', 'dc-woodle'); ?> <a href="http://dualcube.com" target="_blank"><img src="<?php echo $DC_Woodle->plugin_url.'/assets/images/dualcube.png'; ?>"></a><?php _e('Dualcube', 'dc-woodle'); ?> &copy; <?php echo date('Y');?>
    	</div>
    	<?php
	}

	

        /**
         * Admin Scripts
         */
        public function enqueue_admin_script() {
            global $DC_Woodle;
            $screen = get_current_screen();
            // Enqueue admin script and stylesheet from here
            if ($screen->id == 'toplevel_page_dc-woodle' ) :
                wp_enqueue_style( 'wp-color-picker' );                wp_enqueue_script('catalog_admin_js', $DC_Woodle->plugin_url . 'assets/admin/js/admin.js', array('jquery'), $DC_Woodle->version, true);
                wp_enqueue_style('catalog_admin_css', $DC_Woodle->plugin_url . 'assets/admin/css/admin.css', array(), $DC_Woodle->version);

                wp_enqueue_style('font-awesome-solid', $DC_Woodle->plugin_url . 'assets/fontawesome/css/solid.min.css', array(), $DC_Woodle->version);

                wp_enqueue_style('font-awesome-brands', $DC_Woodle->plugin_url . 'assets/fontawesome/css/brands.min.css', array(), $DC_Woodle->version);

                wp_enqueue_style('font-awesome', $DC_Woodle->plugin_url . 'assets/fontawesome/css/fontawesome.min.css', array(), $DC_Woodle->version);

            endif;
        }
	
}