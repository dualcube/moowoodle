import { __ } from '@wordpress/i18n';

export default {
    id: "sso",
    priority: 30,
    name: __("Single Sing On", 'moowoodle'),
    desc: __("Manage seamless login and logout synchronization ", 'moowoodle'),
    icon: "font-mail",
    submitUrl: "save-moowoodle-setting",
    proDependent: true,
    modal: [
        {
            key: "moowoodle_sso_eneble",
            type: "checkbox",
            desc: __("Single Sing On", 'moowoodle'),
            label: __('If enabled Moodle user\'s will login by WordPress user', 'moowoodle'),
            options: [
                {
                    key: "moowoodle_sso_eneble",
                    value: "moowoodle_sso_eneble"
                }
            ],
            proSetting: true,
        },
        {
            key: "moowoodle_sso_secret_key",
            type: "sso_key",
            desc: __(`Enter SSO Secret Key it should be same as  ${ appLocalizer.moodle_sso_url } SSO Secret Key`, 'moowoodle'),
            label: __("SSO Secret Key", 'moowoodle'),
            proSetting: true,
        }
    ]
};