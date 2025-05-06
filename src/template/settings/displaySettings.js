import { __ } from '@wordpress/i18n';

export default {
    id: "display",
    priority: 20,
    name: __("Shop Central", 'moowoodle'),
    desc: __("Efficient course information handling for customers.", 'moowoodle'),
    icon: "adminLib-cart",
    submitUrl: "settings",
    modal: [
        {
            key: "start_end_date",
            type: "checkbox",
            desc: __('When enabled, the course duration, such as the start and end dates, will be visible on the shop page.', 'moowoodle'),
            label: __("Display course duration in Shop Page", 'moowoodle'),
            options: [
                {
                    key: "start_end_date",
                    label: __('', 'moowoodle'),
                    value: "start_end_date"
                }
            ],
            look: "toggle"
        },
        {
            key: "my_courses_priority",
            type: "select",
            desc: __("'<b>My Course</b>' menu will appear beneath the selected menu on the WooCommerce 'My Account' page of customer dashboard.", 'moowoodle'),
            label: __("Endpoint menu position - My Course", 'moowoodle'),
            options: Object.entries(appLocalizer.accountmenu).map(([key, name], index) => {
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
            desc: __(`If enabled, default WordPress new user registration emails will be disabled for both admin and user. <br>You can personalize the content of the MooWoodle New User email from  <a href="${appLocalizer.wc_email_url}" target="_blank">here.</a>`, 'moowoodle'),
            label: __("Disable new user registration email", 'moowoodle'),
            options: [
                {
                    key: "moowoodle_create_user_custom_mail",
                    value: "moowoodle_create_user_custom_mail"
                }
            ],
            look: "toggle"
        }
    ]
};
