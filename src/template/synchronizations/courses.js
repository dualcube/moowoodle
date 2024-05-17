import { __ } from '@wordpress/i18n';

export default {
    id: "synchronize-course",
    priority: 20,
    name: __("Courses and Products", 'moowoodle'),
    desc: __("Mapping of courses to products, automatic & manual mode.", 'moowoodle'),
    icon: "font-mail",
    submitUrl: "save-moowoodle-setting",
    modal: [
        {
            key: "course_sync_direction",
            type: "checkbox",
            desc: __('This feature enables you to synchronize products from Moodle and automatically generate corresponding products in your WordPress site. Use the "Automatic synchronization frequency" option to determine the frequency of this synchronization process.', 'moowoodle'),
            label: __("Initiate synchronization", 'moowoodle'),
            options: [
                {
                    key: "moodle_to_wordpress",
                    label: __('', 'moowoodle'),
                    value: "moodle_to_wordpress",
                }
            ],
        },
        {
            key: "sync-course-options",
            type: "checkbox-default",
            desc: __("", 'moowoodle'),
            label: __("Course information mapping", 'moowoodle'),
            select_deselect: true,
            options: [
                {
                    key: "sync_courses",
                    label: __( 'Course categories', 'moowoodle' ),
                    hints: __("This function will retrieve all Moodle course data and synchronize it with the courses listed in WordPress.", 'moowoodle'),
                    value: "sync_courses",
                },
                {
                    key: "sync_courses_category",
                    label: __( 'Course SKU', 'moowoodle' ),
                    hints: __("This feature will scan the entire Moodle course category structure and synchronize it with the WordPress category listings.", 'moowoodle'),
                    value: "sync_courses_category",                    
                    proSetting: true,
                },
                {
                    key: "sync_image",
                    label: __( 'Course images', 'moowoodle' ),
                    hints: __("This function copies course images and sets them as WooCommerce product images.", 'moowoodle'),
                    value: "sync_image",
                    proSetting: true,
                },
            ]
        },
        {
            key: 'separator_content',
            type: 'section',
            label: "",
        },
        {
            key: "course_schedule_interval",
            type: "select-custom-radio",
            desc: __("Select pption for course synchronization schedule interval.", 'moowoodle'),
            label: __("Automatic synchronization frequency", 'moowoodle'),
            options: [
                {
                    key: "realtime",
                    label: __("Realtime", 'moowoodle'),
                    value: "realtime",
                },
                {
                    key: "hour",
                    label: __("Hourly", 'moowoodle'),
                    value: "hour",
                },
                {
                    key: "hour6",
                    label: __("In 6 Hours", 'moowoodle'),
                    value: "hour6",
                },
                {
                    key: "day",
                    label: __("Daily", 'moowoodle'),
                    value: "day",
                },
                {
                    key: "week",
                    label: __("Weekly", 'moowoodle'),
                    value: "week",
                },
                {
                    key: "month",
                    label: __("Month", 'moowoodle'),
                    value: "month",
                }
            ],
            proSetting: true,
        },
        {
            key: 'separator_content',
            type: 'section',
            label: "",
        },
        {
            key: "product_sync_option",
            type: "checkbox-default",
            desc: __("When courses undergo synchronization, corresponding products are automatically generated or existing ones are updated by default. If you have specific preferences, you can customize the settings by deselecting relevant options from the provided list.", 'moowoodle'),
            label: __("Course-to-product handling", 'moowoodle'),
            options: [
                {
                    key: "create_update",
                    label: __('Create and Update Products', 'moowoodle'),
                    hints: __('This feature allows you to update previously created product information using Moodle course data. NOTE: This action will overwrite all existing product details with those from Moodle course details.', 'moowoodle'),
                    value: "create_update",
                },
                {
                    key: "create",
                    label: __('Create Products', 'moowoodle'),
                    hints: __('This functionality enables automatic creation of new products based on Moodle course data if they do not already exist in WordPress.', 'moowoodle'),
                    value: "create",
                },
                {
                    key: "update",
                    label: __('Update Products', 'moowoodle'),
                    hints: __('This feature allows you to update previously created product information using Moodle course data. NOTE: This action will overwrite all existing product details with those from Moodle course details.', 'moowoodle'),
                    value: "update",
                }
            ],
            proSetting: true,
        },
        {
            key: "sync_course_btn",
            type: "syncbutton",
            label: __("Manual synchronization mode", 'moowoodle'),
            value: "Synchronize courses now!",
            desc: "Initiate the immediate synchronization of all courses from Moodle to WordPress."
        },
    ]
};