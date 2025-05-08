import { __ } from '@wordpress/i18n';

export default {
    id: "synchronize-user",
    priority: 20,
    name: __("User Synchronization", 'moowoodle'),
    desc: __("Synchronization on demand with automatic, real-time updates.", 'moowoodle'),
    icon: "adminLib-supervised_user_circle",
    submitUrl: "settings",
    proDependent: true,
    modal: [
        {
            key: "user_sync_direction",
            type: "checkbox-custom-img",
            desc: __("The synchronization flow specifies the direction of data transfer. To enable two-way synchronization, select both directions. This applies to existing users as well. With 'Real-time profile synchronization', user profile information will sync immediately whenever users update their profiles.<br><br> <span class='highlighted-part'>User uniqueness will be checked based on email. If the user exists in the other system, their profile information will be synchronized; otherwise, a new user will be created. <br>Synchronizing user information fails if the same username is found in another instance but linked to a different email address.</span>", 'moowoodle'),
            label: __("Synchronization flow between sites", 'moowoodle'),
            // image1: appLocalizer.wordpress_logo,
            // image2: appLocalizer.moodle_logo,
            syncDirections: [
                {
                  value: 'wordpress_to_moodle',
                  label: 'WordPress to Moodle',
                  img1: appLocalizer.wordpress_logo,
                  img2: appLocalizer.moodle_logo
                },
                {
                  value: 'moodle_to_wordpress',
                  label: 'Moodle to WordPress',
                  img1: appLocalizer.moodle_logo,
                  img2: appLocalizer.wordpress_logo
                }
            ],
            proSetting: true,
        },
        {
            key: "wordpress_user_role",
            type: "checkbox",
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
            dependent: {
                key: "user_sync_direction",
                value: 'wordpress_to_moodle'
            }
        },
        {
            key: "moodle_user_role",
            type: "checkbox",
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
            dependent: {
                key: "user_sync_direction",
                value: 'moodle_to_wordpress'
            }
        },
        {
            key: "user_sync_options",
            type: "sync-map",
            desc: __("Define the user profile information mapping between WordPress and Moodle. Add multiple rows above to define all the profile data you wish to map. Any remaining profile field will be excluded from the synchronization process.<br>User will be created based on their e-mail id, hence email id can't be mapped.", 'moowoodle'),
            label: __("Profile information mapping", 'moowoodle'),
            select_deselect: true,
            syncFieldsMap: {
                wordpress: {
                  heading: 'WordPress',
                  fields: {
                    firstname: 'First name',
                    lastname: 'Last name',
                    username: 'User name',
                    password: 'Password'
                  }
                },
                moodle: {
                  heading: 'Moodle',
                  fields: {
                    firstname: 'First name',
                    lastname: 'Last name',
                    username: 'User name',
                    password: 'Password'
                  }
                }
            },
            proSetting: true,
        },
        {
            key: "realtime_user_sync",
            type: "checkbox",
            desc: __('If enabled, the real-time profile update scheduler will initiate based on the "synchronization flow" settings.<br>When a new user is added or updates their profile information, it will be synchronized between WordPress to Moodle, or vice versa, according to the profile information mapping settings above, based on the specified direction.', 'moowoodle'),
            label: __("Real-Time profile synchronization", 'moowoodle'),
            options: [
                {
                    key: "realtime_user_sync",
                    label: __('', 'moowoodle'),
                    value: "realtime_user_sync"
                }
            ],
            look: "toggle"
        },
        {
            key: "sync_user_btn",
            type: "syncbutton",
            interval: 2500,
            apilink: 'sync',
            parameter: 'user',
            label: "Sync profiles of existing users ",
            value: "Synchronize users now!! ",
            desc: __("This will trigger immediate synchronization of all existing user accounts between WordPress and Moodle based on the configured data synchronization flow.<br><br><span class='highlighted-part'>User uniqueness will be checked based on email. If the user exists in the other system, their profile information will be synchronized; otherwise, a new user will be created.<br>Synchronizing user information fails if the same username is found in another instance but linked to a different email address.</span>", 'moowoodle'),
            proSetting: true,
        },
    ]
};
