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
            key: "moowoodle_adv_log",
            type: "log",
            classes: "log-section",
        },
    ]
};
