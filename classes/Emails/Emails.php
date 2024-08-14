<?php

namespace MooWoodle\Emails;

class Emails {
	public function __construct() {
		add_action( 'moowoodle_after_enrol_moodle_user', [ &$this, 'send_enrollment_confirmation' ], 10, 2 );
		add_filter( 'woocommerce_email_classes', [ &$this, 'moowoodle_emails' ] );
	}

	/**
	 * MooWoodle emails
	 * @param array $emails
	 * @return array
	 */
	public function moowoodle_emails( $emails ) {
		$emails[ 'EnrollmentEmail' ] = new EnrollmentEmail();
		return $emails;
	}

	/**
	 * Send email
	 * @param string $email_key (default: null)
	 * @param array $email_data (default: array)
	 * @return bool | string
	 */
	public function send_email( $email_key = '', $user_email = '', $email_data = [] ) {
		$emails = WC()->mailer()->get_emails();

		if ( empty( $email_key ) || ! array_key_exists( $email_key, $emails ) ) {
			return false;
		}

		return $emails[ $email_key ]->trigger( $user_email, $email_data );
	}

	/**
	 * Send confirmation for enrollment in moodle course
	 * @param array $enrolments
	 * @return void
	 */
	public function send_enrollment_confirmation( $enrolments, $user_id ) {
		$enrollment_datas 				= [];
		$user 							= get_userdata($user_id);
		$user_email 					= ($user == false) ? '' : $user->user_email;
		$enrollment_datas['enrolments'] = $enrolments;
		
		$this->send_email( 'EnrollmentEmail', $user_email, $enrollment_datas );
	}
}
