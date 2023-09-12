<?php

class MooWoodle_Admin {

	public $settings;

	public function __construct() {

		//admin script and style
		add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_script'));
		add_action('moowoodle_admin_footer', array(&$this, 'moowoodle_admin_footer'));

		$this->load_class('settings');
		$this->settings = new MooWoodle_Settings();
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
			require_once $MooWoodle->plugin_path . '/admin/class-' . $MooWoodle->token . '-' . $class_name . '.php';
		} // End If Statement
	} // End load_class()

	public function moowoodle_admin_footer() {
		global $MooWoodle;

		?>
		<div style="clear: both"></div>
		<div class="dualcube-admin-footer" id="dualcube-admin-footer">
			<?php esc_html_e('Powered by', 'moowoodle');?> <a href="<?php echo esc_url(MOOWOODLE_DUALCUBE_URL); ?>" target="_blank"><img src="<?php echo esc_url($MooWoodle->plugin_url) ?>/assets/images/dualcube.png"></a><?php esc_html_e('ualCube', 'moowoodle');?> &copy; <?php echo esc_html(date('Y')); ?>
		</div>
<?php
}

	/**
	 * Admin Scripts
	 */
	public function enqueue_admin_script() {
		global $MooWoodle;
		wp_enqueue_style('moowoodle_admin_css', $MooWoodle->plugin_url . 'assets/admin/css/admin.css', array(), $MooWoodle->version);
	}
}
