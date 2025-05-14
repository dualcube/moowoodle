<?php

namespace MooWoodle\Emails;

class EnrollmentEmail extends \WC_Email {
	public $recipient = '';
	public $email_data;

	function __construct() {
		$this->id             = 'new_moodle_enrollment';
		$this->title          = __( 'New Moodle Enrollment', 'moowoodle' );
		$this->description    = __( 'This is a notification email sent to the enrollees for new enrollment.', 'moowoodle' );
		$this->heading        = __( 'Welcome to {site_title}!', 'moowoodle' );
		$this->template_html  = 'emails/EnrollmentEmail.php';
		$this->template_plain = 'emails/plain/EnrollmentEmail.php';
		$this->template_base  = MooWoodle()->plugin_path . 'templates/';

		parent::__construct();
	}

	public function trigger( $recipient, $email_data ) {
		$this->customer_email = $recipient;
		$this->recipient      = $recipient;
		$this->email_data     = $email_data;

		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->send(
			$this->get_recipient(),
			$this->get_subject(),
			$this->get_content(),
			$this->get_headers(),
			$this->get_attachments()
		);

		$user = get_user_by( 'email', $recipient );
		if ( $user && $user->ID ) {
			delete_user_meta( $user->ID, 'moowoodle_wordpress_new_user_created' );
			delete_user_meta( $user->ID, 'moowoodle_moodle_new_user_created' );
		}
		
	}

	public function get_default_subject() {
		$site_name = get_bloginfo('name');
		return apply_filters(
			'moowoodle_enrollment_email_subject',
			sprintf( __( 'Welcome to %s! Your Account and Course Access Details', 'moowoodle' ), $site_name )
		);
	}

	public function get_default_heading() {
		$site_name = get_bloginfo('name');
		return apply_filters(
			'moowoodle_enrollment_email_heading',
			sprintf( __( 'Welcome to %s!', 'moowoodle' ), $site_name )
		);
	}

	function get_content_html() {
		ob_start();
		MooWoodle()->util->get_template($this->template_html,
		[
			'enrollments'   => $this->email_data,
			'user_email'    => $this->recipient,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => false,
		]);

		return ob_get_clean();
	}

	function get_content_plain() {
		ob_start();
		MooWoodle()->util->get_template($this->template_plain,
		[
			'enrollments'   => $this->email_data,
			'user_email'    => $this->recipient,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => true,
		]);
		return ob_get_clean();
	}
}
