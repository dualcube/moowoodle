import { __ } from '@wordpress/i18n';

export default {
    id: "moowoodle-user-information",
    priority: 20,
    name: __("User Information Exchange Settings", 'moowoodle'),
    desc: __("User Information Exchange Settings", 'moowoodle'),
    icon: "font-mail",
    submitUrl: "save-moowoodle-setting",
    modal: [
        {
            key: "update_moodle_user",
            type: "checkbox",
            desc: __('If enabled, all moodle user\'s profile data (first name, last name, city, address, etc.) will be updated as per their wordpress profile data. Explicitly, for existing user, their data will be overwritten on moodle.', 'moowoodle'),
            label: __("Force Override Moodle User Profile", 'moowoodle'),
            options: [
                {
                    key: "update_moodle_user",
                    label:  __('Enable', 'moowoodle'), 
                    value: "update_moodle_user"
                }
            ]
        }
    ]
};