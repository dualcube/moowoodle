<div class="mw-input-content">
	<div class='mw-log-content '>
		<form method="post">
			<button type="submit" onclick="return confirm('Are you sure?')" name="clearlog" class="button-secondary"><?php echo esc_html(__('Clear Log', 'moowoodle')); ?></button>
		</form>
		<div class="mw-log-status">
			<?php
if (file_exists(MW_LOGS . "/error.txt")) {
	$logs = explode("\n", wp_remote_retrieve_body(wp_remote_get(get_site_url(null, str_replace(ABSPATH, '', MW_LOGS) . "/error.txt"))));
}
foreach ($logs as $log) {
	echo '<p>' . $log . '</p>';
}
?>
		</div>
	</div>
</div>
