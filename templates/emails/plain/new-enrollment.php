<?php
/**
 * New enrollment email (plain)
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; 

$i = 0;

$enrollment_list = array();

	$user_details = get_user_by( 'email', $user_data ); 
	echo esc_html( 'Username : ' ) . esc_html( $user_details->data->user_login ) . '\n\n';
	echo esc_html( 'Password : 1Admin@23' ) . '\n\n';
	echo esc_html( 'To enroll and access your course please click on the course link given below :') . '\n\n';

foreach ( $enrollments[ 'enrolments' ] as $enrollment ) {
	$enrollment_list[] = apply_filters( 'moowoodle_course_url', $enrollment[ 'linked_course_id' ], $enrollment[ 'course_name' ] );
	echo esc_html( 'You are enrolled in '.$enrollment_list[ $i ] ).' \n\n';
	$i++;
}

echo esc_html( 'You need to change your password after first login.') . '\n\n' ;