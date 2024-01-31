<?php
// {$setting_id}[$id] - Contains the setting id, this is what it will be stored in the db as.
// $class - optional class value
// $id - setting id
// $options[$id] value from the db
global $MooWoodle;
$pro_popup_overlay = $pro_sticker = $disable = '';
if ($MooWoodle->moowoodle_pro_adv) {
	$pro_sticker = '<span class="mw-pro-tag">Pro</span>';
	$pro_popup_overlay = ' mw-pro-popup-overlay ';
	$disable = 'disabled';
}

if($desc_posi == 'up'){
    ?>
    <p class='mw-form-descriptions'><?php echo $desc; ?></p>
    <?php
    $desc = $note != '' ? $note : '';
}
echo "<select id='$id' class='mw-setting-form-select"  . (empty($class) ? '' : $class) . "' name='{$setting_id}[$name]' >" ;
$i=0;
foreach ($option_values as $k => $v) {
	if (is_array($v)) {
		if(!$i){
			echo "<option value='0' " . selected($options[$name], 0, false) . ">Seclect</option>";
			$i++;
		}
		echo "<option value='$i' " . selected($options[$name], $i, false) . " data-desc='" . $v['desc'] . " ' >$k</option>";
		if(selected($options[$name], $i, false)) $sub_desc = $v['desc'];
	} else {
		if (!isset($options[$name])) {
			$options[$name] = '';
		}
		echo "<option value='$k' " . selected($options[$name], $k, false) . ">$v</option>";
	}
	$i++;
}
echo "</select> " . $pro_sticker;
if(is_array($v)){
?>
<script>
var selectElement = document.getElementById('<?php echo $id; ?>');
selectElement.addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
	var description = selectedOption.getAttribute('data-desc');
    document.querySelector('.mw-normal-checkbox-label').innerHTML = description;
});
</script>
<div class="mw-normal-checkbox-label">
	<?php echo $sub_desc;?>
</div>
<?php
}
