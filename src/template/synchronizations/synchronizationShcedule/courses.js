import { __ } from '@wordpress/i18n';

export default {
    id: "synchronize-schedule-course",
    priority: 30,
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
                    key: 0,
                    label: __("Realtime", 'moowoodle'),
                    value: 0, // realtime is 0s
                },
                {
                    key: 3600,
                    label: __("Hourly.", 'moowoodle'),
                    value: 3600, // 1 hour is 36000s
                },
                {
                    key: 21600,
                    label: __("Hour 6.", 'moowoodle'),
                    value: 21600, // 6 hour is 21600
                },
                {
                    key: 86400,
                    label: __("Daily", 'moowoodle'),
                    value: 86400, // 1 day is 86400s
                },
                {
                    key: 604800,
                    label: __("Weekly", 'moowoodle'),
                    value: 604800, // 1 week is 604800s
                },
                {
                    key: 2592000,
                    label: __("Month", 'moowoodle'),
                    value: 2592000, // 1 month is 2592000s
                }
            ],
            proSetting: true,
        },
        {
            key: "product_sync_option",
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
        }
    ]
};