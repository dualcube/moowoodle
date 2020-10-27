<?php
class DC_Woodle_Library {

  public $lib_url;

  public $jquery_lib_url;

  public function __construct() {
	global $DC_Woodle;
	  
    $this->lib_url = $DC_Woodle->plugin_url . 'lib/';
    $this->jquery_lib_url = $this->lib_url . 'jquery/';
	
    }
	
	public function dc_woodle_get_options(){

        global $DC_Woodle;
    /**
     * Create new menus
     */

    $dc_woodle_options[ ] = array(
        "type" => "menu",
        "menu_type" => "add_menu_page",
        "page_name" => __( "MooWoodle", 'dc-woodle' ),
        "menu_slug" => "dc-woodle",
        "layout" => "2-col"
    );

    /**
     * Settings Tab
     */
    $dc_woodle_options[ ] = array(
        "type" => "tab",
        "id" => "dc-woodle-general",
        "label" => __( "General", 'dc-woodle' ),
        "font_class" => "fa-cogs"
    );

    // setting box
    $dc_woodle_options[ ] = array(
        "type" => "setting",
        "id" => "dc_woodle_general_settings",
    );

    //Connection Settings
    $dc_woodle_options[ ] = array(
        "type" => "section",
        "id" => "dc-woodle-connection",
        "label" => __( "Connection", 'dc-woodle' ),
    );

    // Moodle URL
    $dc_woodle_options[ ] = array(
        "type" => "textbox",
        "id" => "moodle-url",
        'name' => "moodle_url",
        "label" => __( "Moodle URL", 'dc-woodle' ),
        "desc" => __('Enter the Moodle URL', 'dc-woodle' ),
    );

    //Moodle Access Token
    $dc_woodle_options[ ] = array(
        "type" => "textbox",
        "id" => "moodle-access-token",
        "name" => "moodle_access_token",
        "label" => __( "Moodle Access Token", 'dc-woodle' ),
        "desc" => __('Enter Moodle Access Token', 'dc-woodle' ),
    );

    
    /**
     * Create new menus
     */

    $dc_woodle_options[ ] = array(
        "type" => "menu",
        "menu_type" => "add_menu_page",
        "page_name" => __( "Synchronization", 'dc-woodle' ),
        "menu_slug" => "dc-woodle-synchronization",
        "layout" => "2-col"
    );

    $dc_woodle_options[ ] = array(
        "type" => "tab",
        "id" => "dc-woodle-courses",
        "label" => __( "Individual Courses", 'dc-woodle' ),
        "font_class" => "fa-user"
    );

    $dc_woodle_options[ ] = array(
        "type" => "setting",
        "id" => "dc_woodle_synchronize_settings"
    );
 
    $dc_woodle_options[ ] = array(
        "type" => "section",
        "id" => "dc-woodle-sync-courses",
        "label" => __( "Synchronize Courses", 'dc-woodle' )
    );
    
    // synchronize courses categories
    $dc_woodle_options[ ] = array(
        "type" => "checkbox",
        "id" => "sync-course-category",
        "name" => "sync_courses_category",
        "label" => __( "Category Synchronization Option", 'dc-woodle' ),
        "desc" => __('If enabled categories will be synchronized from moodle', 'dc-woodle' ),
        "option_values" => array(
             'Enable' => __( '', 'dc-woodle' ),
        )
    );

    //synchonise courses 
    $dc_woodle_options[ ] = array(
        "type" => "checkbox",
        "id" => "draft-courses",
        "name" => "draft_courses",
        "label" => __( "Course Synchronization Option", 'dc-woodle' ),
        "desc" => __('If enabled courses will be synchronized from moodle', 'dc-woodle' ),
        "option_values" => array(
             'Enable' => __( '', 'dc-woodle' ),
        )
    );

    // Product Synchronization
    $dc_woodle_options[ ] = array(
        "type" => "section",
        "id" => "dc-woodle-sync-products",
        "label" => __( "Synchronize Products", 'dc-woodle' )
    );


    // synchronize products
    $dc_woodle_options[ ] = array(
        "type" => "checkbox",
        "id" => "sync-products",
        "name" => "sync_products",
        "label" => __( "Synchronization Option", 'dc-woodle' ),
        "desc" => __('If enabled products will be created while syncing courses from moodle.', 'dc-woodle' ),
        "option_values" => array(
             'Enable' => __( '', 'dc-woodle' ),
        )
    );

  	return apply_filters( 'dc_woodle_fileds_options', $dc_woodle_options);
	}
}
