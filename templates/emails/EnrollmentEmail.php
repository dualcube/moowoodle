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

/*
## With is_guest_user == true ##
$email_data = [
	"enrolments"=> [["courseid"=> 2,"userid"=> 12,"roleid"=> 5,"suspend"=> 0,"linked_course_id"=> "217","course_name"=> "Awesome course"]],
	"is_guest_user"=> true,
	"moodle_user"=> [
		"id"=> 13,
		"username"=> "test8@arsahosting.com",
		"firstname"=> "Test",
		"lastname"=> "arsa",
		"fullname"=> "Test arsa",
		"email"=> "test8@arsahosting.com",
		"department"=> "",
		"firstaccess"=> 0,
		"lastaccess"=> 0,
		"auth"=> "manual",
		"suspended"=> false,
		"confirmed"=> true,
		"lang"=> "es",
		"theme"=> "",
		"timezone"=> "99",
		"mailformat"=> 1,
		"country"=> "ES",
		"profileimageurlsmall"=> "https:\/\/cursos.olivoexperto.es\/theme\/image.php\/boost\/core\/1728822171\/u\/f2",
		"profileimageurl"=> "https:\/\/cursos.olivoexperto.es\/theme\/image.php\/boost\/core\/1728822171\/u\/f1"
	],
	"user_email"=> "test8@arsahosting.com"
];

## With is_guest_user == false ##
$email_data = [
	"enrolments"=> [["courseid"=> 2,"userid"=> 12,"roleid"=> 5,"suspend"=> 0,"linked_course_id"=> "217","course_name"=> "Awesome course"]],
	"is_guest_user"=> false,
	"user_email"=> "test8@arsahosting.com"
];
*/

?>
	<p>
	<?php
		if ( ! $moodle_user_id ) {
			if ( !$email_data['is_guest_user'] ) {
				echo __( 'Username : ', 'moowoodle' ) . esc_html__( $user_details->data->user_login ) . '<br>';
				echo __( 'Password : ', 'moowoodle' ) . esc_html__( $password ) . '<br>';
			} else {
				echo __( 'Username : ', 'moowoodle' ) . esc_html__( $email_data['moodle_user']['username'] ) . '<br>';
				echo __( 'You should reset password at : ', 'moowoodle' ) . trailingslashit( MooWoodle()->setting->get_setting( 'moodle_url' ) ) . 'login/forgot_password.php' . '<br>';
			}
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
