import { __ } from '@wordpress/i18n';

export default {
    id: "synchronize-user",
    priority: 10,
    name: __("Users", 'moowoodle'),
    desc: __("Information management - Manual & Automatic mode", 'moowoodle'),
    icon: "font-mail",
    submitUrl: "save-moowoodle-setting",
    proDependent: true,
    modal: [
        {
            key: "update_moodle_user",
            type: "checkbox",
            desc: __('Enableing this will start the synchornization between WordPress and Moodle sites at specified intervals (Default: daily). <br>Select the "Site-to-site data synchronization direction" to set the synchronization direction and use "<b>Automatic synchronization frequency</b>" to define how frequentl the synchronization process runs.', 'moowoodle'),
            label: __("Initiate synchronization", 'moowoodle'),
            options: [
                {
                    key: "update_moodle_user",
                    label:  __('', 'moowoodle'), 
                    value: "update_moodle_user"
                }
            ],
            proSetting: true,
        },
        {
            key: "sync-user-options",
            type: "sync_map",
            desc: __("Define the user profile information mapping between WordPress and Moodle. Add multiple rows above to define all the profile data you wish to map. Any remaining profile field will be excluded from the synchronization process.<br> User will be created based on their e-mail id, hence email id can't be mapped.", 'moowoodle'),
            label: __("Profile information mapping", 'moowoodle'),
            select_deselect: true,
            proSetting: true,
        },
        {
            key: 'separator_content',
            type: 'section',
            label: "",
            desc: __("Manual synchronization", 'moowoodle')
        },
        {
            key: "user_sync_direction",
            type: "checkbox-custom-img",
            // desc: __("<b>Prior to updating existing user info, you must select the user info to be synchronized at </b>", 'moowoodle') . $moowoodle_sync_setting_url . __("<br><br>While synchronizing user information, we use the email address as the unique identifier for each user. We check the username associated with that email address, and if we find the same username in the other instance but with a different email address, the user's information cannot be synchronized.", 'moowoodle'),
            label: __("Site-to-site data synchronization direction", 'moowoodle'),
            proSetting: true,
        },
        {
            key: "user_schedule_interval",
            type: "select-custom-radio",
            desc: __("Select the interval for the user synchronization process. Based on this schedule, the cron job will run to sync users between WordPress and Moodle.", 'moowoodle'),
            label: __("Automatic synchronization frequency", 'moowoodle'),
            options: [
                {
                    key: "perminute",
                    label: __("Per minute", 'moowoodle'),
                    value: "perminute",
                },
                {
                    key: "hourly",
                    label: __("Hourly", 'moowoodle'),
                    value: "hourly",
                },
                {
                    key: "six_hours",
                    label: __("In 6 hours", 'moowoodle'),
                    value: "six_hours",
                },
                {
                    key: "daily",
                    label: __("Daily", 'moowoodle'),
                    value: "daily",
                },
                {
                    key: "weekly",
                    label: __("Weekly", 'moowoodle'),
                    value: "weekly",
                },
                {
                    key: "monthly",
                    label: __("Monthly", 'moowoodle'),
                    value: "monthly",
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
            key: "sync_user_btn",
            type: "syncbutton",
            apilink: 'cron-sync-users',
            label: __("Manual synchronization mode", 'moowoodle'),
            value: "Synchronize user profile now!! ",
            desc: "This will synchronize user accounts between WordPress and Moodle instantly according to the selected ‘Sync Direction’.",
            proSetting: true,
        },
    ]
};
