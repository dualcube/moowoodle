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
	 * @param array $enrollments List of enrolled course IDs or structured data.
	 * @param int   $user_id     WordPress user ID.
	 * @return void
	 */
	public function send_enrollment_confirmation( $enrollments, $user_id ) {

		$user = get_userdata( $user_id );

		if ( ! $user || empty( $user->user_email ) ) {
			return;
		}

		$email_content = [];

		// Optional fields
		if ( ! empty( $enrollments['teacher_email'] ) ) {
			$email_content['teacher_email'] = sanitize_email( $enrollments['teacher_email'] );
		}

		if ( ! empty( $enrollments['gift_email'] ) && is_array( $enrollments['gift_email'] ) ) {
			$gift_email_address = reset( $enrollments['gift_email'] );
			if ( ! empty( $gift_email_address ) ) {
				$email_content['gift_email'] = sanitize_email( $gift_email_address );
			}
		}

		// Hook for additional enrollment data (classroom, cohort, etc.)
		$email_content = apply_filters( 'moowoodle_enrollment_email_data', $email_content, $enrollments );

		// Course data
		if ( ! empty( $enrollments['course'] ) && is_array( $enrollments['course'] ) ) {

			$enrolled_course_ids = array_map( 'intval', $enrollments['course'] );
			$enrolled_courses    = MooWoodle()->course->get_course( [ 'ids' => $enrolled_course_ids ] );

			if ( ! empty( $enrolled_courses ) && is_array( $enrolled_courses ) ) {
				$email_content['course_details'] = array_map( function( $course ) {
					return [
						'id'   => intval( $course['id'] ?? 0 ),
						'name' => $course['fullname'] ?? '',
					];
				}, $enrolled_courses );
			}
		}

		$this->send_email( 'EnrollmentEmail', $user->user_email, $email_content );
	}


}
