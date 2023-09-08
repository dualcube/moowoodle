<?php
use block_mockblock\search\area;
class MooWoodle_Library {
    public function moowoodle_get_options() {
        global $MooWoodle;
        $conn_settings = get_option('moowoodle_general_settings');
        $url = isset($conn_settings['moodle_url']) ? $conn_settings['moodle_url'] : '';
        $pro_sticker = '';
        if ($url != null) {
            $moodle_tokens_url = '<a href="' . $url . 'admin/webservice/tokens.php"> ' . __('Manage tokens', MOOWOODLE_TEXT_DOMAIN) . '</a>';
            $moodle_sso_url = '<a href="' . $url . 'admin/settings.php?section=auth_moowoodleconnect"> ' . __('Moodle', MOOWOODLE_TEXT_DOMAIN) . '</a>';
        } else {
            $moodle_tokens_url = __('Manage tokens', MOOWOODLE_TEXT_DOMAIN);
            $moodle_sso_url = __('Moodle', MOOWOODLE_TEXT_DOMAIN);
        }
        if ($MooWoodle->moowoodle_pro_adv) {
            $pro_sticker = '<span class="mw-pro-tag">Pro</span>';
        }
        $moowoodle_options = array(
            "menu" => array(
                "moowoodle" => array(
                    "name" => __("All Courses", MOOWOODLE_TEXT_DOMAIN),
                    "menu_type" => "add_menu_page",
                    "page_name" => __("Courses", MOOWOODLE_TEXT_DOMAIN),
                    "layout" => "2-col",
                    "tabs" => array(
                        "moowoodle-linked-courses" => array(
                            "label" => __("Moodle Courses", MOOWOODLE_TEXT_DOMAIN),
                            "font_class" => "dashicons-welcome-learn-more",
                            "setting" => "moowoodle_course_settings",
                            "section" => array(
                                "moowoodle-link-course-table" => array(
                                    "label" => __("Courses", MOOWOODLE_TEXT_DOMAIN),
                                    "field_types" => array(
                                        "all_course_nolabel" => array(
                                            "type" => "all-course-nolabel",
                                            "label" => __("", MOOWOODLE_TEXT_DOMAIN),
                                            "desc" => __("", MOOWOODLE_TEXT_DOMAIN),
                                            "option_values" => array(
                                                'Enable' => __('', MOOWOODLE_TEXT_DOMAIN),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                "moowoodle-manage-enrolment" => array(
                    "name" => __("Manage Enrolment", MOOWOODLE_TEXT_DOMAIN) . $pro_sticker,
                    "menu_type" => "add_menu_page",
                    "page_name" => __("MooWoodle", MOOWOODLE_TEXT_DOMAIN),
                    "layout" => "2-col",
                    "tabs" => array(
                        "moowoodle-manage-enrolment" => array(
                            "label" => __("Enrolment", MOOWOODLE_TEXT_DOMAIN),
                            "font_class" => "dashicons-analytics",
                            "setting" => "manage_enrolmente_settings",
                            "section" => array(
                                "moowoodle-manage-enrolment" => array(
                                    "label" => __("All Enrolments", MOOWOODLE_TEXT_DOMAIN),
                                    "field_types" => array(
                                        "manage_enrolment_nolabel" => array(
                                            "type" => "manage-enrolment-nolabel",
                                            "is_pro" => "pro",
                                            "label" => __("", MOOWOODLE_TEXT_DOMAIN),
                                            "desc" => __("", MOOWOODLE_TEXT_DOMAIN),
                                            "option_values" => array(
                                                'Enable' => __('', MOOWOODLE_TEXT_DOMAIN),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                "moowoodle-settings" => array(
                    "menu_type" => "add_menu_page",
                    "name" => __("Settings", MOOWOODLE_TEXT_DOMAIN),
                    "page_name" => __("MooWoodle", MOOWOODLE_TEXT_DOMAIN),
                    "layout" => "2-col",
                    "tabs" => array(
                        "moowoodle-general" => array(
                            "label" => __("General", MOOWOODLE_TEXT_DOMAIN),
                            "font_class" => "dashicons-admin-generic",
                            "submit_btn_value" => __('Save All Changes', MOOWOODLE_TEXT_DOMAIN),
                            "setting"   => "moowoodle_general_settings",
                            "section" => array(
                                "moowoodle-connection" => array(
                                    "label" => __("Connection Settings", MOOWOODLE_TEXT_DOMAIN),
                                    "field_types" => array(
                                        "moodle-url" => array(
                                            "type" => "textbox",
                                            'name' => "moodle_url",
                                            "label" => __("Moodle Site URL", MOOWOODLE_TEXT_DOMAIN),
                                            "desc" => __('Enter the Moodle Site URL', MOOWOODLE_TEXT_DOMAIN),
                                        ),
                                        "moodle-access-token" => array(
                                            "type" => "textbox",
                                            "name" => "moodle_access_token",
                                            "label" => __("Moodle Access Token", MOOWOODLE_TEXT_DOMAIN),
                                            "desc" => __('Enter Moodle Access Token. You can generate the Access Token from - Dashboard => Site administration => Server => Web services => ' . $moodle_tokens_url, MOOWOODLE_TEXT_DOMAIN),
                                        ),
                                        "test_connect_nolabel" => array(
                                            "type" => "test-connect-nolabel",
                                            "label" => __("Mooowoodle Test Connection", MOOWOODLE_TEXT_DOMAIN),
                                            "desc" => __("", MOOWOODLE_TEXT_DOMAIN),
                                            "option_values" => array(
                                                'Enable' => __('', MOOWOODLE_TEXT_DOMAIN),
                                            ),
                                        ),
                                    ),
                                ),
                                "moowoodle-user-information" => array(
                                    "label" => __("User Information Exchange Settings", MOOWOODLE_TEXT_DOMAIN),
                                    "field_types" => array(
                                        "update-moodle-user" => array(
                                            "type" => "toggle-checkbox",
                                            "name" => "update_moodle_user",
                                            "label" => __("Force Override Moodle User Profile

", MOOWOODLE_TEXT_DOMAIN),
                                            "desc" => __('If enabled, all moodle user\'s profile data (first name, last name, city, address, etc.) will be updated as per their wordpress profile data. Explicitly, for existing user, their data will be overwritten on moodle.', MOOWOODLE_TEXT_DOMAIN),
                                            "option_values" => array(
                                                'Enable' => __('', MOOWOODLE_TEXT_DOMAIN),
                                            ),
                                        ),
                                    ),
                                ),
                                "moowoodle-system-settings" => array(
                                    "label" => __("System Settings", MOOWOODLE_TEXT_DOMAIN),
                                    "field_types" => array(
                                        "moodle-timeout" => array(
                                            "type" => "textbox",
                                            "name" => "moodle_timeout",
                                            "label" => __("Timeout", MOOWOODLE_TEXT_DOMAIN),
                                            "desc" => __('Set Curl connection time out in sec.', MOOWOODLE_TEXT_DOMAIN),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                        "moowoodle-display" => array(
                            "id" => "moowoodle-display",
                            "label" => __("Display", MOOWOODLE_TEXT_DOMAIN),
                            "font_class" => "dashicons-welcome-view-site",
                            "submit_btn_value" => __('Save All Changes', MOOWOODLE_TEXT_DOMAIN),
                            "setting" => "moowoodle_display_settings",
                            "section" => array(
                                "moowoodle-display" => array(
                                    "label" => __("Display Settings", MOOWOODLE_TEXT_DOMAIN),
                                    "field_types" => array(
                                        "start-end-date" => array(
                                            "type" => "toggle-checkbox",
                                            "name" => "start_end_date",
                                            "label" => __("Display Start Date and End Date in Shop Page", MOOWOODLE_TEXT_DOMAIN),
                                            "desc" => __('If enabled display start date and end date in shop page.', MOOWOODLE_TEXT_DOMAIN),
                                            "option_values" => array(
                                                'Enable' => __('', MOOWOODLE_TEXT_DOMAIN),
                                            ),
                                        ),
                                        "my-courses-priority" => array(
                                            "type" => "select",
                                            "name" => "my_courses_priority",
                                            "label" => __("My Courses Menu Position", MOOWOODLE_TEXT_DOMAIN),
                                            "desc" => __('Select below which menu the My Courses Menu will be displayed', MOOWOODLE_TEXT_DOMAIN),
                                            "option_values" => get_account_menu_items(),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                        "moowoodle-SSO" => array(
                            "is_pro" => "pro",
                            "label" => __("SSO ", MOOWOODLE_TEXT_DOMAIN),
                            "font_class" => "dashicons-admin-multisite",
                            "submit_btn_value" => __('Save All Changes', MOOWOODLE_TEXT_DOMAIN),
                            "setting" => "moowoodle_sso_settings",
                            "section" => array(
                                "moowoodlepro-sso" => array(
                                    "label" => __("Single Sing On Settings", 'moowoodle'),
                                    "field_types" => array(
                                        "moowoodlepro-sso-eneble" => array(
                                            "type" => "toggle-checkbox",
                                            'name' => "moowoodlepro_sso_eneble",
                                            "is_pro" => "pro",
                                            "label" => __("Single Sing On", 'moowoodle'),
                                            "desc" => __('If enabled Moodle user\'s will login by WordPress user', 'moowoodle'),
                                            "option_values" => array(
                                                'Enable' => __('', MOOWOODLE_TEXT_DOMAIN),
                                            ),
                                        ),
                                        "moowoodlepro-sso-sskey" => array(
                                            "type" => "textbox",
                                            "name" => "moowoodlepro_sso_sskey",
                                            "is_pro" => "pro",
                                            "copy_text" => "copy",
                                            "label" => __("SSO Secrate Key", MOOWOODLE_TEXT_DOMAIN),
                                            "desc" => __('Enter SSO Secrate Key it should be same as ' . $moodle_sso_url . ' SSO Secrate Key', MOOWOODLE_TEXT_DOMAIN),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                        "moowoodle-log" => array(
                            "id" => "moowoodle-log",
                            "label" => __("Log", MOOWOODLE_TEXT_DOMAIN),
                            "font_class" => "dashicons-welcome-write-blog",
                            "setting" => "moowoodle_log",
                            "section" => array(
                                "moowoodle-log-table" => array(
                                    "label" => __("Log", MOOWOODLE_TEXT_DOMAIN),
                                    "field_types" => array(
                                        "log_nolabel" => array(
                                            "type" => "log-nolabel",
                                            "label" => __("", MOOWOODLE_TEXT_DOMAIN),
                                            "desc" => __("", MOOWOODLE_TEXT_DOMAIN),
                                            "option_values" => array(
                                                'Enable' => __('', MOOWOODLE_TEXT_DOMAIN),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                "moowoodle-synchronization" => array(
                    "menu_type" => "add_menu_page",
                    "name" => __("Synchronization", MOOWOODLE_TEXT_DOMAIN),
                    "page_name" => __("Synchronization", MOOWOODLE_TEXT_DOMAIN),
                    "layout" => "2-col",
                    "tabs" => array(
                        "moowoodle-courses-sync" => array(
                            "label" => __("Course Synchronization", MOOWOODLE_TEXT_DOMAIN),
                            "font_class" => "dashicons-admin-links",
                            "submit_btn_value" => __('Sync Now', MOOWOODLE_TEXT_DOMAIN),
                            "submit_btn_name" => __('syncnow', MOOWOODLE_TEXT_DOMAIN),
                            "setting" => "moowoodle_synchronize_settings",
                            "section" => array(
                                "moowoodle-sync-courses" => array(
                                    "label" => __("Synchronization Settings", MOOWOODLE_TEXT_DOMAIN),
                                    "field_types" => array(
                                        "sync-options" => array(
                                            "type" => "multiple-checkboxs",
                                            "label" => __("Synchronize Options", MOOWOODLE_TEXT_DOMAIN),
                                            "desc" => __("Choose the category you wish to synchronize from Moodle to your WordPress site. During synchronization, if a course is found deleted in Moodle, it will likewise remove the corresponding course and product data from WordPress.", MOOWOODLE_TEXT_DOMAIN),
                                            "option_values" => array(
                                                'Moodle Courses' => array(
                                                    "id" => "sync-courses",
                                                    "name" => "sync_courses",
                                                    "desc" => __("This function will retrieve all Moodle course data and synchronize it with the courses listed in WordPress.", MOOWOODLE_TEXT_DOMAIN),
                                                    "checked" => "forced",
                                                ),
                                                'Moodle Course Categories' => array(
                                                    "id" => "sync-course-category",
                                                    "name" => "sync_courses_category",
                                                    "desc" => __("This feature will scan the entire Moodle course category structure and synchronize it with the WordPress category listings.", MOOWOODLE_TEXT_DOMAIN),
                                                ),
                                                'Create New Products' => array(
                                                    "id" => "sync-products",
                                                    "name" => "sync_products",
                                                    "desc" => __("This functionality enables automatic creation of new products based on Moodle course data if they do not already exist in WordPress.", MOOWOODLE_TEXT_DOMAIN),
                                                ),
                                                'Update Existing Products' => array(
                                                    "id" => "sync-prev-courses",
                                                    "name" => "sync_prev_courses",
                                                    "desc" => __("This feature allows you to update previously created product information using Moodle course data. NOTE: This action will overwrite all existing product details with those from Moodle course details.", MOOWOODLE_TEXT_DOMAIN),
                                                    "is_pro" => "pro",
                                                ),
                                                'Course Images' => array(
                                                    "id" => "sync-image",
                                                    "name" => "sync_image",
                                                    "desc" => __("This function copies course images and sets them as WooCommerce product images.", MOOWOODLE_TEXT_DOMAIN),
                                                    "is_pro" => "pro",
                                                ),
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
        return apply_filters('moowoodle_fileds_options', $moowoodle_options);
    }
}
