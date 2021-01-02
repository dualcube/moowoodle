<?php
class MooWoodle_Emails {

	public function __construct() {
		add_action( 'moowoodle_after_enrol_moodle_user', array( &$this, 'send_moodle_enrollment_confirmation' ) );		
		add_filter( 'woocommerce_email_classes', array( &$this, 'moowoodle_emails' ) );
	}
	
	/**
	 * Woodle emails
	 *
	 * @access public
	 * @param array $emails
   * @return array
	 */
	public function moowoodle_emails( $emails ) {	  
	  $this->load_class( 'new-enrollment' );
	  $emails[ 'MooWoodle_Emails_New_Enrollment' ] = new MooWoodle_Emails_New_Enrollment();
	  
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
		
		$emails[ $email_key ]->trigger( $email_data );
	}
	
	
	/**
	 * Send confirmation for enrollment in moodle course
	 *
	 * @access public
	 * @param array $enrolments
   * @return void
	 */
	public function send_moodle_enrollment_confirmation( $enrolments ) {
		global $MooWoodle, $wpdb;
		
		$conn_settings = $MooWoodle->options_general_settings;
		$moodle_access_url = $conn_settings[ 'moodle_url' ];
		$enrollment_datas = array();

		$user_id = $MooWoodle->enrollment->wc_order->get_user_id();
	    $user = get_userdata( $user_id );
	    $enrollment_datas[ 'email' ] = ( $user == false ) ? '' : $user->user_email;
	    
		$enrollment_data_arr = array();
		foreach ( $enrolments as $enrolment ) {
			$course = get_posts( [ 'meta_key' => 'moodle_course_id', 'meta_value' =>  $enrolment[ 'courseid' ], 'post_type' => 'course', 'post_status' => 'publish' ] );
			$enrollment_data = array();
			$enrollment_data[ 'course_name' ] = $course[0]->post_title;
			
			$course_id_meta = get_post_meta( $course[0]->ID , 'moodle_course_id', true );
			$post_ids = get_posts( [ 'meta_key' => 'moodle_course_id', 'meta_value' => $course_id_meta, 'post_type' => 'product', 'post_status' => 'publish' , 'fields' => 'ids', ] );
        	$post_product_id = $post_ids[0];

        	$linked_course_id_meta = get_post_meta( $post_product_id, 'linked_course_id', true );

			$linked_course_id = ! empty( $linked_course_id_meta ) ? $linked_course_id_meta : '';
			$enrollment_data[ 'linked_course_id' ] = $linked_course_id;
			$enrollment_data_arr[] = $enrollment_data;			
		}
		$enrollment_datas[ 'enrolments' ] = $enrollment_data_arr;
		
		$this->send_email( 'MooWoodle_Emails_New_Enrollment', $enrollment_datas );
	}
	
	/**
	 * Load class file
	 *
	 * @access public
	 * @param string $class_name (default: null)
   * @return void
	 */
	public function load_class( $class_name = '' ) {
	  global $MooWoodle;
		if ( '' != $class_name && '' != $MooWoodle->token ) {
			require_once ( 'emails/class-' . $MooWoodle->token . '-email-' . $class_name . '.php' );
		} // End If Statement
	}// End load_class()
}