import { __ } from '@wordpress/i18n';

export default {
    id: "synchronize-cohort",
    priority: 30,
    name: __("Cohort synchronization", 'moowoodle'),
    desc: __("Fetch Moodle cohort on demand.", 'moowoodle'),
    icon: "adminLib-book",
    submitUrl: "settings",
    modal: [
        {
            key: "cohort_sync_option",
            type: "checkbox",
            desc: __("", 'moowoodle'),
            label: __("Cohort & product synchronization", 'moowoodle'),
            select_deselect: true,
            proSetting: true,
            options: [
                {
                    key: "create",
                    label: __('Create new products along with', 'moowoodle'),
                    hints: __('This will additionally create new products based on Moodle cohort fetched, if they do not already exist in WordPress.', 'moowoodle'),
                    value: "create",
                },
                {
                    key: "update",
                    label: __('Update existing products along with', 'moowoodle'),
                    hints: __('Update product information based on Moodle cohort data. <br><span class="highlighted-part">Caution: This will overwrite all existing product details with those from Moodle cohort details.</span>', 'moowoodle'),
                    value: "update",
                }
            ],
        },
        {
            key: "sync_cohort_btn",
            type: "syncbutton",
            interval: 2500,
            apilink: 'sync',
            parameter: 'cohort',
            value: "Synchronize cohort now!",
            desc: "Initiate the immediate synchronization of all courses from Moodle to WordPress.<br><span class='highlighted-part'><br>With the 'Course & product synchronization' option, you have the ability to specify whether you want to create new products, update existing products.<br>Through the 'Course information mapping' feature, you gain the flexibility to define which specific course data gets imported from Moodle, like course ID number/course images etc. By default we will fetch only the category of the product.</span>"
        },
    ]
}