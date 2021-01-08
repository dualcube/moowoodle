<?php
/**
 * New enrollment email (html)
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; 

$i = 0;

do_action( 'woocommerce_email_header', $email_heading );  
$enrollment_list = array();
?>
<p>
	<?php
		$user_details = get_user_by( 'email', $user_data );
		$pwd = get_user_meta( $user_details->data->ID , '_moodle_user_pwd', true );
		echo esc_html__( 'Username : ', 'moowoodle' ) . esc_html__( $user_details->data->user_login ) . '<br><br>';
		echo esc_html__( 'Password : ', 'moowoodle' ) . esc_html__( $pwd ) . '<br><br>';
		echo esc_html__( 'To access your course please click on the course link given below :', 'moowoodle' ) . '<br><br>';
	?> 
</p>
<?php

foreach( $enrollments[ 'enrolments' ] as $enrollment ) {
	$enrollment_list[] = get_moowoodle_course_url( $enrollment[ 'linked_course_id' ], $enrollment[ 'course_name' ] );
?> 
	<p> 
		<?php echo esc_html__( 'To access your course ', 'moowoodle' ) . $enrollment_list[ $i ].' <br><br>'; ?> 
	</p> 
<?php 
	$i++;
}

?> 
<p> 
	<?php echo esc_html__( 'You need to change your password after first login.', 'moowoodle' ) . '<br><br>'; ?> 
</p> 
<?php
do_action( 'woocommerce_email_footer' ); 