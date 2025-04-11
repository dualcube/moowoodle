<?php
/**
 * New enrollment email (HTML)
 */

defined( 'ABSPATH' ) || exit();
do_action( 'woocommerce_email_header', $email_heading );

// === USER INFO ===
$user_details         = get_user_by( 'email', $user_email );
$user_id              = $user_details->ID ?? 0;
$first_name           = $user_details->first_name ?? '';
$username             = $user_details->user_login ?? '';
$wp_user_created      = get_user_meta( $user_id, 'moowoodle_wordpress_new_user_created', true );
$moodle_user_created  = get_user_meta( $user_id, 'moowoodle_moodle_new_user_created', true );
$passwordWordpress             = get_user_meta( $user_id, 'moowoodle_wordpress_user_pwd', true );
$passwordMoowoodle             = get_user_meta( $user_id, 'moowoodle_moodle_user_pwd', true );

$site_name = get_bloginfo('name');
$site_url = home_url();
$my_courses_link = wc_get_page_permalink( 'myaccount' ) . 'my-courses/';
?>

<p>Hi <?php echo esc_html( $first_name ); ?>,</p>

<p>Welcome to <strong><?php echo esc_html( $site_name ); ?></strong>! We’re excited to have you onboard.</p>

<p>
	An account has been created for you on our learning platform so you can begin your journey with us.
	Below are your login details:
</p>

<h3>Your Account Information</h3>
<p>
	<strong>Website:</strong> <a href="<?php echo esc_url( $site_url ); ?>"><?php echo esc_html( $site_url ); ?></a><br>
	<strong>Username:</strong> <?php echo esc_html( $username ); ?><br>

	<?php if ( $wp_user_created && $moodle_user_created && $passwordWordpress === $passwordMoowoodle ) : ?>
		<strong>Password:</strong> <?php echo esc_html( $passwordWordpress ); ?><br>
		<em>This password will work for both your WordPress and Moodle accounts. You will be required to change your Moodle password after your first login.</em><br>

	<?php else : ?>

		<?php if ( $wp_user_created ) : ?>
			<strong>WordPress Password:</strong> <?php echo esc_html( $passwordWordpress ); ?><br>
		<?php endif; ?>

		<?php if ( $moodle_user_created ) : ?>
			<strong>Moodle Password:</strong> <?php echo esc_html( $passwordMoowoodle ); ?><br>
			<em>Note: You will be required to change your Moodle password after your first login.</em><br>
		<?php endif; ?>

	<?php endif; ?>
</p>


<h3>Enrollment Details</h3>
<?php
$group_name = $enrollments['group_name'] ?? '';

if ( $group_name ) :
?>
	<p><strong>Classroom:</strong> <?php echo esc_html( $group_name ); ?></p>
<?php endif; ?>

<p><strong>Course(s):</strong></p>
<ul>
	<?php foreach ( $enrollments['enrolments'] as $enrollment ) :
		$course_id = $enrollment['courseid'];
		$course_title = get_the_title( $course_id );
	?>
		<li><?php echo esc_html( $course_title ); ?></li>
	<?php endforeach; ?>
</ul>

<h3>Access Your Courses</h3>
<p>
	To get started with your courses, please click the link below:<br>
	👉 <a href="<?php echo esc_url( $my_courses_link ); ?>" target="_blank"><?php echo esc_html( $my_courses_link ); ?></a>
</p>


<p>
	If you have any questions or face any issues logging in, feel free to reach out to our support team at <a href="mailto:support@<?php echo parse_url( $site_url, PHP_URL_HOST ); ?>">support@<?php echo parse_url( $site_url, PHP_URL_HOST ); ?></a>.
</p>

<p>Wishing you a great learning experience!</p>

<?php
// Cleanup temporary flags
if ( $user_id ) {
	delete_user_meta( $user_id, 'moowoodle_wordpress_new_user_created' );
	delete_user_meta( $user_id, 'moowoodle_moodle_new_user_created' );
}
?>

<?php do_action( 'woocommerce_email_footer' ); ?>
