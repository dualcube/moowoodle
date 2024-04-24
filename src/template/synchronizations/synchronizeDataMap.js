import { __ } from '@wordpress/i18n';

export default {
    id: "synchronize-datamap",
    priority: 10,
    name: __("Synchrinize Data Map", 'moowoodle'),
    desc: __("Synchrinize Data Map", 'moowoodle'),
    icon: "font-mail",
    submitUrl: "save-moowoodle-setting",
    modal: [
        {
            key: "sync-user-options",
            type: "checkbox",
            desc: __("Determine User Information to Synchronize in Moodle-WordPress User synchronization. Please be aware that this setting does not apply to newly created users.", 'moowoodle'),
            label: __("User Information", 'moowoodle'),
            select_deselect: true,
            options: [
                {
                    key: "sync_user_first_name",
                    label: __('First Name', 'moowoodle'),
                    value: "sync_user_first_name",
                },
                {
                    key: "sync_user_last_name",
                    label: __('Last Name', 'moowoodle'),
                    value: "sync_user_last_name",
                },
                {
                    key: "sync_username",
                    label: __('Username', 'moowoodle'),
                    value: "sync_username",
                },
                {
                    key: "sync_password",
                    label: __('Password', 'moowoodle'),
                    value: "sync_password",
                }
            ]
        },
        {
            key: "sync-course-options",
            type: "checkbox",
            desc: __("Select Option For Course Sync.", 'moowoodle'),
            label: __("Course Information", 'moowoodle'),
            select_deselect: true,
            options: [
                {
                    key: "sync_courses",
                    label: __("This function will retrieve all Moodle course data and synchronize it with the courses listed in WordPress.", 'moowoodle'),
                    value: "sync_courses",
                },
                {
                    key: "sync_courses_category",
                    label: __("This feature will scan the entire Moodle course category structure and synchronize it with the WordPress category listings.", 'moowoodle'),
                    value: "sync_courses_category",
                },
                {
                    key: "sync_image",
                    label: __("This function copies course images and sets them as WooCommerce product images.", 'moowoodle'),
                    value: "sync_image",
                },
            ]
        }
    ]
};