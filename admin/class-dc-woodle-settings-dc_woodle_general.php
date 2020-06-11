<?php
class DC_Woodle_Settings_Gneral {
  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;
  
  private $tab;

  /**
   * Start up
   */
  public function __construct($tab) {
    $this->tab = $tab;
    $this->options = get_option( "dc_{$this->tab}_settings_name" );
    $this->settings_page_init();
  }
  
  /**
   * Register and add settings
   */
  
public function settings_page_init() {
    global $DC_Woodle;
    
    $settings_tab_options = array("tab" => "{$this->tab}",
                                  "ref" => &$this,
                                  "sections" => array(
    "moodle_settings_section" => array("title" =>  __('Moodle Settings', $DC_Woodle->text_domain), // Section one
    "fields" => array("access_url" => array('title' => __('Access URL', $DC_Woodle->text_domain), 'type' => 'url', 'id' => 'access_url', 'label_for' => 'access_url', 'name' => 'access_url', 'hints' => __('Enter the moodle site URL you want to integrate.', $DC_Woodle->text_domain), 'desc' => __('Moodle site URL (Value of $CFG->wwwroot).', $DC_Woodle->text_domain)), // Text
      "ws_token" => array('title' => __('Webservice token', $DC_Woodle->text_domain), 'type' => 'text', 'id' => 'ws_token', 'label_for' => 'ws_token', 'name' => 'ws_token', 'hints' => __('Enter the moodle webservice token.', $DC_Woodle->text_domain), 'desc' => __('Moodle webservice token (Generate from moodle).', $DC_Woodle->text_domain)) // Text
                                                                                                           )
    ), 
    "wc_settings_section" => array("title" => "WooCommerce Product Settings", // Another section
    "fields" => array("create_wc_product" => array('title' => __('Create products from courses', $DC_Woodle->text_domain), 'type' => 'checkbox', 'id' => 'create_wc_product', 'label_for' => 'create_wc_product', 'name' => 'create_wc_product', 'hints' => __('Checked to create products from moodle courses.', $DC_Woodle->text_domain), 'desc' => __('If checked products will created while syncing courses from moodle.', $DC_Woodle->text_domain), 'value' => 'yes'),
     "wc_product_dates_display" => array('title' => __('Display start date and end date in shop page', $DC_Woodle->text_domain), 'type' => 'checkbox', 'id' => 'wc_product_dates_display', 'label_for' => 'wc_product_dates_display', 'name' => 'wc_product_dates_display', 'hints' => __('Checked to display dates in shop page under products.', $DC_Woodle->text_domain), 'desc' => __('If checked display start date and end date in shop page.', $DC_Woodle->text_domain),  'value' => 'yes')
)
),
   
    "user_settings_section" => array("title" => "User Settings", // Another section
    "fields" => array(
     "update_existing_users" => array('title' => __('Update existing users', $DC_Woodle->text_domain), 'type' => 'radio', 'id' => 'update_existing_users', 'label_for' => 'update_existing_users', 'name' => 'update_existing_users', 'options' => array('true' => 'Yes', 'false' => 'No'), 'dfvalue' => 'true',  'hints' => __('If enabled Moodle will update the profile fields.', $DC_Woodle->text_domain), 'desc' => __('Whether Moodle will update the profile fields in Moodle for existing users.', $DC_Woodle->text_domain))
   )),

  ));
    

    $DC_Woodle->admin->settings->settings_field_init(apply_filters("settings_{$this->tab}_tab_options", $settings_tab_options));
  }
  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function dc_dc_woodle_general_settings_sanitize( $input ) {
    global $DC_Woodle;
    
    $new_input = array();
    
    $hasError = false;
    
    if( isset( $input['access_url'] ) ) {
      $new_input['access_url'] = rtrim( $input['access_url'], '/' );
    } else {
      add_settings_error(
        "dc_{$this->tab}_settings_name",
        esc_attr( "dc_{$this->tab}_settings_admin_error" ),
        __('Moodle access URL should not be empty.', $DC_Woodle->text_domain),
        'error'
      );
      $hasError = true;
    }
    
    if( isset( $input['ws_token'] ) ) {
      $new_input['ws_token'] = sanitize_text_field( $input['ws_token'] );
    } else {
      add_settings_error(
        "dc_{$this->tab}_settings_name",
        esc_attr( "dc_{$this->tab}_settings_admin_error" ),
        __('Moodle webservice token should not be empty.', $DC_Woodle->text_domain),
        'error'
      );
      $hasError = true;
    }
    
    if( isset( $input['create_wc_product'] ) )
      $new_input['create_wc_product'] = $input['create_wc_product'];

    if( isset( $input['wc_product_dates_display'] ) )
      $new_input['wc_product_dates_display'] = $input['wc_product_dates_display'];

    if( isset( $input['update_existing_users'] ) )
      $new_input['update_existing_users'] = $input['update_existing_users'];
    
    if( isset( $input['update_user_info'] ) )
      $new_input['update_user_info'] = $input['update_user_info'];
    
    if( isset( $input['moodle_role_id'] ) )
      $new_input['moodle_role_id'] = ( ! empty( $input['moodle_role_id'] ) ) ? intval( $input['moodle_role_id'] ) : '';
    
    if(!$hasError) {
      add_settings_error(
        "dc_{$this->tab}_settings_name",
        esc_attr( "dc_{$this->tab}_settings_admin_updated" ),
        __('General settings updated', $DC_Woodle->text_domain),
        'updated'
      );
    }

    return $new_input;
  }

  /** 
   * Print the Section text
   */
  public function moodle_settings_section_info() {
    global $DC_Woodle;
    
    _e('Enter your moodle settings below', $DC_Woodle->text_domain);
  }
  
  /** 
   * Print the Section text
   */
  public function wc_settings_section_info() {
    global $DC_Woodle;
    
    _e('Enter your WooCommerce settings below', $DC_Woodle->text_domain);
  }
  
  /** 
   * Print the Section text
   */
  public function user_settings_section_info() {
    global $DC_Woodle;
    
    _e('Enter user settings below', $DC_Woodle->text_domain);
  }
}