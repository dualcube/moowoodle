<?php
/**
 * New enrollment email (plain)
 */

defined( 'ABSPATH' ) || exit();

$user_details         = get_user_by( 'email', $user_email );
$user_id              = $user_details->ID ?? 0;
$username             = $user_details->user_login ?? '';
$first_name           = $user_details->first_name ?? '';
$moodle_user_id       = get_user_meta( $user_id, 'moowoodle_moodle_user_id', true );
$wp_user_created      = get_user_meta( $user_id, 'moowoodle_wordpress_new_user_created', true );
$moodle_user_created  = get_user_meta( $user_id, 'moowoodle_moodle_new_user_created', true );
$passwordWordpress    = get_user_meta( $user_id, 'moowoodle_wordpress_user_pwd', true );
$passwordMoowoodle    = get_user_meta( $user_id, 'moowoodle_moodle_user_pwd', true );

$site_name = get_bloginfo( 'name' );
$site_url  = home_url();
$my_courses_link = wc_get_page_permalink( 'myaccount' ) . 'my-courses/';

echo "Hi {$first_name},\n\n";
echo "Welcome to {$site_name}! We’re excited to have you onboard.\n\n";
echo "An account has been created for you on our learning platform so you can begin your journey with us.\n";
echo "Below are your login details:\n\n";

echo "Website: {$site_url}\n";
echo "Username: {$username}\n";

if ( $wp_user_created && $moodle_user_created && $passwordWordpress === $passwordMoowoodle ) {
	echo "Password: {$passwordWordpress}\n";
	echo "This password will work for both your WordPress and Moodle accounts.\n";
	echo "You will be required to change your Moodle password after your first login.\n\n";
} else {
	if ( $wp_user_created ) {
		echo "WordPress Password: {$passwordWordpress}\n";
	}
	if ( $moodle_user_created ) {
		echo "Moodle Password: {$passwordMoowoodle}\n";
		echo "Note: You will be required to change your Moodle password after your first login.\n";
	}
	echo "\n";
}

$group_name = $enrollments['group_name'] ?? '';
if ( $group_name ) {
	echo "Classroom: {$group_name}\n\n";
}

echo "You are enrolled in the following course(s):\n";
foreach ( $enrollments['enrolments'] as $enrollment ) {
	$course_title = get_the_title( $enrollment['courseid'] );
	echo "- {$course_title}\n";
}
echo "\n";

echo "To access your courses, please visit: {$my_courses_link}\n\n";

echo "If you have any questions or face any issues logging in, feel free to reach out to our support team at support@" . parse_url( $site_url, PHP_URL_HOST ) . "\n\n";

echo "Wishing you a great learning experience!\n";

// Cleanup
if ( $user_id ) {
	delete_user_meta( $user_id, 'moowoodle_wordpress_new_user_created' );
	delete_user_meta( $user_id, 'moowoodle_moodle_new_user_created' );
}
?>
