import { __ } from '@wordpress/i18n';

export default {
    id: "connection",
    priority: 15,
    name: __("Connection Settings", 'moowoodle'),
    desc: __("Connection Settings", 'moowoodle'),
    icon: "font-mail",
    submitUrl: "save-moowoodle-setting",
    modal: [
        {
            key: "moodle_url",
            type: "textarea",
            desc: __('Enter the Moodle Site URL', 'moowoodle'),
            label: __("Moodle Site URL", 'moowoodle'),
        },
        {
            key: "moodle_access_token",
            type: "textarea",
            label: __("Moodle Access Token", 'moowoodle'),
            desc: __(`Enter Moodle Access Token. You can generate the Access Token from - Dashboard => Site administration => Server => Web services => ${ appLocalizer.moodle_tokens_url }`, 'moowoodle'),
        },
        {
            key: "test_connection",
            type: "connectbutton",
            desc: __(`Refer to the ${ appLocalizer.setupguide } to complete all necessary configurations on the Moodle site, and subsequently, perform a Test Connection to verify the functionality of all services.`, 'moowoodle'),
            label: __("Mooowoodle Test Connection", 'moowoodle'),
        },
    ]
};