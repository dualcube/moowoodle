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
            desc: __('Select where the My Course menue will be displayed in My Account page.', 'moowoodle'),
            label: __("Enpoint menu position - My Course", 'moowoodle'),
            options: Object.entries(appLocalizer.accountmenu).map(( [key, name ], index) => {
                return {
                    key: index,
                    label: name,
                    value: index
                }
            }),
        }
    ]
};