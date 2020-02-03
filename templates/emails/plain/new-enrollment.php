<?php
/**
 * New enrollment email (plain)
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; 
global $DC_Woodle;

$enrollment_list = array();
foreach( $enrollments['enrolments'] as $enrollment ) {

	$enrollment_list[] = do_shortcode( $enrollment['course_url'] . ' activity="0"]' . $enrollment['course_name'] . '[/moowoodle]' );

}
?>
<p>
	<?php
		$user_details = get_user_by( 'email', $user_data ); 
		
		echo 'Username : ' . $user_details->data->user_login . '<br><br>';
		echo 'Password : 1Admin@23 <br><br>';
		echo 'You are enrolled in '.$enrollment_list[0].' <br><br>';
		echo 'You need to change your password after first login.';
	?>
</p>