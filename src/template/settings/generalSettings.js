import { __ } from '@wordpress/i18n';

export default {
    id: "general",
    priority: 10,
    name: __("General", 'moowoodle'),
    desc: __("Configure Moodle connection with WordPress", 'moowoodle'),
    icon: "font-mail",
    submitUrl: "save-moowoodle-setting",
    modal: [
        {
            key: "moodle_url",
            type: "text",
            desc: __('Enter the Moodle Site URL', 'moowoodle'),
            label: __("Moodle Site URL", 'moowoodle'),
        },
        {
            key: "moodle_access_token",
            type: "text",
            label: __("Moodle Access Token", 'moowoodle'),
            desc: __(`Enter Moodle Access Token. You can generate the Access Token from - Dashboard => Site administration => Server => Web services => ${ appLocalizer.moodle_tokens_url }`, 'moowoodle'),
        },
        {
            key: "test_connection",
            type: "testconnection",
            desc: __(`Refer to the ${ appLocalizer.setupguide } to complete all necessary configurations on the Moodle site, and subsequently, perform a Test Connection to verify the functionality of all services.`, 'moowoodle'),
            label: __("MooWoodle Test Connection", 'moowoodle'),
        },
        {
            key: "moodle_timeout",
            type: "text",
            desc: __('Set Curl connection time out in sec.', 'moowoodle'),
            label: __("Timeout", 'moowoodle'),
        },
    ]
};