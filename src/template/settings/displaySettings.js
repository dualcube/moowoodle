import { __ } from '@wordpress/i18n';

export default {
    id: "display",
    priority: 20,
    name: __("Shop Central", 'moowoodle'),
    desc: __("Efficient Course Information Handling for customers.", 'moowoodle'),
    icon: "font-mail",
    submitUrl: "save-moowoodle-setting",
    modal: [
        {
            key: "start_end_date",
            type: "checkbox",
            desc: __('Enable this to allow the display of the course duration on the shop page', 'moowoodle'),
            label: __("Display course duration in Shop Page", 'moowoodle'),
            options: [
                {
                    key: "start_end_date",
                    label:  __('', 'moowoodle'), 
                    value: "start_end_date"
                }
            ]
        },
        {
            key: "my_courses_priority",
            type: "select",
            desc: __("Choose the location within the 'My Account' page where the 'My Course' menu will appear.", 'moowoodle'),
            label: __("Endpoint menu position - My Course", 'moowoodle'),
            options: Object.entries(appLocalizer.accountmenu).map(( [key, name ], index) => {
                return {
                    key: index,
                    label: name,
                    value: index
                }
            }),
        },
        {
            key: "moowoodle_create_user_custom_mail",
            type: "checkbox",
            desc: __(`If this option is enabled, default WordPress new user registration emails will be disabled for both admin and user. Our custom New User Registration email will be sent to the newly registered user. You can personalize the content of the MooWoodle New User email from ${ appLocalizer.woocom_new_user_mail }`, 'moowoodle'),
            label: __("Customize New User Registration Email", 'moowoodle'),
            options: [
                {
                    key: "moowoodle_create_user_custom_mail",
                    value: "moowoodle_create_user_custom_mail"
                }
            ],
        }
    ]
};