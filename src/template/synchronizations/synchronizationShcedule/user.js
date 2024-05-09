import { __ } from '@wordpress/i18n';

export default {
    id: "synchronize-shcedule-user",
    priority: 25,
    name: __("User", 'moowoodle'),
    desc: __("Admins can select user sync direction and schedule interval for user synchronization.", 'moowoodle'),
    icon: "font-mail",
    submitUrl: "save-moowoodle-setting",
    proDependent: true,
    modal: [
        {
            key: "user_sync_direction",
            type: "select",
            // desc: __("<b>Prior to updating existing user info, you must select the user info to be synchronized at </b>", 'moowoodle') . $moowoodle_sync_setting_url . __("<br><br>While synchronizing user information, we use the email address as the unique identifier for each user. We check the username associated with that email address, and if we find the same username in the other instance but with a different email address, the user's information cannot be synchronized.", 'moowoodle'),
            label: __("Sync Direction", 'moowoodle'),
            options: [
                {
                    key: "wordpress_to_moodle",
                    label: __('Wordpress to Moodle', 'moowoodle'),
                    value: "wordpress_to_moodle",
                },
                {
                    key: "moodle_to_wordpress",
                    label: __('Moodle to Wordpress', 'moowoodle'),
                    value: "moodle_to_wordpress",
                }
            ],
            proSetting: true,
        },
        {
            key: "user_schedule_interval",
            type: "select",
            desc: __("Select Option For User Synchronization Schedule Interval.", 'moowoodle'),
            label: __("Schedule Interval", 'moowoodle'),
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
        }
    ]
};