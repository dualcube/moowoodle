<?php
class DC_Woodle_Admin {
  
  public $settings;

	public function __construct() {
		global $DC_Woodle;
		
		//admin script and style
		add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_script'));		
		add_action('dc_woodle_dualcube_admin_footer', array(&$this, 'dualcube_admin_footer_for_dc_woodle'));
		
		add_filter( 'plugin_action_links_' . DC_WOODLE_PLUGIN_BASENAME, array( &$this, 'add_action_links' ) );
		
		$this->load_class('settings');
		$this->settings = new DC_Woodle_Settings();
	}
	
	function add_action_links( $links ) {
		global $DC_Woodle;
		
		$links[] = '<a href="' . esc_url( get_admin_url(null, 'admin.php?page=dc-woodle-setting-admin') ) . '">' . __( 'Settings', $DC_Woodle->text_domain ) . '</a>';
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
	
	/**
	 * Add footer to settings page
	 *
	 * @access public
   * @return void
	 */
	function dualcube_admin_footer_for_dc_woodle() {
    global $DC_Woodle;
    
    ?>
    <div style="clear: both"></div>
    <div id="dc_admin_footer">
      <?php _e('Powered by', $DC_Woodle->text_domain); ?> <a href="http://dualcube.com" target="_blank"><img src="<?php echo $DC_Woodle->plugin_url.'/assets/images/dualcube.png'; ?>"></a><?php _e('Dualcube', $DC_Woodle->text_domain); ?> &copy; <?php echo date('Y');?>
    </div>
    <?php
	}

	/**
	 * Admin Scripts
	 *
	 * @access public
   * @return void
	 */
	public function enqueue_admin_script() {
		global $DC_Woodle;
		
		$screen = get_current_screen();
		// Enqueue admin script and stylesheet
		if ( in_array( $screen->id, array( 'synchronise_page_dc-woodle-setting-admin', 'toplevel_page_dc-woodle-sync-courses', 'product', 'course' ) ) ) :   
		  $DC_Woodle->library->load_qtip_lib();
		  
		  wp_enqueue_script( 'admin_js', $DC_Woodle->plugin_url.'assets/admin/js/admin.js', array( 'jquery' ), $DC_Woodle->version, true );
		  wp_enqueue_style( 'admin_css',  $DC_Woodle->plugin_url.'assets/admin/css/admin.css', array(), $DC_Woodle->version );
		  
		  if( DC_Woodle_Dependencies::wc_active_check() ) {
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				$wc_assets_path = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';
				wp_enqueue_script( 'chosen', $wc_assets_path . 'js/chosen/chosen.jquery' . $suffix . '.js', array( 'jquery' ), '1.0.0' );
	  	}
	  endif;
	}
}