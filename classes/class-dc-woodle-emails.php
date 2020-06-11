<?php
class DC_Woodle_Emails {

	public function __construct() {
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
	 * Send confirmation for enrollment in moodle course
	 *
	 * @access public
	 * @param array $enrolments
   * @return void
	 */
	public function send_moodle_enrollment_confirmation( $enrolments ) {
		global $DC_Woodle, $wpdb;
		
		$moodle_access_url = woodle_get_settings( 'access_url', 'dc_woodle_general' );
		$enrollment_datas = array();
		$enrollment_datas['email'] = $DC_Woodle->enrollment->wc_order->billing_email;
		$enrollment_data_arr = array();
		foreach( $enrolments as $enrolment ) {
			$post_id = woodle_get_post_by_moodle_id( $enrolment['courseid'], 'course' );
			$course = get_post( $post_id );
			$enrollment_data = array();
			$enrollment_data['course_name'] = $course->post_title;
			
			$course_id_meta = get_post_meta( $post_id , '_course_id', true );
			$post_id_query = $wpdb->get_results("SELECT post_id FROM $wpdb->postmeta WHERE (meta_key = '_course_id' AND meta_value = '". $course_id_meta ."' )");
			foreach ($post_id_query as $key => $value) {
				if(	get_post_type( $value->post_id ) == 'product' ){
					$post_product_id = $value->post_id;
				}
			}
			$product_course_id = !empty(get_post_meta($post_product_id, 'product_course_id', true)) ? get_post_meta($post_product_id, 'product_course_id', true) : '';
			$cohert_id = !empty(get_post_meta($post_product_id, '_cohert_id', true)) ? get_post_meta($post_product_id, '_cohert_id', true) : '';
			$group_id = !empty(get_post_meta($post_product_id, '_group_id', true)) ? get_post_meta($post_product_id, '_group_id', true) : '';
			
			$enrollment_data['course_url'] = '[moowoodle cohort="'.$cohert_id.'" group="'.$group_id.'" course="'.$product_course_id.'" class="moowoodle" target="_self" authtext="" activity="0"]';
			//$enrollment_data['userid'] = $enrolment['userid'];
			$enrollment_data_arr[] = $enrollment_data;
		}
		$enrollment_datas['enrolments'] = $enrollment_data_arr;
		
		$this->send_email( 'DC_Woodle_Emails_New_Enrollment', $enrollment_datas );
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