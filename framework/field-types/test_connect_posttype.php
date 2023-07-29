<?php 
	global $MooWoodle_test_connection,$MooWoodle;
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
			
			$post_metas = get_post_meta( $posts[0]->ID);
			$no_error = true;
			foreach($post_metas as $key=>$val)
			{
				if($val[0] != 'success'){
					echo $val[0] . '<br/>';
					$no_error = false;
				}
			    
			}
			if($no_error){
				echo "Connection Tested Success : OK";
			}
		}
	
 ?>