<?php
// {$setting_id}[$id] - Contains the setting id, this is what it will be stored in the db as.
// $class - optional class value
// $id - setting id
// $options[$id] value from the db

$c = 0;

foreach ( $option_values as $k => $v ) {
    echo "<label class='switch'><input class='$id' type='checkbox' name='{$setting_id}[$name]' value='$k' " . ( ( isset( $options[ $name ] ) ? $options[ $name ] == "Enable" : '' ) ? 'checked' : '' ) . "  /><span class='slider round'></span></label> $v<br/>";
    $c++;
}

$suffix = defined( 'MOOWOODLE_SCRIPT_DEBUG' ) && MOOWOODLE_SCRIPT_DEBUG ? '' : '.min';
wp_enqueue_style( 'woocommerce_check_css', $MooWoodle->plugin_url . 'framework/field-types/css/checkbox' . $suffix . '.css', array(), $MooWoodle->version );