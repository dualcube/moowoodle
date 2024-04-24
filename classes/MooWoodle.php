<?php

namespace MooWoodle;

use \Automattic\WooCommerce\Utilities\FeaturesUtil;

defined('ABSPATH') || exit;

/**
 * MooWoodle Main Class
 *
 * @version		3.1.11
 * @package		MooWoodle
 * @author 		DualCube
 * 
 * @property Util $util instance of utill class
 * @property Setting $setting instance of setting class
 * @property Course $course instance of course class
 * @property Category $category instance of category class
 * @property RestAPI $restAPI instance of restapi class
 */
class MooWoodle {
    /**
     * Contain reference of MooWoodle class's object
     * @var object | null
     */
	private static $instance = null;

    /**
     * Contain all helper class's reference
     * @var array
     */
    private $container = [];

    /**
     * File path of moowoodle plugin.
     * @var string | null
     */
    public $file = null;

    /**
     * MooWoodle class constructor function
     * @param string $file File path of moowoodle plugin
     */
	public function __construct( $file ) {

        // load config file
        require_once trailingslashit( dirname( $file ) ) . 'config.php';

        // store plugin info
        $this->file = $file;
        $this->container[ 'plugin_url' ]     = trailingslashit( plugins_url( '', $file ) );
        $this->container[ 'plugin_path' ]    = trailingslashit( dirname( $file ) );
        $this->container[ 'version' ]        = MOOWOODLE_PLUGIN_VERSION;
        $this->container[ 'rest_namespace' ] = MOOWOODLE_REST_NAMESPACE;

        // activation and deactivation hook
        register_activation_hook( $file, [ $this, 'activate' ] );
        register_deactivation_hook( $file, [ $this, 'deactivate' ] );

        // initialise plugin
        add_action( 'admin_menu', [ Admin::class, 'add_menu' ] );
        add_action( 'before_woocommerce_init', [ $this, 'declare_compatibility' ] );
        add_action( 'woocommerce_loaded', [ $this, 'init_plugin' ] );
        add_action( 'plugins_loaded', [ $this , 'is_woocommerce_loaded'] );
	}

    /**
     * Activation function.
     * @return void
     */
    public function activate() {
        $this->container[ 'install' ] = new Installer();
    }
	
    /**
     * Deactivation function.
     * @return void
     */
    public function deactivate() {
		// Nothing to write now.
    }

    /**
     * Add High Performance Order Storage Support
     * @return void
     */
    public function declare_compatibility() {
        FeaturesUtil::declare_compatibility ( 'custom_order_tables', WP_CONTENT_DIR.'/plugins/moowoodle/moowoodle.php', true );
    }

    /**
     * Init plugin on woocommerce_loaded hook
     * @return void
     */
    public function init_plugin() {

        // add link on pugin 'active' button
        if ( is_admin() && !defined( 'DOING_AJAX' ) ) {
            add_filter( 'plugin_action_links_' . plugin_basename( MOOWOODLE_FILE ), [ $this , 'plugin_links' ] );
        }

        // Init required classes.
        add_action('init', [$this, 'init_classes']);

		// Init Text Domain
		$this->load_plugin_textdomain();

		// Create Log File.
		Util::log('');

        /**
         * Actiion hook after moowoodle loaded.
         */
        do_action( 'moowoodle_loaded' );
    }

    /**
     * Init all MooWoodle classess.
     * Access this classes using magic method.
     * @return void
     */
    public function init_classes() {
		$this->container[ 'util' ]       = new Util();
        $this->container[ 'setting' ]    = new Setting();
		$this->container[ 'course' ]     = new Course();
		$this->container[ 'category' ]   = new Category();
		$this->container[ 'restAPI' ]    = new RestAPI();

		$this->container[ 'Template' ]   = new Template();
		$this->container[ 'Enrollment' ] = new Enrollment();
		$this->container[ 'Emails' ]     = new Emails();
		$this->container[ 'Product' ]    = new Product();

        $this->container[ 'external_service' ]  = new ExternalService();
		$this->container[ 'MyAccountEndPoint' ] = new MyAccountEndPoint();

		if ( is_admin() ) {
			$this->container[ 'admin' ]        = new Admin();
		    $this->container[ 'TestConnection' ] = new TestConnection();
		}

        $this->container['restAPI']->synchronize(null);
    }

    /**
     * Take action based on if woocommerce is not loaded
     * @return void
     */
    public function is_woocommerce_loaded() {
        if ( !did_action( 'woocommerce_loaded' ) || is_admin() ) {
        	add_action( 'admin_notices', [ $this , 'woocommerce_admin_notice' ] );
        }
    }

    /**
     * Admin notice for woocommerce deactive
     * @return void
     */
    public function woocommerce_admin_notice() {
		?>
		<div id="message" class="error">
		<p><?php printf(__('%sMooWoodle is inactive.%s The %sWooCommerce plugin%s must be active for the MooWoodle to work. Please %sinstall & activate WooCommerce%s', 'moowoodle'), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . admin_url('plugins.php') . '">', '&nbsp;&raquo;</a>');?></p>
		</div>
    	<?php
	}

    /**
     * Render plugin page links.
     * @param array $link
     * @return array
     */
    public static function plugin_links( $links ) {
        
        // Create moowoodle plugin page link
        $plugin_links = array(
            '<a href="' . admin_url('admin.php?page=moowoodle-settings') . '">' . __('Settings', 'moowoodle') . '</a>',
            '<a href="' . MOOWOODLE_SUPPORT_URL . '">' . __('Support', 'moowoodle') . '</a>',
        );

        // Append the link
        $links = array_merge( $plugin_links, $links );

        if ( apply_filters( 'moowoodle_upgrage_to_pro', true ) ) {
            $links[] = '<a href="' . MOOWOODLE_PRO_SHOP_URL . '" target="_blank" style="font-weight: 700;background: linear-gradient(110deg, rgb(63, 20, 115) 0%, 25%, rgb(175 59 116) 50%, 75%, rgb(219 75 84) 100%);-webkit-background-clip: text;-webkit-text-fill-color: transparent;">' . __('Upgrade to Pro', 'moowoodle') . '</a>';
        }

        return $links;
    }

	/**
	 * Load Localisation files.
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present
	 * @return void
	 */
	private function load_plugin_textdomain() {

        $locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
        $locale = apply_filters( 'plugin_locale', $locale, MOOWOODLE_PLUGIN_TOKEN );

		load_textdomain( 'moowoodle', WP_LANG_DIR . "/moowoodle/moowoodle-$locale.mo" );
		load_textdomain( 'moowoodle', MooWoodle()->plugin_path . "/languages/moowoodle-$locale.mo" );

		load_plugin_textdomain( 'moowoodle', false, plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/languages' );
	}

	/**
     * Magic getter function to get the reference of class.
     * Accept class name, If valid return reference, else Wp_Error. 
     * @param   mixed $class
     * @return  object | \WP_Error
     */
    public function __get( $class ) {
        if ( array_key_exists( $class, $this->container ) ) {
            return $this->container[ $class ];
        }

        return new \WP_Error( sprintf('Call to unknown class %s.', $class ) );
    }

	/**
     * Initializes the MooWoodle class.
     * Checks for an existing instance
     * And if it doesn't find one, create it.
     * @param mixed $file
     * @return object | null
     */
	public static function init( $file ) {
        if ( self::$instance === null ) {
            self::$instance = new self( $file );
        }

        return self::$instance;
    }
}
