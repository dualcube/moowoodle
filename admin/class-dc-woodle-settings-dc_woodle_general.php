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
                                                                                         "fields" => array("access_url" => array('title' => __('Access URL', $DC_Woodle->text_domain), 'type' => 'url', 'id' => 'access_url', 'label_for' => 'access_url', 'name' => 'access_url', 'hints' => __('Enter the moodle site URL you want to integrate.', $DC_Woodle->text_domain), 'desc' => __('Moodle site URL.', $DC_Woodle->text_domain)), // Text
                                                                                                           "ws_token" => array('title' => __('Webservice token', $DC_Woodle->text_domain), 'type' => 'text', 'id' => 'ws_token', 'label_for' => 'ws_token', 'name' => 'ws_token', 'hints' => __('Enter the moodle webservice token.', $DC_Woodle->text_domain), 'desc' => __('Moodle webservice token (Generate from moodle).', $DC_Woodle->text_domain)) // Text
                                                                                                           )
                                                                                         ), 
                                                      "wc_settings_section" => array("title" => "WooCommerce Product Settings", // Another section
                                                                                         "fields" => array("create_wc_product" => array('title' => __('Create products from courses', $DC_Woodle->text_domain), 'type' => 'checkbox', 'id' => 'create_wc_product', 'label_for' => 'create_wc_product', 'name' => 'create_wc_product', 'hints' => __('Checked to create products from moodle courses.', $DC_Woodle->text_domain), 'desc' => __('If checked products will created while syncing courses from moodle.', $DC_Woodle->text_domain), 'value' => 'yes')
                                                                                                          )
                                                                                         ),
                                                      "user_settings_section" => array("title" => "User Settings", // Another section
                                                                                         "fields" => array("update_user_info" => array('title' => __('Update user info with order info', $DC_Woodle->text_domain), 'type' => 'checkbox', 'id' => 'update_user_info', 'label_for' => 'update_user_info', 'name' => 'update_user_info', 'hints' => __('Check to update user info according to billing address.', $DC_Woodle->text_domain), 'desc' => __('If chacked user\'s info will be updated according to billing address.', $DC_Woodle->text_domain), 'value' => 'yes'),
                                                                                         	 								 "moodle_role_id" => array('title' => __('Moodle user role id in a course', $DC_Woodle->text_domain), 'type' => 'text', 'id' => 'moodle_role_id', 'label_for' => 'moodle_role_id', 'name' => 'moodle_role_id', 'hints' => __('If role assigned to the enrollees is other than student.', $DC_Woodle->text_domain), 'desc' => __('Default role student (Role id: 5).', $DC_Woodle->text_domain))
                                                                                                          )
                                                                                         )
                                                      )
                                  );
    
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