<?php
/**
 * New enrollment email (plain)
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; 

global $DC_Woodle;
$i = 0;

$enrollment_list = array();

?> <p> <?php

	$user_details = get_user_by( 'email', $user_data ); 
	echo 'Username : ' . $user_details->data->user_login . '<br><br>';
	echo 'Password : 1Admin@23 <br><br>';
	echo 'To enroll and access your course please click on the course link given below :<br><br>';

?> </p> <?php

foreach( $enrollments['enrolments'] as $enrollment ) {

	$enrollment_list[] = do_shortcode( $enrollment['course_url'] . '' . $enrollment['course_name'] . '[/moowoodle]' );
	
	?> <p> <?php echo 'You are enrolled in '.$enrollment_list[$i].' <br><br>'; ?> </p> <?php
	$i++;
}

?> <p> <?php echo 'You need to change your password after first login. <br><br>'; ?> </p>