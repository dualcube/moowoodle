<?php
/**
 * New enrollment email (html)
 */

defined( 'ABSPATH' ) || exit();

do_action( 'woocommerce_email_header', $email_heading );

$user_details 	 = get_user_by( 'email', $user_email );
$user_id 		 = $user_details->data->ID;
$moodle_user_id  =  get_user_meta( $user_id, 'moowoodle_moodle_user_id', true );
$password 		 = get_user_meta( $user_id, 'moowoodle_moodle_user_pwd', true );

?>
	<p>
	<?php
		if ( ! $moodle_user_id ) {
			echo __( 'Username : ', 'moowoodle' ) . esc_html( $user_details->data->user_login );
			echo __( 'Password : ', 'moowoodle' ) . esc_html( $password ) ;
		}

		echo __( 'To enroll and access your course please click on the course link given below :', 'moowoodle' );
	?>
	</p>
<?php

foreach ( $enrollments[ 'enrolments' ] as $enrollment ) {
	$course_url = MooWoodle()->course->get_course_url( $enrollment[ 'courseid' ], $enrollment[ 'course_name' ] );
	?>
		<p>
		<?php echo( 'You are enrolled in ' . $course_url ); ?>
		</p>
	<?php
}


if ( ! get_user_meta( $moodle_user_id, 'moowoodle_moodle_user_id' ) ) {
	?>
		<p>
		<?php echo __( 'You need to change your password after first login.', 'moowoodle' ); ?>
		</p>
	<?php
}

do_action( 'woocommerce_email_footer' );
