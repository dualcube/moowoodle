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
		echo 'Username : ' . $user_details->data->user_login . '<br><br>';
		echo 'Password : 1Admin@23 <br><br>';
		echo 'To enroll and access your course please click on the course link given below :<br><br>';
?> </p> <?php

foreach( $enrollments['enrolments'] as $enrollment ) {
	$enrollment_list[] = do_shortcode( $enrollment['course_url'] . '' . $enrollment['course_name'] . '[/moowoodle]' );
	?> <p> <?php echo 'To access your course '.$enrollment_list[$i].' <br><br>'; ?> </p> <?php 
	$i++;
}

?> <p> <?php echo 'You need to change your password after first login. <br><br>'; ?> </p> <?php
do_action( 'woocommerce_email_footer' ); 