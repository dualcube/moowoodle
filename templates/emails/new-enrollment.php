<?php
/**
 * New enrollment email (html)
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; 
global $DC_Woodle;
$i = 0;

do_action('woocommerce_email_header', $email_heading);  
$enrollment_list = array();
?> <p> <?php
		$user_details = get_user_by( 'email', $user_data );
		echo esc_html__( 'Username : ', 'dc-woodle' ) . esc_html__( $user_details->data->user_login ) . '<br><br>';
		echo esc_html__( 'Password : 1Admin@23 <br><br>', 'dc-woodle' );
		echo esc_html__( 'To enroll and access your course please click on the course link given below :<br><br>', 'dc-woodle' );
?> </p> <?php

foreach( $enrollments['enrolments'] as $enrollment ) {
	$enrollment_list[] = do_shortcode( $enrollment['course_url'] . '' . $enrollment['course_name'] . '[/moowoodle]' );
	?> <p> <?php echo esc_html__( 'To access your course ', 'dc-woodle' ) . $enrollment_list[$i].' <br><br>'; ?> </p> <?php 
	$i++;
}

?> <p> <?php echo esc_html__( 'You need to change your password after first login. <br><br>', 'dc-woodle' ); ?> </p> <?php
do_action( 'woocommerce_email_footer' ); 