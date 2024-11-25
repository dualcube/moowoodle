import { __ } from '@wordpress/i18n';

export default {
    id: 'tool',
    priority: 50,
    name: __("Tools", "moowoodle"),
    desc: __("Review all system logs and errors", "moowoodle"),
    icon: 'adminLib-tools',
    submitUrl: "save-moowoodle-setting",
    modal: [
        {
            key: "moowoodle_adv_log",
            type: "checkbox",
            label: __("Advance Log", 'moowoodle'),
            desc: __(`<span class="highlighted-part">Activating this option will log more detailed error information. Enable it only when essential, as it may result in a larger log file.</span>`, 'moowoodle'),
            options: [
                {
                    key: "moowoodle_adv_log",
                    value: "moowoodle_adv_log"
                }
            ]
        },
        {
            key: 'separator_content',
            type: 'section',
            label: "",
        },
        {
            key: "moodle_timeout",
            type: "text",
            desc: __('When WordPress sends a request to the Moodle server for data, communication delays might exceed the default server connection timeout. You can customize the timeout parameters by adjusting them here. <br>Default: 5 seconds. ', 'moowoodle'),
            label: __("Connection timeout", 'moowoodle'),
            parameter: __("Seconds", 'moowoodle')
        },
        {
            key: "schedule_interval",
            type: "text",
            desc: __("Select the interval for the user synchronization process. Based on this schedule, the cron job will run to sync users between WordPress and Moodle.", 'moowoodle'),
            proSetting: true,
            parameter: __("Minutes", 'moowoodle'),
            label: __("Automatic synchronization frequency", 'moowoodle'),
        },
    ]
}