<?php
// {$setting_id}[$id] - Contains the setting id, this is what it will be stored in the db as.
// $class - optional class value
// $id - setting id
// $options[$id] value from the db
global $MooWoodle;
foreach ($option_values as $k => $v) {
	echo "<div class='mw-toggle-checkbox-content '><input id='$id' class='mw-toggle-checkbox $id " . (($is_pro == 'pro') && $MooWoodle->moowoodle_pro_adv ? ' disabled ' : '') . "' type='checkbox' name='{$setting_id}[$name]' value='$k' " . ((isset($options[$name]) ? $options[$name] == "Enable" : '') ? 'checked' : '') . ' ' . (($is_pro == 'pro') && $MooWoodle->moowoodle_pro_adv ? ' disabled ' : '') . "  /><label for='$id' class='mw-toggle-checkbox-label'></label></div> $v";
}
