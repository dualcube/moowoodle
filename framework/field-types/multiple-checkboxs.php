 <div id="<?php echo $id; ?>-multiple-checkboxs">
     <button type="button" id="selectDeselectButton" class="button-secondary">Select / Deselect All</button>
     <div class="mw-select-deselect-checkbox-label-marge">
         <?php
global $MooWoodle;
$pro_popup_overlay = $pro_sticker = '';
if ($MooWoodle->moowoodle_pro_adv) {
	$pro_sticker = '<span class="mw-pro-tag">Pro</span>';
	$pro_popup_overlay = ' mw-pro-popup-overlay ';
}
foreach ($option_values as $checkbox_name => $checkbox_options) {
	?>
             <div class="mw-col-50 <?php echo (isset($checkbox_options['is_pro']) && ($checkbox_options['is_pro'] == 'pro') ? $pro_popup_overlay : ''); ?>">
                 <div class="mw-wrap-checkbox-and-label">
                     <div class="mw-normal-checkbox-content d-flex">
                         <input id="<?php echo $checkbox_options['id']; ?> " class="mw-toggle-checkbox <?php echo $checkbox_options['id'] . ((isset($checkbox_options['checked']) && $checkbox_options['checked'] == 'forced') ? ' forceCheckCheckbox ' : ''); ?>" value="Enable" type="checkbox" name="<?php echo $setting_id . '[' . $checkbox_options['name'] . ']'; ?>" value="general" <?php echo ((isset($options[$checkbox_options['name']]) ? $options[$checkbox_options['name']] == "Enable" : '') ? 'checked' : '') . ' ' . ((isset($checkbox_options['checked']) && $checkbox_options['checked'] == 'forced') ? ' checked ' : ''); ?>>
                         <p class="mw-settings-checkbox-description pt-0"><?php echo $checkbox_name . (isset($checkbox_options['is_pro']) && ($checkbox_options['is_pro'] == 'pro') ? $pro_sticker : ''); ?></p>
                     </div>
                     <div class="mw-normal-checkbox-label">
                         <p class="mw-form-description"><?php echo esc_html__($checkbox_options['desc']); ?></p>
                     </div>
                 </div>
             </div>
         <?php
}
?>
     </div>
 </div>