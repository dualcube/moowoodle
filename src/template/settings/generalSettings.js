import { __ } from '@wordpress/i18n';

export default {
    id: "general",
    priority: 10,
    name: __("General", 'moowoodle'),
    desc: __("Effortlessly configure and verify your WordPress-Moodle connection.", 'moowoodle'),
    icon: "adminLib-settings",
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
            desc: __(`Enter Moodle access token. You can generate the access token from <a href="${appLocalizer.moodle_site_url}admin/webservice/tokens.php" target="_blank">here</a>. <br> Navigation: Dashboard => Site administration => Server => Manage tokens. `, 'moowoodle'),
        },
        {
            key: "test_connection",
            type: "testconnection",
            desc: __(`Refer to the ${appLocalizer.setupguide} to complete all necessary configurations on the Moodle site, and subsequently, perform a Test Connection to verify the functionality of all services.`, 'moowoodle'),
            label: __("MooWoodle test connection", 'moowoodle'),
            apiLink: 'test-connection',
            tasks: [
                {
                    'action': 'get_site_info',
                    'message': __('Connecting to Moodle', 'moowoodle'),
                },
                {
                    'action': 'get_course',
                    'message': __('Courses Fetch', 'moowoodle'),
                    'cache': 'course_id',
                },
                {
                    'action': 'get_catagory',
                    'message': __('Catagory Fetch', 'moowoodle'),
                },
                {
                    'action': 'create_user',
                    'message': __('User Creation', 'moowoodle'),
                },
                {
                    'action': 'get_user',
                    'message': __('User Fetch', 'moowoodle'),
                    'cache': 'user_id',
                },
                {
                    'action': 'update_user',
                    'message': __('User Update', 'moowoodle'),
                },
                {
                    'action': 'enroll_user',
                    'message': __('User Enroll', 'moowoodle'),
                },
                {
                    'action': 'unenroll_user',
                    'message': __('User Unenroll', 'moowoodle'),
                },
                {
                    'action': 'delete_user',
                    'message': __('User Remove', 'moowoodle'),
                }
            ],
        },
    ]
};
