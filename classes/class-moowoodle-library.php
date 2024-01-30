<?php
class MooWoodle_Library {
	public function moowoodle_get_options() {
		global $MooWoodle;
		$wp_user_sync_running_cron_batch = apply_filters('moowoodle_sync_wordpress_user_corn_info', '');
		$sync_wordpress_users = apply_filters('add_moowoodle_sync_wordpress_users_roles','');
		$conn_settings = get_option('moowoodle_general_settings');
		$url = isset($conn_settings['moodle_url']) ? $conn_settings['moodle_url'] : '';
		$pro_sticker = '';
		$woocom_new_user_mail = '<a href="'. site_url() .'/wp-admin/admin.php?page=wc-settings&tab=email&section=moowoodle_emails_new_enrollment">here!</a>';
		if ($url != null) {
			$moodle_tokens_url = '<a href="' . $url . 'admin/webservice/tokens.php"> ' . __('Manage tokens', 'moowoodle') . '</a>';
			$moodle_sso_url = '<a href="' . $url . 'admin/settings.php?section=auth_moowoodleconnect"> ' . __('Moodle', 'moowoodle') . '</a>';
			$moowoodle_sync_setting_url = '<a href="' . get_admin_url() . '/admin.php?page=moowoodle-synchronization&tab=moowoodle-sync-options"> ' . __('Synchronization Settings', 'moowoodle') . '</a>';
		} else {
			$moodle_tokens_url = __('Manage tokens', 'moowoodle');
			$moodle_sso_url = __('Moodle', 'moowoodle');
			$moowoodle_sync_setting_url = __('Moodle', 'moowoodle');
		}
		if ($MooWoodle->moowoodle_pro_adv) {
			$pro_sticker = '<span class="mw-pro-tag">Pro</span>';
		}
		$moowoodle_options = array(
			"menu" => array(
				"moowoodle" => array(
					"name" => __("All Courses", 'moowoodle'),
					"menu_type" => "add_menu_page",
					"page_name" => __("Courses", 'moowoodle'),
					"layout" => "2-col",
					"tabs" => array(
						"moowoodle-linked-courses" => array(
							"label" => __("Moodle Courses", 'moowoodle'),
							"font_class" => "dashicons-welcome-learn-more",
							"setting" => "moowoodle_course_settings",
							"section" => array(
								"moowoodle-link-course-table" => array(
									"label" => __("Courses", 'moowoodle'),
									"field_types" => array(
										"all_course_nolabel" => array(
											"type" => "all-course-nolabel",
											"label" => __("", 'moowoodle'),
											"desc" => __("", 'moowoodle'),
											"option_values" => array(
												'Enable' => __('', 'moowoodle'),
											),
										),
									),
								),
							),
						),
					),
				),
				"moowoodle-manage-enrolment" => array(
					"name" => __("Manage Enrolment", 'moowoodle') . $pro_sticker,
					"menu_type" => "add_menu_page",
					"page_name" => __("MooWoodle", 'moowoodle'),
					"layout" => "2-col",
					"tabs" => array(
						"moowoodle-manage-enrolment" => array(
							"label" => __("Enrolment", 'moowoodle'),
							"font_class" => "dashicons-analytics",
							"setting" => "manage_enrolmente_settings",
							"section" => array(
								"moowoodle-manage-enrolment" => array(
									"label" => __("All Enrolments", 'moowoodle'),
									"field_types" => array(
										"manage_enrolment_nolabel" => array(
											"type" => "manage-enrolment-nolabel",
											"is_pro" => "pro",
											"label" => __("", 'moowoodle'),
											"desc" => __("", 'moowoodle'),
											"option_values" => array(
												'Enable' => __('', 'moowoodle'),
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
					"name" => __("Settings", 'moowoodle'),
					"page_name" => __("MooWoodle", 'moowoodle'),
					"layout" => "2-col",
					"tabs" => array(
						"moowoodle-general" => array(
							"label" => __("General", 'moowoodle'),
							"font_class" => "dashicons-admin-generic",
							"submit_btn_value" => __('Save All Changes', 'moowoodle'),
							"setting" => "moowoodle_general_settings",
							"section" => array(
								"moowoodle-connection" => array(
									"label" => __("Connection Settings", 'moowoodle'),
									"field_types" => array(
										"moodle-url" => array(
											"type" => "textbox",
											'name' => "moodle_url",
											"label" => __("Moodle Site URL", 'moowoodle'),
											"desc" => __('Enter the Moodle Site URL', 'moowoodle'),
										),
										"moodle-access-token" => array(
											"type" => "textbox",
											"name" => "moodle_access_token",
											"label" => __("Moodle Access Token", 'moowoodle'),
											"desc" => __('Enter Moodle Access Token. You can generate the Access Token from - Dashboard => Site administration => Server => Web services => ' . $moodle_tokens_url, 'moowoodle'),
										),
										"test_connect_nolabel" => array(
											"type" => "test-connect-nolabel",
											"label" => __("Mooowoodle Test Connection", 'moowoodle'),
											"desc" => __("", 'moowoodle'),
											"option_values" => array(
												'Enable' => __('', 'moowoodle'),
											),
										),
									),
								),
								"moowoodle-user-information" => array(
									"label" => __("User Information Exchange Settings", 'moowoodle'),
									"field_types" => array(
										"update-moodle-user" => array(
											"type" => "toggle-checkbox",
											"name" => "update_moodle_user",
											"label" => __("Force Override Moodle User Profile", 'moowoodle'),
											"desc" => __('If enabled, all moodle user\'s profile data (first name, last name, city, address, etc.) will be updated as per their wordpress profile data. Explicitly, for existing user, their data will be overwritten on moodle.', 'moowoodle'),
											"option_values" => array(
												'Enable' => __('', 'moowoodle'),
											),
										),
									),
								),
								"moowoodle-system-settings" => array(
									"label" => __("System Settings", 'moowoodle'),
									"field_types" => array(
										"moodle-timeout" => array(
											"type" => "textbox",
											"name" => "moodle_timeout",
											"label" => __("Timeout", 'moowoodle'),
											"desc" => __('Set Curl connection time out in sec.', 'moowoodle'),
										),
										"moowoodle-adv-log" => array(
											"type" => "toggle-checkbox",
											"name" => "moowoodle_adv_log",
											"label" => __("Advance Log", 'moowoodle'),
											"desc" => __('These setting will record all advanced error informations. Please don\'t Enable it if not required, because it will create a large log file.', 'moowoodle'),
											"option_values" => array(
												'Enable' => __('', 'moowoodle'),
											),
										),
									),
								),
							),
						),
						"moowoodle-display" => array(
							"id" => "moowoodle-display",
							"label" => __("Display", 'moowoodle'),
							"font_class" => "dashicons-welcome-view-site",
							"submit_btn_value" => __('Save All Changes', 'moowoodle'),
							"setting" => "moowoodle_display_settings",
							"section" => array(
								"moowoodle-display" => array(
									"label" => __("Display Settings", 'moowoodle'),
									"field_types" => array(
										"start-end-date" => array(
											"type" => "toggle-checkbox",
											"name" => "start_end_date",
											"label" => __("Display Start Date and End Date in Shop Page", 'moowoodle'),
											"desc" => __('If enabled display start date and end date in shop page.', 'moowoodle'),
											"option_values" => array(
												'Enable' => __('', 'moowoodle'),
											),
										),
										"my-courses-priority" => array(
											"type" => "select",
											"name" => "my_courses_priority",
											"label" => __("My Courses Menu Position", 'moowoodle'),
											"desc" => __('Select below which menu the My Courses Menu will be displayed', 'moowoodle'),
											"option_values" => get_account_menu_items(),
										),
									),
								),
							),
						),
						"moowoodle-SSO" => array(
							"is_pro" => "pro",
							"label" => __("SSO ", 'moowoodle'),
							"font_class" => "dashicons-admin-multisite",
							"submit_btn_value" => __('Save All Changes', 'moowoodle'),
							"setting" => "moowoodle_sso_settings",
							"section" => array(
								"moowoodle-sso" => array(
									"label" => __("Single Sing On Settings", 'moowoodle'),
									"field_types" => array(
										"moowoodle-sso-eneble" => array(
											"type" => "toggle-checkbox",
											'name' => "moowoodle_sso_eneble",
											"is_pro" => "pro",
											"label" => __("Single Sing On", 'moowoodle'),
											"desc" => __('If enabled Moodle user\'s will login by WordPress user', 'moowoodle'),
											"option_values" => array(
												'Enable' => __('', 'moowoodle'),
											),
										),
										"moowoodle-sso-secret-key" => array(
											"type" => "textbox",
											"name" => "moowoodle_sso_secret_key",
											"is_pro" => "pro",
											"copy_text" => "copy",
											"label" => __("SSO Secret Key", 'moowoodle'),
											"desc" => __('Enter SSO Secret Key it should be same as ' . $moodle_sso_url . ' SSO Secret Key', 'moowoodle'),
										),
									),
								),
							),
						),
						"moowoodle-notification" => array(
							"is_pro" => "pro",
							"label" => __("Notification ", 'moowoodle'),
							"font_class" => "dashicons-bell",
							"submit_btn_value" => __('Save All Changes', 'moowoodle'),
							"setting" => "moowoodle_notification_settings",
							"section" => array(
								"moowoodle-notification" => array(
									"label" => __("Manage Notification", 'moowoodle'),
									"field_types" => array(
										"moowoodle-create-user-custom-mail-eneble" => array(
											"type" => "toggle-checkbox",
											'name' => "moowoodle_create_user_custom_mail",
											"is_pro" => "pro",
											"label" => __("Customize New User Registration Email", 'moowoodle'),
											"desc" => __('If this option is enabled, default WordPress new user registration emails will be disabled for both admin and user. Our custom New User Registration email will be sent to the newly registered user. You can personalize the content of the MooWoodle New User email from ', 'moowoodle') . $woocom_new_user_mail,
											"option_values" => array(
												'Enable' => __('', 'moowoodle'),
											),
										),
									),
								),
							),
						),
						"moowoodle-log" => array(
							"id" => "moowoodle-log",
							"label" => __("Log", 'moowoodle'),
							"font_class" => "dashicons-welcome-write-blog",
							"setting" => "moowoodle_log",
							"section" => array(
								"moowoodle-log-table" => array(
									"label" => __("Log", 'moowoodle'),
									"field_types" => array(
										"log_nolabel" => array(
											"type" => "log-nolabel",
											"label" => __("", 'moowoodle'),
											"desc" => __("", 'moowoodle'),
											"option_values" => array(
												'Enable' => __('', 'moowoodle'),
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
					"name" => __("Synchronization", 'moowoodle'),
					"page_name" => __("Synchronization", 'moowoodle'),
					"layout" => "2-col",
					"tabs" => array(
						"moowoodle-sync-options" => array(
							"label" => __("Synchronization Settings", 'moowoodle'),
							"font_class" => "dashicons-admin-links",
							"setting" => "moowoodle_synchronize_settings",
							"section" => array(
								"moowoodle-sync-settings" => array(
									"label" => __("Synchronization Options", 'moowoodle'),
									"field_types" => array(
										"sync-real-time-user-options" => array(
											"type" => "multiple-checkboxs",
											"label" => __("Realtime User Sync", 'moowoodle'),
											"desc" => __("Activate this feature for effortless user synchronization between Moodle and WordPress. As soon as a new user is added on one platform, our system dynamically syncs their profile to the other, accompanied by email notifications. This ensures users are promptly informed, creating a seamlessly unified experience across both platforms.", 'moowoodle') ,
											"option_values" => array(
												'Moodle &rArr; WordPress' => array(
													"id" => "realtime-sync-moodle-users",
													"name" => "realtime_sync_moodle_users",
													"desc" => __("", 'moowoodle'),
													"is_pro" => "pro",
												),
												'WordPress &rArr; Moodle' => array(
													"id" => "realtime-sync-wordpress-users",
													"name" => "realtime_sync_wordpress_users",
													"desc" => __("", 'moowoodle'),
													"is_pro" => "pro",
												),
												
											),
										),
										"sync-user-options" => array(
											"type" => "multiple-checkboxs",
											"label" => __("User Information", 'moowoodle'),
											"desc" => __("Determine User Information to Synchronize in Moodle-WordPress User synchronization. Please be aware that this setting does not apply to newly created users.", 'moowoodle'),
											"desc_posi" => "up",
											"note" => "<b>Note:</b> We're updating the WordPress password hashing method to ensure compatibility with Moodle. Rest assured, no user data is compromised, and this won't impact the login procedure on your site.",
											"option_values" => array(
												'First Name' => array(
													"id" => "sync-user-first-name",
													"name" => "sync_user_first_name",
													"desc" => __("", 'moowoodle'),
													"is_pro" => "pro",
												),
												'Last Name' => array(
													"id" => "sync-user-last-name",
													"name" => "sync_user_last_name",
													"desc" => __("", 'moowoodle'),
													"is_pro" => "pro",
												),
												'Username' => array(
													"id" => "sync-username",
													"name" => "sync_username",
													"desc" => __("", 'moowoodle'),
													"is_pro" => "pro",
												),
												'Password' => array(
													"id" => "sync-password",
													"name" => "sync_password",
													"desc" => __("", 'moowoodle'),
													"is_pro" => "pro",
												),
												
											),
										),
									),
								),
							),
						),
						"moowoodle-sync-now" => array(
							"label" => __("Synchronize Now", 'moowoodle'),
							"font_class" => "dashicons-admin-links",
							"setting" => "moowoodle_synchronize_now",
							"section" => array(
								"moowoodle-sync-now" => array(
									"label" => __("Synchronize Option", 'moowoodle'),
									"field_types" => array(
										"sync-all-user-options" => array(
											"type" => "select",
											"name" => "sync_users_now",
											"label" => __("Existing Users", 'moowoodle'),
											"desc" => __("<b>Prior to updating existing user info, you must seclect the user info to be synchronized at </b>", 'moowoodle') . $moowoodle_sync_setting_url . __("<br><br>While synchronizing user information, we use the email address as the unique identifier for each user. We check the username associated with that email address, and if we find the same username in the other instance but with a different email address, the user's information cannot be synchronized.", 'moowoodle') ,
											"desc_posi" => "up",
											"option_values" => array(
												'Moodle &rArr; WordPress' => array(
													"id" => "sync-moodle-users",
													"name" => "sync_moodle_users",
													"desc" => __("", 'moowoodle'),
													"is_pro" => "pro",
												),
												'WordPress &rArr; Moodle' => array(
													"id" => "sync-wordpress-users",
													"name" => "sync_wordpress_users",
													"desc" => $wp_user_sync_running_cron_batch . $sync_wordpress_users . __("To update passwords for users created before activating the plugin, they must log into WorPress site to migrate their passwords to the new hashing method. After this step, it will synchronize their passwords from WordPress to Moodle.If the user doesn't log in, all other fields will be synchronized except for the password.", 'moowoodle'),
													"is_pro" => "pro",
												),
											),
										),
										"sync-course-options" => array(
											"type" => "multiple-checkboxs",
											"label" => __("Courses", 'moowoodle'),
											"desc" => __("Choose the category you wish to synchronize from Moodle to your WordPress site. During synchronization, if a course is found deleted in Moodle, it will likewise remove the corresponding course and product data from WordPress.", 'moowoodle'),
											"option_values" => array(
												'Moodle Courses' => array(
													"id" => "sync-courses",
													"name" => "sync_courses",
													"desc" => __("This function will retrieve all Moodle course data and synchronize it with the courses listed in WordPress.", 'moowoodle'),
													"checked" => "forced",
												),
												'Moodle Course Categories' => array(
													"id" => "sync-course-category",
													"name" => "sync_courses_category",
													"desc" => __("This feature will scan the entire Moodle course category structure and synchronize it with the WordPress category listings.", 'moowoodle'),
												),
												'Create and Update Products' => array(
													"id" => "sync-all-product",
													"name" => "sync_all_product",
													"desc" => __("This feature allows you to update previously created product information using Moodle course data. NOTE: This action will overwrite all existing product details with those from Moodle course details.", 'moowoodle'),
												),
												'Create New Products' => array(
													"id" => "sync-new-products",
													"name" => "sync_new_products",
													"desc" => __("This functionality enables automatic creation of new products based on Moodle course data if they do not already exist in WordPress.", 'moowoodle'),
													"is_pro" => "pro",
												),
												'Update Existing Products' => array(
													"id" => "sync-exist-product",
													"name" => "sync_exist_product",
													"desc" => __("This feature allows you to update previously created product information using Moodle course data. NOTE: This action will overwrite all existing product details with those from Moodle course details.", 'moowoodle'),
													"is_pro" => "pro",
												),
												'Course Images' => array(
													"id" => "sync-image",
													"name" => "sync_image",
													"desc" => __("This function copies course images and sets them as WooCommerce product images.", 'moowoodle'),
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
