<?php
/**
 * New enrollment email (HTML)
 */

defined( 'ABSPATH' ) || exit();

do_action( 'woocommerce_email_header', $email_heading );

$user = get_user_by( 'email', $args['user_email'] );
?>

<p>Hi <?php echo esc_html( $user->first_name ?? '' ); ?>,</p>

<p>Welcome to <strong><?php echo esc_html( get_bloginfo( 'name' ) ); ?></strong>! We’re excited to have you onboard.</p>

<p>
	An account has been created for you on our learning platform so you can begin your journey with us.
	Below are your login details:
</p>

<h3>Your Account Information</h3>
<p>
	<strong>Website:</strong> <a href="<?php echo esc_url( home_url() ); ?>"><?php echo esc_html( home_url() ); ?></a><br>
	<strong>Username:</strong> <?php echo esc_html( $user->user_login ?? '' ); ?><br>

	<?php
	$wp_pwd = get_user_meta( $user->ID ?? 0, 'moowoodle_wordpress_user_pwd', true );
	$moodle_pwd = get_user_meta( $user->ID ?? 0, 'moowoodle_moodle_user_pwd', true );
	$wp_created = get_user_meta( $user->ID ?? 0, 'moowoodle_wordpress_new_user_created', true );
	$moodle_created = get_user_meta( $user->ID ?? 0, 'moowoodle_moodle_new_user_created', true );

	if ( $wp_created && $moodle_created && $wp_pwd === $moodle_pwd ) : ?>
		<strong>Password:</strong> <?php echo esc_html( $wp_pwd ); ?><br>
		<em>This password will work for both your WordPress and Moodle accounts. You will be required to change your Moodle password after your first login.</em><br>
	<?php else : ?>
		<?php if ( $wp_created ) : ?>
			<strong>WordPress Password:</strong> <?php echo esc_html( $wp_pwd ); ?><br>
		<?php endif; ?>
		<?php if ( $moodle_created ) : ?>
			<strong>Moodle Password:</strong> <?php echo esc_html( $moodle_pwd ); ?><br>
			<em>Note: You will be required to change your Moodle password after your first login.</em><br>
		<?php endif; ?>
	<?php endif; ?>
</p>

<h3>Enrollment Details</h3>
<?php if ( ! empty( $args['enrollments']['gift_email'] ) ) : ?>
	<p>
		This enrollment was gifted by <strong><?php echo esc_html( $args['enrollments']['gift_email'] ); ?></strong>.
	</p>
<?php elseif ( ! empty( $args['enrollments']['teacher_email'] ) ) : ?>
	<p>
		You have been enrolled by <strong><?php echo esc_html( $args['enrollments']['teacher_email'] ); ?></strong>.
	</p>
<?php endif; ?>

<?php if ( ! empty( $args['enrollments']['group_details'] ) ) : ?>
	<p><strong>Group(s):</strong></p>
	<ul>
		<?php foreach ( $args['enrollments']['group_details'] as $group ) : ?>
			<li><?php echo esc_html( $group['name'] ); ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?php if ( ! empty( $args['enrollments']['cohort_details'] ) ) : ?>
	<p><strong>Cohort(s):</strong></p>
	<ul>
		<?php foreach ( $args['enrollments']['cohort_details'] as $cohort ) : ?>
			<li><?php echo esc_html( $cohort['name'] ); ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?php if ( ! empty( $args['enrollments']['classroom_details'] ) && empty( $args['enrollments']['teacher_email'] ) ) : ?>
	<p><strong>Classroom:</strong> <?php echo esc_html( $args['enrollments']['classroom_details'][0]['name'] ); ?></p>
<?php endif; ?>

<?php if ( ! empty( $args['enrollments']['course_details'] ) ) : ?>
	<p><strong>Course(s):</strong></p>
	<ul>
		<?php foreach ( $args['enrollments']['course_details'] as $course ) : ?>
			<li><?php echo esc_html( $course['fullname'] ); ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>

<h3>Access Your Courses</h3>
<p>
	To get started with your courses, please click the link below:<br>
	👉 <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) . 'my-courses/' ); ?>" target="_blank"><?php echo esc_html( wc_get_page_permalink( 'myaccount' ) . 'my-courses/' ); ?></a>
</p>

<p>
	If you have any questions or face any issues logging in, feel free to reach out to our support team at 
	<a href="mailto:<?php echo esc_attr( 'support@' . parse_url( home_url(), PHP_URL_HOST ) ); ?>"><?php echo esc_html( 'support@' . parse_url( home_url(), PHP_URL_HOST ) ); ?></a>.
</p>

<p>Wishing you a great learning experience!</p>

<?php do_action( 'woocommerce_email_footer' ); ?>