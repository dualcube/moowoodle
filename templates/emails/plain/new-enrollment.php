<?php
/**
 * New enrollment email (plain)
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; 

$i = 0;

$enrollment_list = array();

	$user_details = get_user_by( 'email', $user_data ); 
	$pwd = get_user_meta( $user_details->data->ID , 'moowoodle_moodle_user_pwd', true );
	$count_course_order = moodle_customer_created_orders_count($user_details->data->ID);
	if ($count_course_order && $count_course_order < 2) {
		echo esc_html( 'Username : ', 'moowoodle' ) . esc_html( $user_details->data->user_login ) . '\n\n';
		echo esc_html( 'Password : ', 'moowoodle' ) .esc_html( $pwd ) . '\n\n';
	}
	echo esc_html( 'To enroll and access your course please click on the course link given below :', 'moowoodle' ) . '\n\n';

foreach ( $enrollments[ 'enrolments' ] as $enrollment ) {
	$enrollment_list[] = get_moowoodle_course_url( $enrollment[ 'linked_course_id' ], $enrollment[ 'course_name' ] );
	echo esc_html( 'You are enrolled in '.$enrollment_list[ $i ] ).' \n\n';
	$i++;
}
if ($count_course_order && $count_course_order < 2) {
	echo esc_html( 'You need to change your password after first login.', 'moowoodle' ) . '\n\n' ;
}