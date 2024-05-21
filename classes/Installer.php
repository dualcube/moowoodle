<?php
namespace MooWoodle;
defined('ABSPATH') || exit;
/**
 * plugin Install
 *
 * @version     3.1.7
 * @package     MooWoodle
 * @author      DualCube
 */
class Installer {
    /**
     * Construct installation.
     * @return void
     */
    public function __construct() {
        if ( get_option( 'moowoodle_version' ) != MOOWOODLE_PLUGIN_VERSION ) {
            $this->set_default_settings();
            $this->migration();
            do_action( 'moowoodle_updated' );
        }
    }
    /**
     * Plugin migration.
     * @return void
     */
    public static function migration() {
        update_option( 'moowoodle_version', MOOWOODLE_PLUGIN_VERSION );
    }
    /**
     * Set default moowoodle admin settings.
     * @return void
     */
    private function set_default_settings() {
        $general_settings = [
            'moodle_url'          => '',
            'moodle_access_token' => '',
            'moodle_timeout'      => 10,
        ];
        // Default value for sso setting
        $sso_settings = [
            'moowoodle_sso_enable'     => [],
            'moowoodle_sso_secret_key' => '',
        ];
        // Default value for display setting
        $display_settings = [
            'start_end_date'                    => [],
            'my_courses_priority'               => [],
            'moowoodle_create_user_custom_mail' => [],
        ];
        // Default value for log setting
        $log_settings = [
            'moowoodle_adv_log' => [],
        ];
        // Default value sync course setting
        $course_settings = [
            'course_sync_direction'    => [ 'moodle_to_wordpress' ],
            'course_sync_options'      => [ 'sync_courses', 'sync_courses_category' ],
            'course_schedule_interval' => [],
            'product_sync_option'      => [ 'create_update', 'create', 'update' ],
        ];
        // Default value for sync user setting
        $user_settings = [
            'update_moodle_user'     => [],
            'user_sync_options'      => [],
            'user_sync_direction'    => [],
            'user_schedule_interval' => [],
        ];
        // Update default settings
        update_option( 'moowoodle_general_settings', array_merge(
            $general_settings,
            get_option( 'moowoodle_general_settings', [] )
        ));
        update_option( 'moowoodle_sso_settings', array_merge(
            $sso_settings,
            get_option( 'moowoodle_sso_settings', [] )
        ));
        update_option( 'moowoodle_display_settings', $display_settings );
        update_option( 'moowoodle_log_settings', $log_settings );
        update_option( 'moowoodle_synchronize_course_settings', $course_settings );
        update_option( 'moowoodle_synchronize_user_settings',$user_settings );
    }
}