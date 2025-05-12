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

		$email_data = [];

		// Optional fields
		if ( ! empty( $enrolments['teacher_email'] ) ) {
			$email_data['teacher_email'] = sanitize_email( $enrolments['teacher_email'] );
		}

		if ( ! empty( $enrolments['gift_email'][0] ) ) {
			$email_data['gift_email'] = sanitize_email( $enrolments['gift_email'][0] );
		}

		// Hook for additional enrollment data (classroom, cohort, etc.)
		$email_data = apply_filters( 'moowoodle_enrollment_email_data', $email_data, $enrolments );

		// Course data
		if ( ! empty( $enrolments['course'] ) && is_array( $enrolments['course'] ) ) {

			$course_ids = array_map( 'intval', $enrolments['course'] );
			$courses    = MooWoodle()->course->get_course( [ 'ids' => $course_ids ] );

			if ( ! empty( $courses ) && is_array( $courses ) ) {
				$email_data['course_details'] = array_map( function( $course ) {
					return [
						'id'   => intval( $course['id'] ?? 0 ),
						'name' => $course['fullname'] ?? '',
					];
				}, $courses );
			}
		}

		$this->send_email( 'EnrollmentEmail', $user->user_email, $email_data );
	}	
}
