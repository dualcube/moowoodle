import { __ } from '@wordpress/i18n';

export default {
    id: "synchronize-user",
    priority: 10,
    name: __("Users - Manual & Automatic mode", 'moowoodle'),
    desc: __("Synchronize user data as needed, with automatic, real-time updates when users update their profiles.", 'moowoodle'),
    icon: "font-supervised_user_circle",
    submitUrl: "save-moowoodle-setting",
    proDependent: true,
    modal: [
        {
            key: 'separator_content',
            type: 'section',
            label: "",
            desc: "Automatic, Real-time updates",
            hint: "Under the automatic synchronization mode, user data is synchronized in real-time across WordPress and Moodle platforms in accordance with synchronization direction. <br>Whenever a new user is added or profile information is updated, it is synchronized, based on the specified direction and profile information mapping settings.<br>During user information synchronization, we verify the username linked to the email address. If we discover the same username in another instance but with a different email address, synchronization of the user's information is not possible."
        },
        {
            key: 'separator_content',
            type: 'section',
            label: "",
        },
        {
            key: "user_sync_direction",
            type: "checkbox-custom-img",
            desc: __("Once enabled, the real-time profile update scheduler will initiate based on the synchronization direction you set between your WordPress and Moodle sites. <br>The user roles to be synchronized are defined by selecting the appropriate settings below.", 'moowoodle'),
            label: __("Synchronization flow between sites", 'moowoodle'),
            proSetting: true,
        },
        {
            key: "wordpress_user_role",
            type: "checkbox-default",
            desc: __("Users from the chosen roles will be added or updated in Moodle.", 'moowoodle'),
            label: __("WordPress user role to synchronize", 'moowoodle'),
            options: Object.entries(appLocalizer.wp_user_roles).map(( [key, name ] ) => {
                return {
                    key: key,
                    label: name,
                    value: key
                }
            }),
            select_deselect: true,
            proSetting: true,
        },
        {
            key: "moodle_user_role",
            type: "checkbox-default",
            desc: __("Users from the chosen roles will be added or updated in WordPress.", 'moowoodle'),
            label: __("Moodle user role to synchronize", 'moowoodle'),
            options: Object.entries(appLocalizer.md_user_roles).map(( [key, name ] ) => {
                return {
                    key: key,
                    label: name,
                    value: key
                }
            }),
            select_deselect: true,
            proSetting: true,
        },
        {
            key: "user_sync_options",
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
            desc: "Manual mode",
            hint: "In this mode, all current user accounts between WordPress and Moodle will be synchronized instantly according to the data synchronization direction configured above.<br>User uniqueness will be checked based on email. If the user exists in the other system, their profile information will be synchronized. If the user does not exist, a new user will be created."
        },
        {
            key: "sync_user_btn",
            type: "syncbutton",
            interval: 10000,
            apilink: 'realtime-sync-users',
            statusApiLink: 'sync-status-user',
            label: "Synchronize profile instantly",
            value: "Update all current user profiles now!! ",
            desc: __("<span class='highlighted-part'>During user information synchronization, we verify the username linked to the email address. If we discover the same username in another instance but with a different email address, synchronization of the user's information is not possible.</span>", 'moowoodle'),
            proSetting: true,
        },
    ]
};
