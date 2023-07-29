<?php
// require_once( ABSPATH  . '/wp-content/plugins/moowoodle/includes/moowoodle-core-functions.php');
class MooWoodle_testconnection {
	function __construct() {		
		add_action( 'admin_init', array( &$this, 'testconnection') );
	}

	public function testconnection() {
		// echo get_post_meta(get_posts(array(	'post_title' => 'moowoodle_test_connection', 'post_name' => 'moowoodle_test_connection', 'post_content' => 'MooWoodle_testconnection', 'post_status' => 'private', 'post_type' => 'test', 'numberposts' => -1	))[0]->ID,'_test_connect_submit',true);die;
		// echo $_POST['moowoodle_general_settings[moodle_url]'];die;
		// global $MooWoodle;
		// $moodle_user = moowoodle_moodle_core_function_callback( 'create_users', array( 'users' => array( $user_data ) ) );	

		if ( ! isset( $_POST[ 'test_connection' ] ) ) {
			return;
		}
		$args = array(	'post_title'   => 'moowoodle_test_connection',
						'post_name'	  => 'moowoodle_test_connection',
						'post_content' => 'MooWoodle_testconnection',
						'post_status'  => 'private',
						'post_type'    => 'test'	,
						'numberposts' => -1						
					);
		$posts = get_posts($args);
		unset($args['numberposts']);
		$post_id = $posts[0]->ID;
		if ( $post_id > 0 ) {
			$args[ 'ID' ] = $post_id;
			$new_post_id = wp_update_post( $args );				
		} else {
			$new_post_id = wp_insert_post( $args );
		}	

		file_put_contents(MW_LOGS . "/error.log",date("d/m/Y h:i:s a",time()). ": " . "test_connection: run\n", FILE_APPEND );
		$response = $this->moowoodle_moodle_test_connection_callback( 'get_courses' );
		if($response != null){
			update_post_meta( $new_post_id, '_test_connect_get_courses', $this->check_connection($response));
			if($this->check_connection($response) == 'success'){
				$response_arr = json_decode( $response[ 'body' ], true );
				$course_id = $response_arr[0][ 'id' ];

			}
		}
		$response = $this->moowoodle_moodle_test_connection_callback( 'get_categories' );
		if($response != null){
			update_post_meta( $new_post_id, '_test_connect_get_categories', $this->check_connection($response));
		}
		$response = $this->moowoodle_moodle_test_connection_callback( 'get_course_image' );
		if($response != null){
			update_post_meta( $new_post_id, '_test_connect_get_course_image', $this->check_connection($response));
		}
		$user_data = array();
		$user_data['email'] = 'moowoodletestuser@gmail.com';
		$user_data['username'] = 'moowoodletestuser';
		$user_data['password'] = 'moowoodletestuser';
		$user_data['auth'] = 'manual';
		$a=get_locale();
		$b=strtolower($a);
		$user_data['lang'] = substr($b,0,2);
		$user_data['firstname'] = 'moowoodle';
		$user_data['lastname'] = 'testuser';
		$user_data['city'] = 'moowoodlecity';
		$user_data['country'] = 'IN';
		$user_data['preferences'][0]['type'] = "auth_forcepasswordchange";
		$user_data['preferences'][0]['value'] = 1;
		$response = $this->moowoodle_moodle_test_connection_callback( 'create_users' , array( 'users' => array( $user_data ) ) );
		if($response != null){
			update_post_meta( $new_post_id, '_test_connect_create_users', $this->check_connection($response));
		}
		// if($this->check_connection($response) == 'success'){
			$response = $this->moowoodle_moodle_test_connection_callback( 'get_moodle_users',array( 'criteria' => array( array( 'key' => 'email', 'value' => 'moowoodletestuser@gmail.com' ) ) ) );
			if($response != null){
				$response_arr = json_decode( $response[ 'body' ], true );
				$user_data['id'] = $response_arr['users'][0]['id'];
				update_post_meta( $new_post_id, '_test_connect_get_moodle_users', $this->check_connection($response));
			}
			if($this->check_connection($response) == 'success'){
				if(!empty($user_data['id'])){
					$user_data['city'] = 'citymoowoodle';
					$response = $this->moowoodle_moodle_test_connection_callback( 'update_users', array( 'users' => array( $user_data ) ) );
					if($response != null){
						update_post_meta( $new_post_id, '_test_connect_update_users', $this->check_connection($response));
					}
					if($this->check_connection($response) == 'success'){
						// $enrolment = array();
						$enrolment['courseid'] =  $course_id;
						$enrolment['userid'] = 20;
						$enrolment['roleid'] =  '5';
						// $enrolment['suspend'] =  '0';
						// $enrolments[] = $enrolment;
						// $enrolments[0] = $enrolment;
						// enrolments[0][courseid]=4&enrolments[0][userid]=4 &enrolments[0][roleid]=5&enrolments[0][suspend]=0
						// enrolments[0][courseid]=1&enrolments[0][userid]=37&enrolments[0][roleid]=5&enrolments[0][suspend]=0
						$response = $this->moowoodle_moodle_test_connection_callback( 'enrol_users', array( 'enrolments' => array($enrolment) ) );
						if($response != null){
							update_post_meta( $new_post_id, '_test_connect_enrol_users', $this->check_connection($response));
						}
						if($this->check_connection($response) == 'success'){
							unset($enrolment['suspend']);
							unset($enrolment['roleid']);
							$response = $this->moowoodle_moodle_test_connection_callback( 'unenrol_users', array( 'enrolments' => $enrolments )  );
							if($response != null){
								update_post_meta( $new_post_id, '_test_connect_unenrol_users', $this->check_connection($response));
							}
						}
					}
					$response = $this->moowoodle_moodle_test_connection_callback( 'delete_users' , array( 'userids' =>   array($user_data['id']) ) );
					if($response != null){
						update_post_meta( $new_post_id, '_test_connect_delete_users', $this->check_connection($response));
						// print_r($enrolment);die;
					}
				}
			}
		
		// }

	}

	private function moowoodle_moodle_test_connection_callback( $key = '', $request_param = array() ) {
		global $MooWoodle;

		$response = null;
		$function_name = "";
		$moodle_core_functions = array( 'get_categories'	=> 'core_course_get_categories',
		                                'get_courses'		=> 'core_course_get_courses',
		                                'get_moodle_users'	=> 'core_user_get_users',
		                                'create_users'		=> 'core_user_create_users',
		                                'update_users'		=> 'core_user_update_users',
		                                'enrol_users'		=> 'enrol_manual_enrol_users',
		                                'get_course_image'  =>  'core_course_get_courses_by_field',
		                                'unenrol_users'		=> 'enrol_manual_unenrol_users',
		                                'delete_users'		=> 'core_user_delete_users',
		                              );
		if ( array_key_exists( $key, $moodle_core_functions ) ) {
		  $function_name = $moodle_core_functions[ $key ];
		}

		$conn_settings = get_option( 'moowoodle_general_settings' );
		$url = $conn_settings[ 'moodle_url' ];
		$token = $conn_settings[ 'moodle_access_token' ];
		$request_url = $url . '/webservice/rest/server.php?wstoken=' . $token . '&wsfunction=' . $function_name . '&moodlewsrestformat=json';

		if ( $function_name == 'core_user_get_users' ) {
		  $request_url = $request_url . '&criteria[0][key]=email&criteria[0][value]=%%';
		} 
		    
		if ( ! empty( $url )  && ! empty( $token ) && $function_name != '' ) {
		  $request_query = http_build_query( $request_param );
		  $response = wp_remote_post( $request_url, array(  'body' => $request_query , 'timeout' => $MooWoodle->options_timeout_settings['moodle_timeout']));
		  file_put_contents(MW_LOGS . "/error.log",date("d/m/Y h:i:s a",time()). ": " ."\n        request_url:" . $request_url."\n        body:" . $request_query. "\n        response: " . json_encode($response) ."\n", FILE_APPEND );
		} 
		return $response;
	}
	private function check_connection($response){
		$conn_settings = get_option( 'moowoodle_general_settings' );
		$url_check = '';
		if ( ! is_wp_error( $response ) && $response != null && $response[ 'response' ][ 'code' ] == 200 ) {
			if ( is_string( $response[ 'body' ] ) ) {
				$response_arr = json_decode( $response[ 'body' ], true ); 
				if ( json_last_error() === JSON_ERROR_NONE ) {
					if ( is_null( $response_arr ) || ! array_key_exists( 'exception', $response_arr ) ) {
						return 'success';
					} else {
						
						if(str_contains($response_arr['message'],'Access control exception')){
							$url_check = '<a href="'.$conn_settings[ 'moodle_url' ] . '/admin/settings.php?section=externalservices">Link</a>'; 
						}
						if(str_contains($response_arr['message'],'Invalid token')){
							$url_check = '<a href="'.$conn_settings[ 'moodle_url' ] . '/admin/webservice/tokens.php">Link</a>'; 
						}
						return $response_arr['message'] . ' ' . $url_check;
					}
				} else {
					return 'Response is not JSON decodeable';
				}
			} else {
				return 'Not String response';
			}
		} else {
			if($response[ 'response' ][ 'code' ] == 404){
				$url_check = 'Please check "Moodle Site URL" ||';
			}
			return $url_check.' error code: ' . $response[ 'response' ][ 'code' ]. " " . $response['response']['message'];
		} 
	}

}