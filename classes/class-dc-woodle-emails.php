<?php
class DC_Woodle_Emails {

	public function __construct() {
		add_action( 'woodle_after_create_moodle_user', array( &$this, 'send_moodle_user_credentials' ) );
		add_action( 'woodle_after_enrol_moodle_user', array( &$this, 'send_moodle_enrollment_confirmation' ) );
		
		add_filter( 'woocommerce_email_classes', array( &$this, 'woodle_emails' ) );
	}
	
	/**
	 * Woodle emails
	 *
	 * @access public
	 * @param array $emails
   * @return array
	 */
	public function woodle_emails( $emails ) {
	  $this->load_class('new-account');
	  $emails['DC_Woodle_Emails_New_Account'] = new DC_Woodle_Emails_New_Account();
	  
	  $this->load_class('new-enrollment');
	  $emails['DC_Woodle_Emails_New_Enrollment'] = new DC_Woodle_Emails_New_Enrollment();
	  
	  return $emails;
	}
	
	/**
	 * Send email
	 *
	 * @access public
	 * @param string $email_key (default: null)
	 * @param array $email_data (default: array)
   * @return void
	 */
	public function send_email( $email_key = '', $email_data = array() ) {
		$emails = WC()->mailer()->get_emails();
		
		if( empty( $email_key ) || ! array_key_exists( $email_key, $emails ) ) {
			return;
		}
		
		$emails[$email_key]->trigger( $email_data );
	}
	
	/**
	 * Send moodle account credentials
	 *
	 * @access public
	 * @param array $user_data
   * @return void
	 */
	public function send_moodle_user_credentials( $user_data ) {
		$this->send_email( 'DC_Woodle_Emails_New_Account', $user_data );
	}
	
	/**
	 * Send confirmation for enrollment in moodle course
	 *
	 * @access public
	 * @param array $enrolments
   * @return void
	 */
	public function send_moodle_enrollment_confirmation( $enrolments ) {
		global $DC_Woodle;
		
		$moodle_access_url = woodle_get_settings( 'access_url', 'dc_woodle_general' );
		$enrollment_data = array();
		foreach( $enrolments as $enrolment ) {
			$post_id = woodle_get_post_by_moodle_id( $enrolment['courseid'], 'course' );
			$course = get_post( $post_id );
			$enrollment_data['email'] = $DC_Woodle->enrollment->wc_order->billing_email;
			$enrollment_data['course_name'] = $course->post_title;
			$enrollment_data['course_url'] = "{$moodle_access_url}/course/view.php?id={$enrolment['courseid']}";
		}
		$this->send_email( 'DC_Woodle_Emails_New_Enrollment', $enrollment_data );
	}
	
	/**
	 * Load class file
	 *
	 * @access public
	 * @param string $class_name (default: null)
   * @return void
	 */
	public function load_class($class_name = '') {
	  global $DC_Woodle;
		if ('' != $class_name && '' != $DC_Woodle->token) {
			require_once ('emails/class-' . esc_attr($DC_Woodle->token) . '-email-' . esc_attr($class_name) . '.php');
		} // End If Statement
	}// End load_class()
}