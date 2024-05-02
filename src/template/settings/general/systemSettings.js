import { __ } from '@wordpress/i18n';

export default {
    id: "system",
    priority: 25,
    name: __("System Settings", 'moowoodle'),
    desc: __("System Settings", 'moowoodle'),
    icon: "font-mail",
    submitUrl: "save-moowoodle-setting",
    modal: [
        {
            key: "moodle_timeout",
            type: "text",
            desc: __('Set Curl connection time out in sec.', 'moowoodle'),
            label: __("Timeout", 'moowoodle'),
        },
        {
            key: "moowoodle_adv_log",
            type: "checkbox",
            label: __("Advance Log", 'moowoodle'),
            desc: __('These setting will record all advanced error informations. Please don\'t Enable it if not required, because it will create a large log file.', 'moowoodle'),
            options: [
                {
                    key: "moowoodle_adv_log",
                    label:  __('Enable', 'moowoodle'), 
                    value: "moowoodle_adv_log"
                }
            ]
        },
    ]
};