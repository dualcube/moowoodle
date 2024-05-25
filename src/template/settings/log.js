import { __ } from '@wordpress/i18n';

export default {
    id: 'log',
    priority: 60,
    name: __("Log", "moowoodle"),
    desc: __("Review all system logs and errors", "moowoodle"),
    icon: 'font-support',
    submitUrl: "save-moowoodle-setting",
    modal: [
        {
            key: "moowoodle_adv_log",
            type: "log",
            classes: "log-section",
        },
    ]
};
