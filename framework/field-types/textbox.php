<?php
// {$setting_id}[$id] - Contains the setting id, this is what it will be stored in the db as.
// $class - optional class value
// $id - setting id
// $options[$id] value from the db
global $MooWoodle;
if (empty($options[$name])) {
	$options[$name] = '';
}

echo "
	<div class='mw-textbox-input-wraper " . ' ' .  (empty($class) ? 'regular-text' : $class) . "'>
		<input id='$id' class='mw-setting-form-input " . ' ' . (($is_pro == 'pro') ? $MooWoodle->moowoodle_pro_adv ? ' disabled ' : '' : '') . (empty($class) ? 'regular-text' : $class) . "' name='{$setting_id}[$name]' type='text' value='" . esc_attr($options[$name]) . "' />";
if ($copy_text == 'copy') {
	echo "<button class='mw-copytoclip button-secondary' type='button'>" . esc_html(__('Copy', 'moowoodle')) . "</button>";
}
echo "</div>";
