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
            
            $this->create_databases();
            
            $this->migrate_databases();

            update_option( 'moowoodle_version', MOOWOODLE_PLUGIN_VERSION );
            
            do_action( 'moowoodle_updated' );
        }
    }

    /**
     * Database creation functions
     * @return void
     */
    public static function create_databases() {
        global $wpdb;

        $collate = '';

        if ($wpdb->has_cap('collation')) {
            $collate = $wpdb->get_charset_collate();
        }

        $wpdb->query(
            "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "moowoodle_enrollment` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) NOT NULL DEFAULT 0,
                `user_email` varchar(100) NOT NULL,
                `course_id` bigint(20) NOT NULL,
                `order_id` bigint(20) NOT NULL,
                `item_id` bigint(20) NOT NULL,
                `status` varchar(20) NOT NULL,
                `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) $collate;"
        );
    }

    /**
     * Migrate database
     * @return void
     */
    public static function migrate_databases() {
        $previous_version = get_option( 'moowoodle_version', '' );

        if ( version_compare( $previous_version, '3.2.0', '<' ) ) {
            Enrollment::migrate_enrollments();
        }
    }

    /**
     * Set default moowoodle admin settings.
     * @return void
     */
    private function set_default_settings() {
        $general_settings = [
            'moodle_url'          => '',
            'moodle_access_token' => '',
        ];
        // Default value for sso setting
        $sso_settings = [
            'moowoodle_sso_enable'     => [],
            'moowoodle_sso_secret_key' => '',
        ];
        // Default value for display setting
        $display_settings = [
            'start_end_date'                    => ['start_end_date'],
            'my_courses_priority'               => 0,
            'moowoodle_create_user_custom_mail' => [],
        ];
        // Default value for log setting
        $tool_settings = [
            'moowoodle_adv_log' => [],
            'moodle_timeout'    => 5,
            'schedule_interval' => 1
        ];
        // Default value sync course setting
        $course_settings = [
            'sync-course-options'      => [ 'sync_courses_category', 'sync_courses_sku' ],
            'product_sync_option'      => [ 'create', 'update' ],
        ];
        // Default value for sync user setting
        $user_settings = [
            'wordpress_user_role'  => ['customer'],
            'moodle_user_role'     => ['5'],
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
        update_option( 'moowoodle_tool_settings', $tool_settings );
        update_option( 'moowoodle_synchronize_course_settings', $course_settings );
        update_option( 'moowoodle_synchronize_user_settings',$user_settings );
    }
}