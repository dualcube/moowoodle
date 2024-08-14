<?php
/**
 * New enrollment email (plain)
 */

defined( 'ABSPATH' ) || exit();

$user_details 	 = get_user_by( 'email', $user_email );
$user_id 		 = $user_details->data->ID;
$moodle_user_id  = get_user_meta( $user_id, 'moowoodle_moodle_user_id', true );
$password 		 = get_user_meta( $user_id, 'moowoodle_moodle_user_pwd', true );

if ( ! $moodle_user_id ) {
	echo __( 'Username : ', 'moowoodle' ) . esc_html( $user_details->data->user_login ) . '\n\n';
	echo __( 'Password : ', 'moowoodle' ) . esc_html( $password ) . '\n\n';
}

echo __( 'To enroll and access your course please click on the course link given below :', 'moowoodle' ) . '\n\n';

foreach ( $enrollments[ 'enrolments' ] as $enrollment ) {

	$course_url = MooWoodle()->course->get_course_url( $enrollment[ 'courseid' ], $enrollment[ 'course_name' ] );
	echo( 'You are enrolled in ' . $course_url ) . ' \n\n';
}

if ( ! $moodle_user_id ) {
	echo __( 'You need to change your password after first login.', 'moowoodle' ) . '\n\n';
}
