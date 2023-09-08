<?php
// {$setting_id}[$id] - Contains the setting id, this is what it will be stored in the db as.
// $class - optional class value
// $id - setting id
// $options[$id] value from the db
global $MooWoodle;
if ($MooWoodle->moowoodle_pro_adv) {
    $pro_popup_overlay = ' mw-pro-popup-overlay ';
}
echo "<select id='$id' class='mw-setting-form-select" . ' ' . (($is_pro == 'pro') ? $MooWoodle->moowoodle_pro_adv ? ' disabled ': '' : '') . (empty($class) ? '' : $class) . "' name='{$setting_id}[$name]'>";
foreach ($option_values as $k => $v) {
	if (is_array($v)) {
		echo '<optgroup label="' . ucwords($k) . '">';
		foreach ($v as $k1 => $v1) {
			echo "<option value='$k1' " . selected($options[$name], $k1, false) . ">$v1</option>";
		}
		echo '</optgroup>';
	} else {
		if (!isset($options[$name])) {
			$options[$name] = '';
		}
		echo "<option value='$k' " . selected($options[$name], $k, false) . ">$v</option>";
	}
}
echo "</select> ";
