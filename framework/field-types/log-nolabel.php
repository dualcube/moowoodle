<div class="mw-input-content">
	<div class='mw-log-content '>
		<form method="post">
			<button type="submit" onclick="return confirm('Are you sure?')" name="clearlog" class="button-secondary"><?php echo esc_html(__('Clear Log', 'moowoodle')); ?></button>
		</form>
		<div class="mw-log-status">
			<?php
if (file_exists(MW_LOGS . "/error.log")) {
	global $wp_filesystem;
	$logs = explode("\n", $wp_filesystem->get_contents((get_site_url(null, str_replace(ABSPATH, '', MW_LOGS) . "/error.log"))));
}
foreach ($logs as $log) {
	echo '<p>' . $log . '</p>';
}
?>
		</div>
	</div>
</div>
