<?php

namespace MooWoodle\Emails;

class EnrollmentEmail extends \WC_Email {
	public $recipient = '';
	public $email_data;

	function __construct() {
		$this->id 			  = 'new_moodle_enrollment';
		$this->title 		  = __( 'New Moodle Enrollment', 'moowoodle' );
		$this->description 	  = __( 'This is a notification email sent to the enrollees for new enrollment.', 'moowoodle' );
		$this->heading 		  = __( 'New Enrollment', 'moowoodle' );
		$this->template_html  = 'emails/EnrollmentEmail.php';
		$this->template_plain = 'emails/plain/EnrollmentEmail.php';
		$this->template_base  = MooWoodle()->plugin_path . 'templates/';
		
		// Call parent constructor
		parent::__construct();
	}

	/**
	 * Trigger email.
	 * @return void
	 */
	public function trigger( $recipient, $email_data ) {

		$this->customer_email = $recipient;
		$this->recipient 	  = $recipient;
		$this->email_data 	  = $email_data;
		
		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * Get email subject.
	 *
	 * @since  1.4.7
	 * @return string
	 */
	public function get_default_subject() {
		return apply_filters( 'moowoodle_enrollment_email_subject', __( 'New Moodle Enrollment', 'moowoodle' ) );
	} 

	/**
	 * Get email heading.
	 *
	 * @since  1.4.7
	 * @return string
	 */
	public function get_default_heading() {
		return apply_filters( 'moowoodle_enrollment_email_heading', __( 'Welcome to {site_title} ', 'moowoodle' ) );
	} 

	/**
	 * get_content_html function.
	 * @return string
	 */
	function get_content_html() {
		ob_start();

		wc_get_template( $this->template_html, [
			'enrollments' 	=> $this->email_data,
			'user_email' 	=> $this->recipient,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text' 	=> false,
		], '', $this->template_base );

		return ob_get_clean();
	}

	/**
	 * get_content_plain function.
	 * @return string
	 */
	function get_content_plain() {
		ob_start();

		wc_get_template( $this->template_plain, [
			'enrollments' 	=> $this->email_data,
			'user_email' 	=> $this->recipient,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text' 	=> true,
		], '', $this->template_base );

		return ob_get_clean();
	}
}
