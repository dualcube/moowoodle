import { __ } from '@wordpress/i18n';

export default {
    id: "synchronize-course",
    priority: 10,
    name: __("Courses and Products synchronization", 'moowoodle'),
    desc: __("Fetch Moodle courses & generate products on demand.", 'moowoodle'),
    icon: "adminLib-book",
    submitUrl: "settings",
    modal: [
        {
            key: "sync-course-options",
            type: "checkbox",
            desc: __("", 'moowoodle'),
            label: __("Course information mapping", 'moowoodle'),
            select_deselect: true,
            options: [
                {
                    key: "sync_courses_category",
                    label: __('Course categories', 'moowoodle'),
                    hints: __("Scan the entire Moodle course category structure and synchronize it with the WordPress category listings.", 'moowoodle'),
                    value: "sync_courses_category",
                },
                {
                    key: "sync_courses_sku",
                    label: __('Course ID number - Product SKU', 'moowoodle'),
                    hints: __("Retrieves the course ID number and assigns it as the product SKU.", 'moowoodle'),
                    value: "sync_courses_sku",
                    proSetting: true,
                },
                {
                    key: "sync_image",
                    label: __('Course image', 'moowoodle'),
                    hints: __("Copies course images and sets them as WooCommerce product images.", 'moowoodle'),
                    value: "sync_image",
                    proSetting: true,
                },
                {
                    key: "sync_group",
                    label: __('Course group', 'moowoodle'),
                    hints: __("Copies course images and sets them as WooCommerce product images.", 'moowoodle'),
                    value: "sync_group",
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
            key: "product_sync_option",
            type: "checkbox",
            desc: __("", 'moowoodle'),
            label: __("Course & product synchronization", 'moowoodle'),
            select_deselect: true,
            options: [
                {
                    key: "create",
                    label: __('Create new products along with', 'moowoodle'),
                    hints: __('This will additionally create new products based on Moodle courses fetched, if they do not already exist in WordPress.', 'moowoodle'),
                    value: "create",
                },
                {
                    key: "update",
                    label: __('Update existing products along with', 'moowoodle'),
                    hints: __('Update product information based on Moodle course data. <br><span class="highlighted-part">Caution: This will overwrite all existing product details with those from Moodle course details.</span>', 'moowoodle'),
                    value: "update",
                }
            ],
        },
        {
            key: 'separator_content',
            type: 'section',
            label: "",
        },
        {
            key: "sync_course_btn",
            type: "syncbutton",
            interval: 2500,
            apilink: 'sync',
            parameter: 'course',
            value: "Synchronize courses now!",
            desc: "Initiate the immediate synchronization of all courses from Moodle to WordPress.<br><span class='highlighted-part'><br>With the 'Course & product synchronization' option, you have the ability to specify whether you want to create new products, update existing products.<br>Through the 'Course information mapping' feature, you gain the flexibility to define which specific course data gets imported from Moodle, like course ID number/course images etc. By default we will fetch only the category of the product.</span>"
        },
    ]
};