<?php
class DC_Woodle_Settings {
  
  private $tabs = array();
  
  private $sync_tabs = array();
  
  private $options;
  
  /**
   * Start up
   */
  public function __construct() {
    // Admin menu
    add_action( 'admin_menu', array( $this, 'add_settings_page' ), 100 );
    add_action( 'admin_init', array( $this, 'settings_page_init' ) );
    add_action( 'admin_init', array( $this, 'sync_page_init' ) );
    
    // Settings tabs
    add_action('settings_page_dc_woodle_general_tab_init', array(&$this, 'general_tab_init'), 10, 1);
    
    add_action('settings_page_dc_woodle_sync_tab_init', array(&$this, 'sync_tab_init'), 10, 1);
  }
  
  /**
   * Add options page
   */
  public function add_settings_page() {
    global $DC_Woodle;
    
    add_menu_page(
        __('MooWoodle', $DC_Woodle->text_domain), 
        __('MooWoodle', $DC_Woodle->text_domain), 
        'manage_options', 
        'dc-woodle-sync-courses', 
        array( $this, 'create_dc_woodle_sync' ),
        $DC_Woodle->plugin_url . 'assets/images/dualcube.png'
    );
    
    add_submenu_page('dc-woodle-sync-courses',
			__('Settings', $DC_Woodle->text_domain),
			__('Settings', $DC_Woodle->text_domain),
			'manage_options',
			'dc-woodle-setting-admin',
			array( $this, 'create_dc_woodle_settings' )
		);
		
		$this->sync_tabs = $this->get_dc_sync_tabs();
		$this->tabs = $this->get_dc_settings_tabs();
  }
  
  function get_dc_settings_tabs() {
    global $DC_Woodle;
    
    $tabs = apply_filters('dc_woodle_tabs', array(
      'dc_woodle_general' => __('MooWoodle General', $DC_Woodle->text_domain)
      ));
    
    return $tabs;
  }
  
  function get_dc_sync_tabs() {
    global $DC_Woodle;
    
    $tabs = array(
    	'dc_woodle_sync' => __('Synchronise Courses & categories', $DC_Woodle->text_domain)
		);
    return $tabs;
  }
  
  function dc_settings_tabs( $current = 'dc_woodle_general' ) {
    if ( isset ( $_GET['tab'] ) ) :
      $current = $_GET['tab'];
    else:
      $current = 'dc_woodle_general';
    endif;
    
    $links = array();
    foreach( $this->tabs as $tab => $name ) :
      if ( $tab == $current ) :
        $links[] = "<a class='nav-tab nav-tab-active' href='?page=dc-woodle-setting-admin&tab=$tab'>$name</a>";
      else :
        $links[] = "<a class='nav-tab' href='?page=dc-woodle-setting-admin&tab=$tab'>$name</a>";
      endif;
    endforeach;
    echo '<div class="icon32" id="dualcube_menu_ico"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach ( $links as $link )
      echo $link;
    echo '</h2>';
    
    foreach( $this->tabs as $tab => $name ) :
      if ( $tab == $current ) :
        echo "<h2>$name Settings</h2>";
      endif;
    endforeach;
  }
  
  function dc_sync_tabs( $current = 'dc_woodle_sync' ) {
    if ( isset ( $_GET['tab'] ) ) :
      $current = $_GET['tab'];
    else:
      $current = 'dc_woodle_sync';
    endif;
    
    $links = array();
    foreach( $this->sync_tabs as $tab => $name ) :
      if ( $tab == $current ) :
        $links[] = "<a class='nav-tab nav-tab-active' href='?page=dc-woodle-sync-courses&tab=$tab'>$name</a>";
      else :
        $links[] = "<a class='nav-tab' href='?page=dc-woodle-sync-courses&tab=$tab'>$name</a>";
      endif;
    endforeach;
    echo '<div class="icon32" id="dualcube_menu_ico"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach ( $links as $link )
      echo $link;
    echo '</h2>';
    
    foreach( $this->sync_tabs as $tab => $name ) :
      if ( $tab == $current ) :
        echo "<h2>$name</h2>";
      endif;
    endforeach;
  }

  /**
   * Options page callback
   */
  public function create_dc_woodle_settings() {
    global $DC_Woodle;
    
    ?>
    <div class="wrap">
      <?php $this->dc_settings_tabs(); ?>
      <?php
      $tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'dc_woodle_general' );
      $this->options = get_option( "dc_{$tab}_settings_name" );
      
      // This prints out all hidden setting errors
      settings_errors("dc_{$tab}_settings_name");
      ?>
      <form method="post" action="options.php">
      <?php
        //This prints out all hidden setting fields
        settings_fields( "dc_{$tab}_settings_group" );   
        do_settings_sections( "dc-{$tab}-settings-admin" );
        submit_button(); 
      ?>
      </form>
    </div>
    <?php
    do_action('dc_woodle_dualcube_admin_footer');
  }
  
  public function create_dc_woodle_sync() {
  	global $DC_Woodle;
  	
    ?>
    <div class="wrap">
      <?php $this->dc_sync_tabs(); ?>
      <?php
      $tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'dc_woodle_sync' );
      $this->options = get_option( "dc_{$tab}_settings_name" );
      
      // This prints out all hidden setting errors
      settings_errors("dc_{$tab}_settings_name");
      ?>
      <form method="post">
      <?php
        // This prints out all hidden setting fields
        settings_fields( "dc_{$tab}_settings_group" );   
        do_settings_sections( "dc-{$tab}-settings-admin" );
        submit_button('Synchronise');
        wp_nonce_field( 'dc-sync_courses_and_categories' );
      ?>
      </form>
    </div>
    <?php
    do_action('dc_woodle_dualcube_admin_footer');
  }

  /**
   * Register and add settings
   */
  public function settings_page_init() { 
    do_action('befor_settings_page_init');
    
    // Register each tab settings
    foreach( $this->tabs as $tab => $name ) :
      do_action("settings_page_{$tab}_tab_init", $tab);
    endforeach;
    
    foreach( $this->sync_tabs as $tab => $name ) :
      do_action("settings_page_{$tab}_tab_init", $tab);
    endforeach;
    
    do_action('after_settings_page_init');
  }
  
    /**
   * Register and add settings
   */
  public function sync_page_init() { 
    foreach( $this->sync_tabs as $tab => $name ) :
      do_action("settings_page_{$tab}_tab_init", $tab);
    endforeach;
  }
  
  /**
   * Register and add settings fields
   */
  public function settings_field_init($tab_options) {
    global $DC_Woodle;
    
    if(!empty($tab_options) && isset($tab_options['tab']) && isset($tab_options['ref']) && isset($tab_options['sections'])) {
      // Register tab options
      register_setting(
        "dc_{$tab_options['tab']}_settings_group", // Option group
        "dc_{$tab_options['tab']}_settings_name", // Option name
        array( $tab_options['ref'], "dc_{$tab_options['tab']}_settings_sanitize" ) // Sanitize
      );
      
      foreach($tab_options['sections'] as $sectionID => $section) {
        // Register section
        add_settings_section(
          $sectionID, // ID
          $section['title'], // Title
          array( $tab_options['ref'], "{$sectionID}_info" ), // Callback
          "dc-{$tab_options['tab']}-settings-admin" // Page
        );
        
        // Register fields
        if(isset($section['fields'])) {
          foreach($section['fields'] as $fieldID => $field) {
            if(isset($field['type'])) {
              $field = $DC_Woodle->dc_wp_fields->check_field_id_name($fieldID, $field);
              $field['tab'] = $tab_options['tab'];
              $callbak = $this->get_field_callback_type($field['type']);
              if(!empty($callbak)) {
                add_settings_field(
                  $fieldID,
                  $field['title'],
                  array( $this, $callbak ),
                  "dc-{$tab_options['tab']}-settings-admin",
                  $sectionID,
                  $field
                );
              }
            }
          }
        }
      }
    }
  }
  
  function general_tab_init($tab) {
    global $DC_Woodle;
    
    $DC_Woodle->admin->load_class("settings-{$tab}", $DC_Woodle->plugin_path, $DC_Woodle->token);
    new DC_Woodle_Settings_Gneral($tab);
  }
  
  function sync_tab_init($tab) {
    global $DC_Woodle;
    
    $DC_Woodle->admin->load_class("settings-{$tab}", $DC_Woodle->plugin_path, $DC_Woodle->token);
    new DC_Woodle_Settings_Sync($tab);
  }
  
  function get_field_callback_type($fieldType) {
    $callBack = '';
    switch($fieldType) {
      case 'input':
      case 'text':
      case 'email':
      case 'number':
      case 'file':
      case 'url':
        $callBack = 'text_field_callback';
        break;
        
      case 'hidden':
        $callBack = 'hidden_field_callback';
        break;
        
      case 'textarea':
        $callBack = 'textarea_field_callback';
        break;
        
      case 'wpeditor':
        $callBack = 'wpeditor_field_callback';
        break;
        
      case 'checkbox':
        $callBack = 'checkbox_field_callback';
        break;
        
      case 'radio':
        $callBack = 'radio_field_callback';
        break;
        
      case 'select':
        $callBack = 'select_field_callback';
        break;
        
      case 'upload':
        $callBack = 'upload_field_callback';
        break;
        
      default:
        $callBack = '';
        break;
    }
    
    return $callBack;
  }
  
  /** 
   * Get the hidden field display
   */
  public function hidden_field_callback($field) {
    global $DC_Woodle;
    
    $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "dc_{$field['tab']}_settings_name[{$field['name']}]";
    $DC_Woodle->dc_wp_fields->hidden_input($field);
  }
  
  /** 
   * Get the text field display
   */
  public function text_field_callback($field) {
    global $DC_Woodle;
    
    $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "dc_{$field['tab']}_settings_name[{$field['name']}]";
    $DC_Woodle->dc_wp_fields->text_input($field);
  }
  
  /** 
   * Get the text area display
   */
  public function textarea_field_callback($field) {
    global $DC_Woodle;
    
    $field['value'] = isset( $field['value'] ) ? esc_textarea( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_textarea( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "dc_{$field['tab']}_settings_name[{$field['name']}]";
    $DC_Woodle->dc_wp_fields->textarea_input($field);
  }
  
  /** 
   * Get the wpeditor display
   */
  public function wpeditor_field_callback($field) {
    global $DC_Woodle;
    
    $field['value'] = isset( $field['value'] ) ? ( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? ( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "dc_{$field['tab']}_settings_name[{$field['name']}]";
    $DC_Woodle->dc_wp_fields->wpeditor_input($field);
  }
  
  /** 
   * Get the checkbox field display
   */
  public function checkbox_field_callback($field) {
    global $DC_Woodle;
    
    $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
    $field['dfvalue'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : '';
    $field['name'] = "dc_{$field['tab']}_settings_name[{$field['name']}]";
    $DC_Woodle->dc_wp_fields->checkbox_input($field);
  }
  
  /** 
   * Get the checkbox field display
   */
  public function radio_field_callback($field) {
    global $DC_Woodle;
    
    $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "dc_{$field['tab']}_settings_name[{$field['name']}]";
    $DC_Woodle->dc_wp_fields->radio_input($field);
  }
  
  /** 
   * Get the select field display
   */
  public function select_field_callback($field) {
    global $DC_Woodle;
    
    $field['value'] = isset( $field['value'] ) ? esc_textarea( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_textarea( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "dc_{$field['tab']}_settings_name[{$field['name']}]";
    $DC_Woodle->dc_wp_fields->select_input($field);
  }
  
  /** 
   * Get the upload field display
   */
  public function upload_field_callback($field) {
    global $DC_Woodle;
    
    $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
    $field['name'] = "dc_{$field['tab']}_settings_name[{$field['name']}]";
    $DC_Woodle->dc_wp_fields->upload_input($field);
  }
}