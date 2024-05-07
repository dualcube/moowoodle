import { __ } from '@wordpress/i18n';

export default {
    id: "sso",
    priority: 30,
    name: __("SSO ", 'moowoodle'),
    desc: __("SSO ", 'moowoodle'),
    icon: "font-mail",
    submitUrl: "save-moowoodle-setting",
    modal: [
        {
            key: "moowoodle_sso_eneble",
            type: "checkbox",
            desc: __("Single Sing On", 'moowoodle'),
            label: __('If enabled Moodle user\'s will login by WordPress user', 'moowoodle'),
            options: [
                {
                    key: "moowoodle_sso_eneble",
                    label:  __('Enable', 'moowoodle'), 
                    value: "moowoodle_sso_eneble"
                }
            ]
        },
        {
            key: "moowoodle_sso_secret_key",
            type: "text",
            desc: __(`Enter SSO Secret Key it should be same as  ${ appLocalizer.moodle_sso_url } SSO Secret Key`, 'moowoodle'),
            label: __("SSO Secret Key", 'moowoodle'),
        }
    ]
};