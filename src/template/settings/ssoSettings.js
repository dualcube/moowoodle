import { __ } from '@wordpress/i18n';

export default {
    id: "sso",
    priority: 30,
    name: __("Single Sign On", 'moowoodle'),
    desc: __("Manage seamless login and logout synchronization ", 'moowoodle'),
    icon: "font-mail",
    submitUrl: "save-moowoodle-setting",
    proDependent: true,
    modal: [
        {
            key: "moowoodle_sso_enable",
            type: "checkbox",
            desc: __("Enabling this option allows users to access Moodle courses directly, bypassing the need for login ", 'moowoodle'),
            label: __('Single Sign On', 'moowoodle'),
            options: [
                {
                    key: "moowoodle_sso_enable",
                    value: "moowoodle_sso_enable"
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