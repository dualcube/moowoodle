 <div id="<?php echo $id; ?>-multiple-checkboxs">
 <button type="button" id="selectDeselectButton" class="button-secondary">Select / Deselect All</button>
 <div class="mw-select-deselect-checkbox-label-marge">
    <?php ?>
<?php 
    
    foreach ( $option_values as $k => $v ) {
        ?>
        <div class="mw-col-50 <?php echo ( isset($v['is_pro'])&&($v['is_pro'] == 'pro') ? apply_filters('moowoodle_pro_sticker', ' mw-pro-popup-overlay ') : '' )  ; ?>">
        <div class="mw-wrap-checkbox-and-label d-flex">
            <div class="mw-normal-checkbox-content">
                <input id="<?php echo $v['id'] ;?>" class="mw-toggle-checkbox <?php echo $v['id'] ;?>" value="Enable" type="checkbox" name="<?php echo $setting_id .'['. $v['name'] .']';?>" value="general" <?php echo ( ( isset( $options[ $v['name'] ] ) ? $options[ $v['name'] ] == "Enable" : '' ) ? 'checked' : '' ) . ' ' . ( isset($v['is_pro'])&&($v['is_pro'] == 'pro') ? apply_filters('moowoodle_pro_sticker',' disabled ') : '' )  ;?>>
            </div>
            <div class="mw-normal-checkbox-label">
                <p class="mw-settings-checkbox-description pt-0"><?php echo $k. ( isset($v['is_pro'])&&($v['is_pro'] == 'pro') ? apply_filters('moowoodle_pro_sticker', '<span class="mw-pro-tag">Pro</span>') : '' )  ; ?></p>
            </div>
        </div>
    </div>
    <?php
    }

?>
</div>
</div>