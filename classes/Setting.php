<?php

namespace MooWoodle;

defined('ABSPATH') || exit;

/**
 * Handle MooWoodle Global setting.
 * Use Wordpress Option table to manage setting.
 * Cache The settings for a session.
 */
class Setting {
    /**
     * Container store global setting
     * @var array
     */
    private $settings = [];

    /**
     * Contain global key of all settings
     * @var array
     */
    private $settings_keys = [];

    /**
     * Construct function for load setting
     */
    function __construct() {

        // Load all settings
        $this->load_settings();
    }

    /**
     * Load all setting from option table
     * @param mixed $fource
     * @return void
     */
    private function load_settings( $fource = true ) {

        // If settings are loaded previously and not force to load
        if ( ! $fource && $this->settings ) {
            return;
        }

        $setting_keys = $this->get_settings_keys();

        // Get all setting from option table
        foreach( $setting_keys as $key) {
            $this->settings[ $key ] = get_option( $key, [] );
        }
    }

    /**
     * Get all register setting key
     * @return array 
     */
    private function get_settings_keys() {

        // Settings key are avialable
        if ( $this->settings_keys ) {
            return $this->settings_keys;
        }

        /**
         * Filter for register settings key's
         * @var array setting keys
         */
        $this->settings_keys = apply_filters( 'moowoodle_register_settings_keys', [
            'moowoodle_extra_settings',
            'moowoodle_general_settings',
            'moowoodle_display_settings',
            'moowoodle_sso_settings',
            'moowoodle_notification_settings',
            'moowoodle_log_settings',
            'moowoodle_synchronize_shcedule_course_settings',
            'moowoodle_synchronize_shcedule_user_settings',
            'moowoodle_synchronize_datamap_settings',
        ]);

        return $this->settings_keys;
    }

    /**
     * Get the setting that was previously added.
     * If setting is not present it return defalult value 
     * @param string $key setting key
     * @param string $default setting value
     * @param mixed $option_key option table's key
     * @return mixed
     */
    public function get_setting( $key, $default = '', $option_key = null ) {

        // If option key is not provided
        if ( ! $option_key ) {
            $option_key = $this->get_option_key( $key );
        }

        $setting = $this->settings[ $option_key ] ?? [];

        return $setting[ $key ] ?? $default;
    }

    /**
     * Update the setting that was already added.
     * @param string $key setting key
     * @param string $value setting value
     * @param string $option_key option table's key
     * @return void
     */
    public function update_setting( $key, $value, $option_key = null ) {

        // If option key is not provided
        if ( ! $option_key ) {
            $option_key = $this->get_option_key( $key );
        }

        // Get the setting array from setting settings container
        $setting = $this->settings[ $option_key ] ?? [];

        // Update setting in setting container
        $setting[ $key ] = $value;
        $this->settings[ $option_key ] = $setting;

        // Update the setting in database
        update_option( $option_key,  $setting );
    }

    /**
     * Get the option 
     * @param string $key 
     * @param mixed $default
     * @return mixed value
     */
    public function get_option( $key, $default = [] ) {

        // Check key exist in register settings keys
        if ( in_array( $key, $this->get_settings_keys() ) ) {
            return $this->settings[ $key ];
        }

        return get_option( $key, [] );
    }

    /**
     * Update the value in option table.
     * If key does't exist it create it.
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function update_option( $key, $value ) {
        
        // Check key exist in register settings keys
        if ( in_array( $key, $this->get_settings_keys() ) ) {
            
            // Update the container
            $this->settings[ $key ] = $value;
        }

        // Update the option
        update_option( $key, $value );
    }

    /**
     * Find option key from setting container
     * @param mixed $key setting key
     * @return string
     */
    private function get_option_key( $key ) {
        foreach ( $this->settings as $option_key => $setting ) {     
            // Key exist in a particular setting.
            if ( is_array( $setting ) && array_key_exists( $key, $setting ) ) {
                return $option_key;
            }
        }

        return 'stockmanager_extra_settings';
    }
}