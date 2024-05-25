import { __ } from '@wordpress/i18n';

export default {
    id: 'tool',
    priority: 50,
    name: __("Tool", "moowoodle"),
    desc: __("Review all system logs and errors", "moowoodle"),
    icon: 'font-support',
    submitUrl: "save-moowoodle-setting",
    modal: [
        {
            key: "moodle_timeout",
            type: "text",
            desc: __('When WordPress sends a request to the Moodle server for data, communication delays might exceed the default server connection timeout. You can customize the timeout parameters by adjusting them here. <br>Default: 5 seconds. ', 'moowoodle'),
            label: __("Connection timeout", 'moowoodle'),
        },
        {
            key: "user_schedule_interval",
            type: "select-custom-radio",
            desc: __("Select the interval for the user synchronization process. Based on this schedule, the cron job will run to sync users between WordPress and Moodle.", 'moowoodle'),
            label: __("Automatic synchronization frequency", 'moowoodle'),
            options: [
                {
                    key: "perminute",
                    label: __("Per minute", 'moowoodle'),
                    value: "perminute",
                },
                {
                    key: "hourly",
                    label: __("Hourly", 'moowoodle'),
                    value: "hourly",
                },
                {
                    key: "six_hours",
                    label: __("In 6 hours", 'moowoodle'),
                    value: "six_hours",
                },
                {
                    key: "daily",
                    label: __("Daily", 'moowoodle'),
                    value: "daily",
                },
                {
                    key: "weekly",
                    label: __("Weekly", 'moowoodle'),
                    value: "weekly",
                },
                {
                    key: "monthly",
                    label: __("Monthly", 'moowoodle'),
                    value: "monthly",
                }
            ],
            proSetting: true,
        },
    ]
};