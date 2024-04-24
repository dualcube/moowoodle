import { __ } from '@wordpress/i18n';

export default {
    id: "display",
    priority: 20,
    name: __("Display Settings", 'moowoodle'),
    desc: __("Display Settings", 'moowoodle'),
    icon: "font-mail",
    submitUrl: "save-moowoodle-setting",
    modal: [
        {
            key: "start_end_date",
            type: "checkbox",
            desc: __('If enabled display start date and end date in shop page.', 'moowoodle'),
            label: __("Display Start Date and End Date in Shop Page", 'moowoodle'),
            options: [
                {
                    key: "start_end_date",
                    label:  __('Enable', 'moowoodle'), 
                    value: "start_end_date"
                }
            ]
        },
        {
            key: "my_courses_priority",
            type: "select",
            desc: __('Select below which menu the My Courses Menu will be displayed', 'moowoodle'),
            label: __("My Courses Menu Position", 'moowoodle'),
            options: []
        }
    ]
};