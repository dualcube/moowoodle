import { __ } from '@wordpress/i18n';

export default {
    id: "moowoodle-notification",
    priority: 40,
    name: __("Notification ", 'moowoodle'),
    desc: __("Notification ", 'moowoodle'),
    icon: "font-mail",
    submitUrl: "save-moowoodle-setting",
    modal: [
        {
            key: "moowoodle_create_user_custom_mail",
            type: "checkbox",
            desc: __(`If this option is enabled, default WordPress new user registration emails will be disabled for both admin and user. Our custom New User Registration email will be sent to the newly registered user. You can personalize the content of the MooWoodle New User email from ${ appLocalizer.woocom_new_user_mail }`, 'moowoodle'),
            label: __("Customize New User Registration Email", 'moowoodle'),
            options: [
                {
                    key: "moowoodle_create_user_custom_mail",
                    label:  __('Enable', 'moowoodle'),
                    value: "moowoodle_create_user_custom_mail"
                }
            ]
        }
    ]
};