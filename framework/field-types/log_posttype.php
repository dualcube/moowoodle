<?php
if(file_exists(MW_LOGS . "/error.log")){
	echo '<pre>' . file_get_contents(MW_LOGS . "/error.log") . '</pre>';
}

?>