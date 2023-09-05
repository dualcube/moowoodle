<?php
// {$setting_id}[$id] - Contains the setting id, this is what it will be stored in the db as.
// $class - optional class value
// $id - setting id
// $options[$id] value from the db
if ( empty( $options[ $name ] ) ) {
	$options[ $name ] = '';
}
echo "
	<div class='mw-textbox-input-wraper " . ' ' . ( ($is_pro == 'pro') ? apply_filters('moowoodle_pro_sticker',' disabled ') : '' ) . ( empty( $class ) ? 'regular-text' : $class ) . "'>
		<input id='$id' class='mw-setting-form-input " . ' ' . ( ($is_pro == 'pro') ? apply_filters('moowoodle_pro_sticker',' disabled ') : '' ) . ( empty( $class ) ? 'regular-text' : $class ) . "' name='{$setting_id}[$name]' type='text' value='" . esc_attr( $options[ $name ] ) . "' />";
		if($copy_text == 'copy'){
			echo "<button class='mw-copytoclip button-secondary' type='button'>". esc_html(__('Copy', MOOWOODLE_TEXT_DOMAIN))."</button>";
		}


echo "</div>";