<?php
class MooWoodle_Testconnection {

	//user data for test
	public $user_data = array();

	function __construct() {
		$this->user_data['email'] = 'moowoodletestuser@gmail.com';
		$this->user_data['username'] = 'moowoodletestuser';
		$this->user_data['password'] = 'moowoodletestuser';
		$this->user_data['auth'] = 'manual';
		$a=get_locale();
		$b=strtolower($a);
		$this->user_data['lang'] = substr($b,0,2);
		$this->user_data['firstname'] = MOOWOODLE_TEXT_DOMAIN;
		$this->user_data['lastname'] = 'testuser';
		$this->user_data['city'] = 'moowoodlecity';
		$this->user_data['country'] = 'IN';
		$this->user_data['preferences'][0]['type'] = "auth_forcepasswordchange";
		$this->user_data['preferences'][0]['value'] = 1;
		$this->testconnection();

	}

	// test connection with moodle server from connection settings
	public function testconnection() {
		add_action('wp_ajax_get_course', array($this, 'get_course'));
		add_action('wp_ajax_get_catagory', array($this, 'get_catagory'));
		add_action('wp_ajax_get_course_by_fuild', array($this, 'get_course_by_fuild'));
		add_action('wp_ajax_create_user', array($this, 'create_user'));
		add_action('wp_ajax_get_user', array($this, 'get_user'));
		add_action('wp_ajax_update_user', array($this, 'update_user'));
		add_action('wp_ajax_enrol_users', array($this, 'enrol_users'));
		add_action('wp_ajax_unenrol_users', array($this, 'unenrol_users'));
		add_action('wp_ajax_delete_users', array($this, 'delete_users'));
	}
	//test get course 
	public function get_course()
	{
		$response = $this->moowoodle_moodle_test_connection_callback( 'get_courses' );
		$response_data['message'] = 'Failed, please check the <a href="'. admin_url("admin.php?page=moowoodle-settings&tab=moowoodle-log") .'"> error log </a>';
		if($response != null){
			if($this->check_connection($response) == 'success'){
				$response_arr = json_decode( $response[ 'body' ], true );
				$response_data['message'] = 'success';
				if ( ! empty( $response_arr ) ){
					foreach ( $response_arr as $course ) {
						if ( $course[ 'format' ] == 'topics' ) {
							$response_data['course_id'] = $course[ 'id' ];
							$response_data['course_empty'] = '';
							break;
						}
						$response_data['course_empty'] = 'create a course on moodle for complet test connection';
					}
				}

			}
		}
		$response_data['user_id'] = $_POST['user_id'];
		wp_send_json($response_data);
	}
	// test get category
	public function get_catagory()
	{
		$response = $this->moowoodle_moodle_test_connection_callback( 'get_categories' );
		$response_data['message'] = 'Failed, please check the <a href="'. admin_url("admin.php?page=moowoodle-settings&tab=moowoodle-log") .'"> error log </a>';
		if($response != null && $this->check_connection($response) == 'success'){
			$response_data['message'] = 'success';
		}
		$response_data['course_empty'] = $_POST['course_empty'];
		$response_data['course_id'] = $_POST['course_id'];
		$response_data['user_id'] = $_POST['user_id'];
		wp_send_json($response_data);
	}
	//  test get course by fuild
	public function get_course_by_fuild()
	{
		$response = $this->moowoodle_moodle_test_connection_callback( 'get_course_by_fuild' );
		$response_data['message'] = 'Failed, please check the <a href="'. admin_url("admin.php?page=moowoodle-settings&tab=moowoodle-log") .'"> error log </a>';
		if($response != null && $this->check_connection($response) == 'success'){
			$response_data['message'] = 'success';
		}
		$response_data['course_empty'] = $_POST['course_empty'];
		$response_data['course_id'] = $_POST['course_id'];
		$response_data['user_id'] = $_POST['user_id'];
		wp_send_json($response_data);
	}

	public function create_user()
	{
		
		
		$response = $this->moowoodle_moodle_test_connection_callback( 'create_users' , array( 'users' => array( $this->user_data ) ) );
		$response_data['message'] = esc_html(__('Failed, please check the',MOOWOODLE_TEXT_DOMAIN)) . ' <a href="'. admin_url("admin.php?page=moowoodle-settings&tab=moowoodle-log") .'"> '.esc_html(__('error log',MOOWOODLE_TEXT_DOMAIN)) . ' </a>';
		if($response != null && ($this->check_connection($response) == 'success' || strpos($this->check_connection($response),'Username already exists'))){
			$response_data['message'] = 'success';
		}
		$response_data['course_empty'] = $_POST['course_empty'];
		$response_data['course_id'] = $_POST['course_id'];
		$response_data['user_id'] = $_POST['user_id'];
		wp_send_json($response_data);
	}

	// test get user
	public function get_user()
	{
		$response = $this->moowoodle_moodle_test_connection_callback( 'get_moodle_users',array( 'criteria' => array( array( 'key' => 'email', 'value' => 'moowoodletestuser@gmail.com' ) ) ) );
		if($response != null && $this->check_connection($response) == 'success'){
			$response_arr = json_decode( $response[ 'body' ], true );
			$response_data['user_id'] = $response_arr['users'][0]['id'];
			$response_data['message'] = 'success';
		}
		$response_data['course_empty'] = $_POST['course_empty'];
		$response_data['course_id'] = $_POST['course_id'];
		wp_send_json($response_data);
		
	}

	// test user update
	public function update_user()
	{
		$this->user_data['id'] = $_POST['user_id'];
		$this->user_data['city'] = 'citymoowoodle';
		$response = $this->moowoodle_moodle_test_connection_callback( 'update_users', array( 'users' => array( $this->user_data ) ) );
		if($response != null && $this->check_connection($response) == 'success'){
			$response_arr = json_decode( $response[ 'body' ], true );
			$response_data['message'] = 'success';
		}
		$response_data['course_empty'] = $_POST['course_empty'];
		$response_data['course_id'] = $_POST['course_id'];
		$response_data['user_id'] = $_POST['user_id'];
		wp_send_json($response_data);
		
	}

	// test enrol users
	public function enrol_users()
	{
		$enrolment['courseid'] = $_POST['course_id'];
		$enrolment['userid'] = $_POST['user_id'];
		$enrolment['roleid'] =  '5';
		$response = $this->moowoodle_moodle_test_connection_callback( 'enrol_users', array( 'enrolments' => array($enrolment) ) );
		$response_data['message'] = 'Failed, please check the <a href="'. admin_url("admin.php?page=moowoodle-settings&tab=moowoodle-log") .'"> error log </a>';
		if($response != null && $this->check_connection($response) == 'success'){
			$response_data['message'] = 'success';
		}
		$response_data['course_empty'] = $_POST['course_empty'];
		$response_data['course_id'] = $_POST['course_id'];
		$response_data['user_id'] = $_POST['user_id'];
		wp_send_json($response_data);
	}

	// test unenrol users
	public function unenrol_users()
	{
		$enrolment['courseid'] = $_POST['course_id'];
		$enrolment['userid'] = $_POST['user_id'];
		$response = $this->moowoodle_moodle_test_connection_callback( 'unenrol_users', array( 'enrolments' => array($enrolment) )  );
		$response_data['message'] = 'Failed, please check the <a href="'. admin_url("admin.php?page=moowoodle-settings&tab=moowoodle-log") .'"> error log </a>';
		if($response != null && $this->check_connection($response) == 'success'){
			$response_data['message'] = 'success';
		}
		$response_data['course_empty'] = $_POST['course_empty'];
		$response_data['course_id'] = $_POST['course_id'];
		$response_data['user_id'] = $_POST['user_id'];
		wp_send_json($response_data);
	}

	// test delete users
	public function delete_users()
	{
		$this->user_data['id'] = $_POST['user_id'];
		$response = $this->moowoodle_moodle_test_connection_callback( 'delete_users' , array( 'userids' =>   array($this->user_data['id']) ) );
		$response_data['message'] = 'Failed, please check the <a href="'. admin_url("admin.php?page=moowoodle-settings&tab=moowoodle-log") .'"> error log </a>';
		if($response != null && $this->check_connection($response) == 'success'){
			$response_data['message'] = 'success';
		}
		$response_data['course_empty'] = $_POST['course_empty'];
		$response_data['course_id'] = $_POST['course_id'];
		$response_data['user_id'] = $_POST['user_id'];
		wp_send_json($response_data);
	}




	/**
	 * get server resposne from moodle with externel service.
	 *
	 * @access private
	 * @param string $function_name (default: null)
	 * @param string $request_param (default: null)
	 * @return mixed
	 */
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
		                                'get_course_by_fuild'  =>  'core_course_get_courses_by_field',
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
		  // file_put_contents(MW_LOGS . "/error.log",date("d/m/Y h:i:s a",time()). ": " ."\n        response:" . ($response['body']) . "\n", FILE_APPEND );
		} 
		return $response;
	}
	/**
	 * check server resposne result .
	 *
	 * @access private
	 * @param string $response (default: null)
	 * @return string
	 */
	private function check_connection($response){
		$conn_settings = get_option( 'moowoodle_general_settings' );
		$url_check = $error_massage = '';
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
						$error_massage = $response_arr['message'] . ' ' . $url_check;
					}
				} else {
					$error_massage = __('Response is not JSON decodeable', MOOWOODLE_TEXT_DOMAIN );
				}
			} else {
				$error_massage = __('Not String response', MOOWOODLE_TEXT_DOMAIN );
			}
		} else {
			if($response[ 'response' ][ 'code' ] == 404){
				$url_check = __('Please check "Moodle Site URL" ||', MOOWOODLE_TEXT_DOMAIN );
			}
			$error_massage = $url_check. __(' error code: ', MOOWOODLE_TEXT_DOMAIN ) . $response[ 'response' ][ 'code' ]. " " . $response['response']['message'];
		}
		file_put_contents(MW_LOGS . "/error.log",date("d/m/Y H:i:s",time()). ": " ."\n        moowoodle error:" . $error_massage."\n", FILE_APPEND );
		return $error_massage;
	}

}