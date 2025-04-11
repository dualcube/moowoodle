<?php

namespace MooWoodle\Emails;

class Emails {
	public function __construct() {
		add_action( 'moowoodle_after_successful_enrollments', [ &$this, 'send_enrollment_confirmation' ], 10, 2 );
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
	 * Send confirmation for enrollment in Moodle courses.
	 *
	 * @param array $enrolments List of enrolled course IDs or structured data.
	 * @param int   $user_id    WordPress user ID.
	 * @return void
	 */
	public function send_enrollment_confirmation( $enrolments, $user_id ) {

		$user = get_userdata( $user_id );
		if ( ! $user || empty( $user->user_email ) ) {
			return;
		}

		$email_data = [
			'group_name' => '',
			'enrolments' => [],
		];
		
		foreach ( $enrolments as $course ) {
			$course_id      = is_array( $course ) ? $course['course_id'] : $course;
			$classroom_name = is_array( $course ) && isset( $course['classroom_name'] ) ? $course['classroom_name'] : '';
		
			if ( empty( $email_data['group_name'] ) && ! empty( $classroom_name ) ) {
				$email_data['group_name'] = $classroom_name;
			}
		
			$email_data['enrolments'][] = [
				'courseid' => $course_id,
			];
		}
		

		$this->send_email( 'EnrollmentEmail', $user->user_email, $email_data );
	}
}
