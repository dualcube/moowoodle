<?php
global $MooWoodle;
$pro_sticker = '';
if ($MooWoodle->moowoodle_pro_adv) {
    $pro_popup_overlay = ' mw-pro-popup-overlay ';
}
?>
<div class='mw-manage-enrolment-content '>
    <div class="moowoodle-manage-enrolment  <?php echo $pro_popup_overlay; ?> ">
        <p><a class="mw-image-adv">
                <img src="<?php echo esc_url(plugins_url()) ?>/moowoodle/assets/images/manage-enrolment.jpg" />
            </a></p>
    </div>
</div>