<div class="mw-form-group">
<div class="mw-input-content">
<div class='mw-log-content '>
<form method="post">
    <button type="submit" onclick="return confirm('Are you sure?')" name="clearlog" class="button-secondary">Clear Log</button>
</form>
<div class="mw-log-status">
<?php
if(file_exists(MW_LOGS . "/error.log")){
	$logs = explode("\n", file_get_contents(MW_LOGS . "/error.log"));
}
foreach($logs as $log){
	
	echo '<p>'. $log . '</p>';
}


?>
</div>
</div>
</div>
