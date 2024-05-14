import { __ } from '@wordpress/i18n';

export default {
    id: "synchronize-course",
    priority: 20,
    name: __("Course", 'moowoodle'),
    desc: __("Control course sync direction and schedule interval", 'moowoodle'),
    icon: "font-mail",
    submitUrl: "save-moowoodle-setting",
    proDependent: true,
    modal: [
        {
            key: "course_sync_direction",
            type: "checkbox-default",
            // desc: __("<b>Prior to updating existing course info, you must select the course info to be synchronized at </b>", 'moowoodle') . $moowoodle_sync_setting_url . __("<br><br>While synchronizing user information, we use the email address as the unique identifier for each user. We check the username associated with that email address, and if we find the same username in the other instance but with a different email address, the user's information cannot be synchronized.", 'moowoodle'),
            label: __("Sync Direction", 'moowoodle'),
            options: [
                {
                    key: "moodle_to_wordpress",
                    label: __('Moodle to Wordpress', 'moowoodle'),
                    value: "moodle_to_wordpress",
                }
            ],
            proSetting: true,
        },
        {
            key: "course_schedule_interval",
            type: "select",
            desc: __("Select Option For Course Synchronization Schedule Interval.", 'moowoodle'),
            label: __("Course Synchronization Schedule Interval", 'moowoodle'),
            options: [
                {
                    key: "realtime",
                    label: __("Realtime", 'moowoodle'),
                    value: "realtime",
                },
                {
                    key: "hour",
                    label: __("Hourly.", 'moowoodle'),
                    value: "hour",
                },
                {
                    key: "hour6",
                    label: __("Hour 6.", 'moowoodle'),
                    value: "hour6",
                },
                {
                    key: "day",
                    label: __("Daily", 'moowoodle'),
                    value: "day",
                },
                {
                    key: "week",
                    label: __("Weekly", 'moowoodle'),
                    value: "week",
                },
                {
                    key: "month",
                    label: __("Month", 'moowoodle'),
                    value: "month",
                }
            ],
            proSetting: true,
        },
        {
            key: "course_sync_action",
            type: "checkbox-default",
            desc: __("Action Required", 'moowoodle'),
            label: __("Action Required", 'moowoodle'),
            options: [
                {
                    key: "create_update",
                    label: __('Create and Update Products', 'moowoodle'),
                    hints: __('This feature allows you to update previously created product information using Moodle course data. NOTE: This action will overwrite all existing product details with those from Moodle course details.', 'moowoodle'),
                    value: "create_update",
                },
                {
                    key: "create",
                    label: __('Create Products', 'moowoodle'),
                    hints: __('This functionality enables automatic creation of new products based on Moodle course data if they do not already exist in WordPress.', 'moowoodle'),
                    value: "create",
                },
                {
                    key: "update",
                    label: __('Update Products', 'moowoodle'),
                    hints: __('This feature allows you to update previously created product information using Moodle course data. NOTE: This action will overwrite all existing product details with those from Moodle course details.', 'moowoodle'),
                    value: "update",
                }
            ],
            proSetting: true,
        },
        {
            key: "sync-course-options",
            type: "checkbox-default",
            desc: __("Select Option For Course Sync.", 'moowoodle'),
            label: __("Course Information", 'moowoodle'),
            select_deselect: true,
            options: [
                {
                    key: "sync_courses",
                    label: __( 'Moodle Courses', 'moowoodle' ),
                    hints: __("This function will retrieve all Moodle course data and synchronize it with the courses listed in WordPress.", 'moowoodle'),
                    value: "sync_courses",
                },
                {
                    key: "sync_courses_category",
                    label: __( 'Moodle Course Categories', 'moowoodle' ),
                    hints: __("This feature will scan the entire Moodle course category structure and synchronize it with the WordPress category listings.", 'moowoodle'),
                    value: "sync_courses_category",
                },
                {
                    key: "sync_image",
                    label: __( 'Course Images', 'moowoodle' ),
                    hints: __("This function copies course images and sets them as WooCommerce product images.", 'moowoodle'),
                    value: "sync_image",
                    proSetting: true,
                },
            ]
        },
        {
            key: "sync_course_btn",
            type: "syncbutton",
            label: __("Sync course", 'moowoodle'),
        },
    ]
};