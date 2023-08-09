<form method="post">
    <button type="submit" onclick="return confirm('Are you sure?')" name="clearlog" class="button-secondary">Clear Log</button>
</form>
<div>
<?php

if(file_exists(MW_LOGS . "/error.log")){
	echo '<pre>' . file_get_contents(MW_LOGS . "/error.log") . '</pre>';
}

?>
</div>
