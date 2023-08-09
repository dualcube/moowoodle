<div class="test-connection-status">
	

</div>


<?php 
	global $MooWoodle_test_connection,$MooWoodle;
	if(isset($_POST['get_courses'])){
		echo 'hi';
	}

	$conn_settings = $MooWoodle->options_general_settings;
	    $url = $conn_settings[ 'moodle_url' ];

	    $token = $conn_settings[ 'moodle_access_token' ];
		if($url == '' || $token == ''){
			echo 'Enter URL and Token';
		}else{
			$args = array(	'post_title'   => 'moowoodle_test_connection',
						'post_name'	  => 'moowoodle_test_connection',
						'post_content' => 'MooWoodle_testconnection',
						'post_status'  => 'private',
						'post_type'    => 'test'					
					);
			$posts = get_posts($args);
			$errormsg = '';
			$post_metas = get_post_meta( $posts[0]->ID);
			if($post_metas){
				// print_r($post_metas['_test_connect_submit']);die;
				if($post_metas['_test_connect_submit'][0] == 'success'){
					update_post_meta( $posts[0]->ID, '_test_connect_submit', '');
					foreach($post_metas as $key=>$val)
					{
						if($val[0] != 'success'){
							if(!str_contains($errormsg,$val[0])){
								$errormsg .= '* ' . $val[0] . '<br/>';
							}
						}
					    
					}
					if(empty($errormsg)){
						echo 'Connection Tested : OK';
					}else{
						echo $errormsg;
					}
				}
			} else {
				echo 'complete setup and test connection';
			}
			
			
		}

 ?>