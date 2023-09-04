<?php
class MooWoodle_Library {

    
    
    public function moowoodle_get_options() {
        $conn_settings = get_option( 'moowoodle_general_settings' );
        $url = $conn_settings[ 'moodle_url' ];
   
//=============================
        /**
         * Create new menus
        */
        $moowoodle_options[ ] = array(
            "type" => "menu",
            "menu_type" => "add_menu_page",
            "page_name" => __( "Courses", 'moowoodle' ),
            "menu_slug" => "moowoodle",
            "layout" => "2-col"
        );

        $moowoodle_options[ ] = array(
            "type" => "tab",
            "id" => "moowoodle-linked-courses",
            "label" => __( "Moodle Courses", 'moowoodle' ),
            "font_class" => "dashicons-welcome-learn-more"
        );

        $moowoodle_options[ ] = array(
            "type" => "setting",
            "id" => "moowoodle_course_settings"
        );
     
        $moowoodle_options[ ] = array(
            "type" => "section",
            "id" => "moowoodle-link-course-table",
            "label" => __( "Courses", 'moowoodle' )
        );
        
        // courses page
        $moowoodle_options[ ] = array(
            "type" => "course_posttype",//table
            "id" => "course_posttype",
            "label" => __( "", 'moowoodle' ),
            "desc" => __("", '' ),
            "option_values" => array(
                 'Enable' => __( '', 'moowoodle' ),
            )
        );
//=============================
        /**
         * Create new menus
        */
        $moowoodle_options[ ] = array(
            "type" => "menu",
            "menu_type" => "add_menu_page",
            "page_name" => __( "Synchronization", 'moowoodle' ),
            "menu_slug" => "moowoodle-synchronization",
            "layout" => "2-col"
        );

        $moowoodle_options[ ] = array(
            "type" => "tab",
            "id" => "moowoodle-courses-sync",
            "label" => __( "Course Synchronization", 'moowoodle' ),
            "font_class" => "dashicons-admin-links"
        );

        $moowoodle_options[ ] = array(
            "type" => "setting",
            "id" => "moowoodle_synchronize_settings"
        );
     
        $moowoodle_options[ ] = array(

            "type" => "section",
            "id" => "moowoodle-sync-courses",
            "label" => __( "Synchronization Settings", 'moowoodle' )
        );

        $moowoodle_options[ ] = array(
            "type" => "multiple_checkboxs",
            "id" => "sync-options",
            "label" => __( "Synchronize Options", 'moowoodle' ),
            "desc" => __("Choose the category you wish to synchronize from Moodle to your WordPress site.", '' ),
            "option_values" => array(
                 'Courses'=> array(
                    "id" => "sync-courses",
                    "name" => "sync_courses",
                 ),
                 'Categories'=> array(
                    "id" => "sync-course-category",
                    "name" => "sync_courses_category",
                 ),
                 'Products'=> array(
                    "id" => "sync-products",
                    "name" => "sync_products",
                 ),
                 'Image'=> array(
                    "id" => "sync-image",
                    "name" => "sync_image",
                    "is_pro" => "pro",
                 ),
                 'Update Courses'=> array(
                    "id" => "sync-prev-courses",
                    "name" => "sync_prev_courses",
                    "is_pro" => "pro",
                 ),
            )
        );
//=============================
        /**
         * Create new menus
         */
        $moowoodle_options[ ] = array(
            "type" => "menu",
            "menu_type" => "add_menu_page",
            "page_name" => __( "MooWoodle", 'moowoodle' ),
            "menu_slug" => "moowoodle-manage-enrolment",
            "layout" => "2-col"
        );
        $moowoodle_options[ ] = array(
            "type" => "tab",
            "id" => "moowoodle-manage-enrolment",
            "label" => __( "Enrolment", 'moowoodle' ),
            "font_class" => "dashicons-analytics"
        );
         $moowoodle_options[ ] = array(
            "type" => "setting",
            "id" => "manage_enrolmente_settings"
        );
     
        $moowoodle_options[ ] = array(
            "type" => "section",
            "id" => "moowoodle-manage-enrolment",
            "label" => __( "All Enrolments", 'moowoodle' )
        );
        
        // manage enrolment page
        $moowoodle_options[ ] = array(
            "type" => "manage_enrolment_posttype",
            "id" => "manage_enrolment_posttype",
            "is_pro" => "pro",
            "label" => __( "", 'moowoodle' ),
            "desc" => __("", '' ),
            "option_values" => array(
                 'Enable' => __( '', 'moowoodle' ),
            )
        );
//=============================
        /**
         * Create new menus
         */
        $moowoodle_options[ ] = array(
            "type" => "menu",
            "menu_type" => "add_menu_page",
            "page_name" => __( "MooWoodle", 'moowoodle' ),
            "menu_slug" => "moowoodle-settings",
            "layout" => "2-col"
        );

        /**
         * Settings Tab
         */
        $moowoodle_options[ ] = array(
            "type" => "tab",
            "id" => "moowoodle-general",
            "label" => __( "General", 'moowoodle' ),
            "font_class" => "dashicons-admin-generic"
        );

        // setting box
        $moowoodle_options[ ] = array(
            "type" => "setting",
            "id" => "moowoodle_general_settings",
        );

        //Connection Settings
        $moowoodle_options[ ] = array(
            "type" => "section",
            "id" => "moowoodle-connection",
            "label" => __( "Connection Settings", 'moowoodle' ),
        );

        // Moodle URL
        $moowoodle_options[ ] = array(
            "type" => "textbox",
            "id" => "moodle-url",
            'name' => "moodle_url",
            "label" => __( "Moodle Site URL", 'moowoodle' ),
            "desc" => __('Enter the Moodle Site URL', 'moowoodle' ),
        );
        if($url != null){
            $moodle_tokens_url = '<a href="'.$url.'admin/webservice/tokens.php"> Manage tokens</a>';
        }
        else{
            $moodle_tokens_url = 'Manage tokens';
        }
        //Moodle Access Token
        $moowoodle_options[ ] = array(
            "type" => "textbox",
            "id" => "moodle-access-token",
            "name" => "moodle_access_token",
            "label" => __( "Moodle Access Token", 'moowoodle' ),
            "desc" => __('Enter Moodle Access Token. You can generate the Access Token from - Dashboard => Site administration => Server => Web services => '.$moodle_tokens_url, 'moowoodle' ),
        );
        //test connection massage page
        $moowoodle_options[ ] = array(
            "type" => "test_connect_posttype",
            "id" => "test_connect_posttype",
            "label" => __( "", 'moowoodle' ),
            "desc" => __("", '' ),
            "option_values" => array(
                 'Enable' => __( '', 'moowoodle' ),
            )
        );

        //Connection Settings
        $moowoodle_options[ ] = array(
            "type" => "section",
            "id" => "moowoodle-user-information",
            "label" => __( "User Information Settings", 'moowoodle' ),
        );

        // Update Moodle User
        $moowoodle_options[ ] = array(
            "type" => "toggle_checkbox",
            "id" => "update-moodle-user",
            'name' => "update_moodle_user",
            "label" => __( "Update Profile Data", 'moowoodle' ),
            "desc" => __('If activated, the personal information of Moodle users will be automatically refreshed to match the data of the corresponding WordPress users.' , 'moowoodle' ),
            "option_values" => array(
                 'Enable' => __( '', 'moowoodle' ),
            )
        );

        $moowoodle_options[ ] = array(
            "type" => "section",
            "id" => "moowoodle-system-settings",
            "label" => __( "System Settings", 'moowoodle' ),
        );
        // moodle time out
        $moowoodle_options[ ] = array(
            "type" => "textbox",
            "id" => "moodle-timeout",
            'name' => "moodle_timeout",
            "label" => __( "Timeout", 'moowoodle' ),
            "desc" => __('Adjust the timeout option in settings if slow server responses affect course synchronization or user registration in Moodle. Enter the CURL request timeout in sec. Default: 5 (in Sec).', 'moowoodle' ),
        );
        
        //display settings
        $moowoodle_options[ ] = array(
            "type" => "tab",
            "id" => "moowoodle-display",
            "label" => __( "Display", 'moowoodle' ),
            "font_class" => "dashicons-welcome-view-site"
        );

        // setting box
        $moowoodle_options[ ] = array(
            "type" => "setting",
            "id" => "moowoodle_display_settings",
        );

        //Connection Settings
        $moowoodle_options[ ] = array(
            "type" => "section",
            "id" => "moowoodle-display",
            "label" => __( "Display Settings", 'moowoodle' ),
        );

        //start and end date 
        $moowoodle_options[ ] = array(
            "type" => "toggle_checkbox",
            "id" => "start-end-date",
            "name" => "start_end_date",
            "label" => __( "Display Start Date and End Date in Shop Page", 'moowoodle' ),
            "desc" => __('If enabled display start date and end date in shop page.', 'moowoodle' ),
            "option_values" => array(
                 'Enable' => __( '', 'moowoodle' ),
            )
        );

        //my-courses on display tab
        $moowoodle_options[ ] = array(
            "type" => "select",
            "id" => "my-courses-priority",
            "name" => "my_courses_priority",
            "label" => __( "My Courses Menu Position", 'moowoodle' ),
            "desc" => __('Select below which menu the My Courses Menu will be displayed', 'moowoodle'),
            "option_values" => get_account_menu_items()

        );
        $moowoodle_options[ ] = array(
            "type" => "tab",
            "id" => "moowoodle-SSO",
            "is_pro" => "pro",
            "label" => __( "SSO ", 'moowoodle' ),
            "font_class" => "dashicons-admin-multisite"
        );
        $moowoodle_options[ ] = array(
            "type" => "setting",
            "id" => "moowoodle_sso_settings",
        );
        $moowoodle_options[ ] = array(
            "type" => "section",
            "id" => "moowoodlepro-sso",
            "label" => __( "Single Sing On Settings", 'moowoodlepro' ),
        );
        $moowoodle_options[ ] = array(
            "type" => "toggle_checkbox",
            "id" => "moowoodlepro-sso-eneble",
            'name' => "moowoodlepro_sso_eneble",
            "is_pro" => "pro",
            "label" => __( "Single Sing On", 'moowoodlepro' ),
            "desc" => __('If enabled Moodle user\'s will login by WordPress user' , 'moowoodlepro' ),
            "option_values" => array(
              'Enable' => __( '', 'moowoodle' ),
            )
        );
        if($url != null){
            $moodle_sso_url = '<a href="'.$url.'admin/settings.php?section=auth_moowoodleconnect"> Moodle</a>';
        }
        else{
            $moodle_sso_url = 'Moodle';
        }
        $moowoodle_options[ ] = array(
            "type" => "textbox",
            "id" => "moowoodlepro-sso-sskey",
            "name" => "moowoodlepro_sso_sskey",
            "is_pro" => "pro",
            "label" => __( "SSO Secrate Key", 'moowoodle' ),
            "desc" => __('Enter SSO Secrate Key it should be same as '.$moodle_sso_url.' SSO Secrate Key', 'moowoodle' ),
        );
        // log page
        
        // log
        $moowoodle_options[ ] = array(
            "type" => "tab",
            "id" => "moowoodle-log",
            "label" => __( "Log", 'moowoodle' ),
            "font_class" => "dashicons-welcome-write-blog"
        );

        $moowoodle_options[ ] = array(
            "type" => "setting",
            "id" => "moowoodle_log"
        );
     
        $moowoodle_options[ ] = array(
            "type" => "section",
            "id" => "moowoodle-log-table",
            "label" => __( "Log", 'moowoodle' )
        );
        
        // log page
        $moowoodle_options[ ] = array(
            "type" => "log_posttype",
            "id" => "log_posttype",
            "label" => __( "", 'moowoodle' ),
            "desc" => __("", '' ),
            "option_values" => array(
                 'Enable' => __( '', 'moowoodle' ),
            )
        );
        
        return apply_filters( 'moowoodle_fileds_options', $moowoodle_options);
    }
}