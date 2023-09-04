<?php
// {$setting_id}[$id] - Contains the setting id, this is what it will be stored in the db as.
// $class - optional class value
// $id - setting id
// $options[$id] value from the db

$c = 0;

foreach ( $option_values as $k => $v ) {
    echo "<div class='mw-toggle-checkbox-content '><input id='$id' class='mw-toggle-checkbox $id ". ( ($is_pro == 'pro') ? apply_filters('moowoodle_pro_sticker',' mw-toggle-checkbox-disabled ') : '' ) ."' type='checkbox' name='{$setting_id}[$name]' value='$k' " . ( ( isset( $options[ $name ] ) ? $options[ $name ] == "Enable" : '' ) ? 'checked' : '' ) . ' ' . ( ($is_pro == 'pro') ? apply_filters('moowoodle_pro_sticker',' disabled ') : '' ) . "  /><label for='$id' class='mw-toggle-checkbox-label'></label></div> $v";
    $c++;
}

$suffix = defined( 'MOOWOODLE_SCRIPT_DEBUG' ) && MOOWOODLE_SCRIPT_DEBUG ? '' : '.min';
wp_enqueue_style( 'woocommerce_check_css', $MooWoodle->plugin_url . 'framework/field-types/css/checkbox.css', array(), $MooWoodle->version );