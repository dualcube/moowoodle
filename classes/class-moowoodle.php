<?php
class MooWoodle {

	public $plugin_url;

	public $plugin_path;

	public $version;

	public $token;
	
	public $text_domain;
	
	public $library;

	public $admin;

	public $sync;

	public $template;

	public $posttype;
	
	public $taxonomy;

	private $file;
	
	public $settings;
	
	public $enrollment;
	
	public $emails;

	public $endpoints;
	
	public $ws_has_error;
	
	public $options_general_settings;
	
	public $options_display_settings;
	
	public $options_synchronize_settings;
	
	public $options_timeout_settings;
	
	public $testconnection;

	private static $active_plugins;

	public function __construct( $file ) {

		$this->file = $file;
		$this->plugin_url = trailingslashit( plugins_url( '', $plugin = $file ) );
		$this->plugin_path = trailingslashit( dirname( $file ) );
		$this->token = MOOWOODLE_PLUGIN_TOKEN;
		$this->text_domain = MOOWOODLE_TEXT_DOMAIN;
		$this->version = MOOWOODLE_PLUGIN_VERSION;
		
		// default general setting
		$this->options_general_settings = get_option( 'moowoodle_general_settings' );
		//display settings
		$this->options_display_settings = get_option( 'moowoodle_display_settings' );
		// synchronize settings
		$this->options_synchronize_settings = get_option( 'moowoodle_synchronize_settings' );
		// moodle timeout settings
			$timeout = get_option( 'moowoodle_general_settings' );
		if($timeout != null && is_int((int)$timeout['moodle_timeout']) && (int)$timeout['moodle_timeout']>5){
			$this->options_timeout_settings = $timeout;
		}
		else{
			$timeout['moodle_timeout'] = 5;
			$this->options_timeout_settings['moodle_timeout'] = 5;
			update_option('moowoodle_general_settings', $timeout);
		}

		

		add_filter( 'woocommerce_product_class',array($this, 'product_type_subcription_warning'), 10, 2 );
		add_action( 'init', array( &$this, 'init' ), 0 );
	}
	
	/**
	 * initilize plugin on WP init
	*/
	function init() {
		
		// Init Text Domain
		$this->load_plugin_textdomain();
		
		// Init library
		$this->load_class( 'library' );
		$this->library = new MooWoodle_Library();

		if ( is_admin() ) {
			$this->load_class( 'admin' );
			$this->admin = new MooWoodle_Admin();
			$this->load_class( 'sync' );
			$this->sync = new MooWoodle_Sync();
			$this->load_class( 'testconnection' );
			$this->testconnection = new MooWoodle_testconnection();

			//frontend js file
			wp_enqueue_script('moowoodle_admin_frontend', plugins_url('../assets/admin/js/moowoodle-admin-frontend.js', __FILE__), array('jquery'), '', true);
		}
		
		// init templates
		$this->load_class( 'template' );
		$this->template = new MooWoodle_Template();
		
		// init posttype
		$this->load_class( 'posttype' );
		$this->posttype = new MooWoodle_PostType();
		
		// init taxonomy
		$this->load_class( 'taxonomy' );
		$this->taxonomy = new MooWoodle_Toxonomy();
		
		// init enrollment
		$this->load_class( 'enrollment' );
		$this->enrollment = new MooWoodle_Enrollment();
		
		// init emails
		$this->load_class( 'emails' );
		$this->emails = new MooWoodle_Emails();

		//init endpoints
		$this->load_class( 'endpoints' );
		$this->endpoints = new MooWoodle_Endpoints();

		//log folder
		if(!file_exists(MW_LOGS . "/error.log")){
			wp_mkdir_p( MW_LOGS );
			 echo file_put_contents(MW_LOGS . "/error.log",date("d/m/Y h:i:s a",time()). ": " . "MooWoodle Log file Created\n", FILE_APPEND );
		}
		//clear log file
		if(isset($_POST['clearlog'])){
			file_put_contents(MW_LOGS . "/error.log" ,  date("d/m/Y h:i:s a",time()). ": " . "MooWoodle Log file Cleared\n");
		}
	}
	
	/**
   * Load Localisation files.
   *
   * Note: the first-loaded translation file overrides any following ones if the same translation is present
   *
   * @access public
   * @return void
   */
  public function load_plugin_textdomain() {
    $locale = apply_filters( 'plugin_locale', get_locale(), $this->token );

    load_textdomain( $this->text_domain, WP_LANG_DIR . "/moowoodle/moowoodle-$locale.mo" );
    load_textdomain( $this->text_domain, $this->plugin_path . "/languages/moowoodle-$locale.mo" );
 
    $locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
    $locale = apply_filters( 'moowoodle_plugin_locale', $locale, 'moowoodle' );
    load_plugin_textdomain( 'moowoodle', false, plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/languages' );
  }

  /**
	 * Load class file
	 *
	 * @access public
	 * @param string $class_name (default: null)
	 * @return void
	 */
	public function load_class( $class_name = '' ) {
		if ( '' != $class_name && '' != $this->token ) {
			require_once ( 'class-' . esc_attr( $this->token ) . '-' . esc_attr( $class_name ) . '.php');
		} // End If Statement
	}// End load_class()
	
	/** Cache Helpers *********************************************************/

	/**
	 * Sets a constant preventing some caching plugins from caching a page. Used on dynamic pages
	 *
	 * @access public
	 * @return void
	 */
	function nocache() {
		if ( ! defined( 'DONOTCACHEPAGE' ) )
			define( "DONOTCACHEPAGE", "true" );
		// WP Super Cache constant
	}

	public function product_type_subcription_warning($php_classname, $product_type)
	{
		self::$active_plugins = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() )
			self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		if(in_array( 'woocommerce-subscriptions/woocommerce-subscriptions.php', self::$active_plugins ) || array_key_exists( 'woocommerce-subscriptions/woocommerce-subscriptions.php', self::$active_plugins )){
			add_action( 'admin_notices', array( $this, 'product_type_subcription_notice' ) );
			
		}
	}
	/**
	* Displays an inactive notice when the software is inactive.
	*/
	public function product_type_subcription_notice() { 
		echo apply_filters('moowoodle_pro_sticker','<div class="notice notice-warning is-dismissible"><p>'. __('Woocomerce subbcription is supported only with moowoodle pro') .'</p></div>');
	}
}