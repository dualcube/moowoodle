import { __ } from '@wordpress/i18n';

export default {
    id: "synchronize-user",
    priority: 10,
    name: __("Users - Manual & Automatic mode", 'moowoodle'),
    desc: __("Information management", 'moowoodle'),
    icon: "font-mail",
    submitUrl: "save-moowoodle-setting",
    proDependent: true,
    modal: [
        // {
        //     key: "update_moodle_user",
        //     type: "checkbox",
        //     desc: __('Enableing this will start the synchornization between WordPress and Moodle sites at specified intervals (Default: daily). <br>Select the "Site-to-site data synchronization direction" to set the synchronization direction and use "<b>Automatic synchronization frequency</b>" to define how frequentl the synchronization process runs.', 'moowoodle'),
        //     label: __("Initiate synchronization", 'moowoodle'),
        //     options: [
        //         {
        //             key: "update_moodle_user",
        //             label:  __('', 'moowoodle'), 
        //             value: "update_moodle_user"
        //         }
        //     ],
        //     proSetting: true,
        // },
        {
            key: "user_sync_direction",
            type: "checkbox-custom-img",
            desc: __("Initiate the <b>real-time synchronization</b> direction between your WordPress and Moodle sites.<br>When a new user is added, their profile will be synchronized between WordPress and Moodle according to the Profile Information Mapping settings.<br>For an existing user, if they update their profile and the updated data matches any criteria set in the 'Profile Information Mapping', their information will also be synchronized between WordPress and Moodle.", 'moowoodle'),
            label: __("Site-to-site data synchronization direction", 'moowoodle'),
            proSetting: true,
        },
        {
            key: 'separator_content',
            type: 'section',
            label: "",
        },
        {
            key: "sync-user-options",
            type: "sync_map",
            desc: __("Define the user profile information mapping between WordPress and Moodle. Add multiple rows above to define all the profile data you wish to map. Any remaining profile field will be excluded from the synchronization process.<br>User will be created based on their e-mail id, hence email id can't be mapped.", 'moowoodle'),
            label: __("Profile information mapping", 'moowoodle'),
            select_deselect: true,
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
            interval: 60000,
            apilink: 'realtime-sync-users',
            statusApiLink: 'sync-status-user',
            // label: __("Manual synchronization mode", 'moowoodle'),
            value: "Synchronize all existing user profile now!! ",
            desc: __("This will synchronize all existing user accounts between WordPress and Moodle instantly according to the selected ‘Site-to-site data synchronization direction’. <br> <span class='highlighted-part'>While synchronizing user information, we use the email address as the unique identifier for each user. We check the username associated with that email address, and if we find the same username in the other instance but with a different email address, the user's information cannot be synchronized.</span>", 'moowoodle'),
            proSetting: true,
        },
    ]
};
