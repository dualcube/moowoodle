<?php

class MooWoodle_Manage_Enrolment {
	
	function __construct() {		
		add_action( 'admin_init', array( &$this, 'unenroll' ) );
	}

	/**
	 * manual Unenroll user from manage-enrollment page.
	 *
	 * @access public
	 * @return void
	*/
	public function unenroll() {
		global  $MooWoodle;
		if ( ! isset( $_POST[ 'unenroll' ] ) ) {
			return;
		}
		$order_id =  $_POST['order_id'];
	    $enrolment['courseid'] =  $_POST['course_id'];
		$enrolment['userid'] =  $_POST['user_id'];
		moowoodle_moodle_core_function_callback( 'unenrol_users', array( 'enrolments' => array($enrolment) ) );
		if(get_post_meta($order_id, '_course_unenroled',true) != null){
			$unenrolled_course = get_post_meta($order_id, '_course_unenroled',true);
		}
		$unenrolled_course .= $unenrolled_course != null ? ',' .$enrolment['courseid'] : $enrolment['courseid'];
		
		update_post_meta($order_id, '_course_unenroled', ($unenrolled_course));
		do_action( 'moowoodle_after_unenrol_moodle_user', $enrolment_data );
	}
}