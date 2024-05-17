import { __ } from '@wordpress/i18n';

export default {
    id: "general",
    priority: 10,
    name: __("General", 'moowoodle'),
    desc: __("Effortlessly configure and verify your WordPress-Moodle connection.", 'moowoodle'),
    icon: "font-mail",
    submitUrl: "save-moowoodle-setting",
    modal: [
        {
            key: "moodle_url",
            type: "text",
            desc: __('Provide the URL of your Moodle site where the course will be hosted. Students will receive access to the course content on that site.', 'moowoodle'),
            label: __("Moodle site URL", 'moowoodle'),
        },
        {
            key: "moodle_access_token",
            type: "text",
            label: __("Moodle access token", 'moowoodle'),
            desc: __(`Enter Moodle access token. You can generate the access token from - Dashboard => Site administration => Server => Web services => <a href="${appLocalizer.moodle_tokens_url}" target="_blank">Here</a>`, 'moowoodle'),
        },
        {
            key: "test_connection",
            type: "testconnection",
            desc: __(`Refer to the ${ appLocalizer.setupguide } to complete all necessary configurations on the Moodle site, and subsequently, perform a Test Connection to verify the functionality of all services.`, 'moowoodle'),
            label: __("MooWoodle test connection", 'moowoodle'),
        },
        {
            key: 'separator_content',
            type: 'section',
            label: "",
        },
        {
            key: "moodle_timeout",
            type: "text",
            desc: __('When WordPress sends a request to the Moodle server for data, communication delays might exceed the server connection timeout. You can customize the timeout parameters by adjusting them here. The timeout duration is measured in seconds (Default is 5 seconds). ', 'moowoodle'),
            label: __("Connection timeout", 'moowoodle'),
        },
    ]
};
