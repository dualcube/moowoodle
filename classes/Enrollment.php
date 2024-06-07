<?php

namespace MooWoodle;

class Enrollment {
	/**
	 * Variable store woocommerce order object
	 * @var \WC_Order | null
	 */
	public $order = null;

	public function __construct() {
		add_action( 'woocommerce_order_status_completed', [ &$this, 'process_order' ], 10, 1 );
		add_action( 'woocommerce_thankyou', [ &$this, 'enrollment_modified_details' ] );
		add_action( 'woocommerce_after_shop_loop_item_title', [ &$this, 'add_dates_with_product' ] );
		add_action( 'woocommerce_product_meta_start', [ &$this, 'add_dates_with_product' ] );
	}

	/**
	 * Process the oreder when order status is complete.
	 * @param int $order_id
	 * @return void
	 */
	public function process_order( $order_id ) {	
		$order = new \WC_Order( $order_id );

		// Check order contain courses
		$has_course = false;

		foreach( $order->get_items() as $item_id => $item ) {
			$product = $item->get_product();

			if ( $product->get_meta( 'moodle_course_id', true ) ) {
				$has_course = true;
				break;
			}
		}

		if ( ! $has_course ) {
			\MooWoodle\Util::log( "Unable to enroll on order compleate. Order item is't linked with any course." );
		}

		// Enroll moodle user
		if ( $has_course && ! $order->get_meta( 'moodle_user_enrolled', true ) ) {
			$this->order	= $order;
			$moodle_user_id = $this->get_moodle_user_id();

			$this->enrol_moodle_user( $moodle_user_id );
		}
	}

	/**
	 * Get moodle user id. If the user does not exist in moodle then creats an user in moodle.
	 * @return int newly create user id.
	 */
	public function get_moodle_user_id() {
		$user_id = $this->order->get_user_id();

		// if user is a guest user.
		if ( ! $user_id ) return $user_id;
		
		$moodle_user_id = get_user_meta( $user_id, 'moowoodle_moodle_user_id', true );
		
		/**
		 * Filter before moodle user create or update.
		 * @var int $moodle_user_id
		 * @var int user_id
		 */
		$moodle_user_id = apply_filters( 'moowoodle_get_moodle_user_id_before_enrollment', $moodle_user_id, $user_id );
		
		// If moodle user id exist then return it.
		if ( $moodle_user_id ) return $moodle_user_id;

		$user 	  = ( $user_id ) ? get_userdata( $user_id ) : false;
		$email 	  = $this->order->get_billing_email();

		// Get user id from moodle database.
		$moodle_user_id = $this->search_for_moodle_user( 'email', ( $user ) ? $user->user_email : $email );
		
		if ( ! $moodle_user_id ) {
			$moodle_user_id = $this->create_moodle_user();
		} else {
			// User id is availeble update user id.

			$should_user_update = MooWoodle()->setting->get_setting( 'update_moodle_user', [] );
			$should_user_update = is_array( $should_user_update ) ? $should_user_update : [];
			$should_user_update = in_array(
				'update_moodle_user',
				$should_user_update
			);

			if ( $should_user_update ) {
				$this->update_moodle_user( $moodle_user_id );
			}
		}

		update_user_meta( $user_id, 'moowoodle_moodle_user_id', $moodle_user_id );

		return $moodle_user_id;
	}

	/**
	 * Searches for an user in moodle by a specific field.
	 * @param string $field
	 * @param string $values
	 * @return int
	 */
	private function search_for_moodle_user( $key, $value ) {
		// find user on moodle with moodle externel function.
		$response = MooWoodle()->external_service->do_request(
			'get_moodle_users',
			[ 
				'criteria' => [
					[
						'key' 	=> $key,
						'value' => $value
					]
				]
			]
		);

		if ( ! empty( $response[ 'data' ][ 'users' ]) ) {
			$user = reset( $response[ 'data' ][ 'users' ] );
			return $user[ 'id' ];
		}

		return 0;
	}

	/**
	 * Creates an user in moodle.
	 * @return int newly created user id.
	 */
	private function create_moodle_user() {
		try {
			$user_data = $this->get_user_data();
			$user_id   = 0;

			// create user on moodle.
			$response = MooWoodle()->external_service->do_request(
				'create_users', 
				[
					'users' => [ $user_data ]
				]
			);

			// Not a valid response.
			if ( ! $response[ 'data' ] ) return 0;

			$moodle_users = $response[ 'data' ];
			$moodle_users = reset( $moodle_users );

			if ( is_array( $moodle_users ) && isset( $moodle_users[ 'id' ] ) ) {
				$user_id = $moodle_users[ 'id' ];

				/**
				 * Action hook after moodle user creation.
				 * @var array $user_data data for creating user in moodle
				 * @var int $user_id newly created user id
				 */
				do_action( 'moowoodle_after_create_moodle_user', $user_data, $user_id );

				return $user_id;
			} else {
				throw new \Exception( "Unable to create user." );
			}
		} catch ( \Exception $e ) {
			Util::log( $e->getMessage() );
		}

		return 0;
	}

	/**
	 * Updates an user info in moodle.
	 * @param int $moodle_user_id
	 * @return int moowoodle user id
	 */
	private function update_moodle_user( $moodle_user_id ) {
		$user_data = $this->get_user_data( $moodle_user_id );

		// update user data on moodle.
		MooWoodle()->external_service->do_request(
			'update_users',
			[
				'users' => [ $user_data ]
			]
		);

		return $moodle_user_id;
	}

	/**
	 * Info about an user to be created/updated in moodle.
	 * @param int $moodle_user_id
	 * @return array
	 */
	private function get_user_data( $moodle_user_id = 0 ) {
		// Prepare user data.
		$user_id  = $this->order->get_user_id();
		$email 	  = $this->order->get_billing_email();
		$user 	  = ( $user_id ) ? get_userdata( $user_id ) : false;
		$username = ( $user ) ? $user->user_login : '';
		$username = str_replace( ' ', '', strtolower( $username ) );
		$password = get_user_meta( $user_id, 'moowoodle_moodle_user_pwd', true );

		// If password not exist create a password.
		if ( ! $password ) {
			$password = $this->generate_password();
			add_user_meta( $user_id, 'moowoodle_moodle_user_pwd', $password );
		}

		$user_data = [];

		// Moodle user 
		if ( $moodle_user_id ) {
			$user_data[ 'id' ] = $moodle_user_id;
		} else {
			$user_data[ 'email' ] 	 = ( $user ) ? $user->user_email : $email;
			$user_data[ 'username' ] = $username;
			$user_data[ 'password' ] = $password;
			$user_data[ 'auth' ] 	 = 'manual';
		}

		$user_data[ 'firstname' ] = $this->order->get_billing_first_name();
		$user_data[ 'lastname' ]  = $this->order->get_billing_last_name();
		$user_data[ 'city' ] 	  = $this->order->get_billing_city();
		$user_data[ 'country' ]   = $this->order->get_billing_country();
		$user_data['preferences'] = [
			[
				'type'  => "auth_forcepasswordchange",
				'value' => 1
			]
		];

		/**
		 * Filter after prepare users data.
		 * @var array $user_data
		 * @var \WC_Order $order
		 */
		return apply_filters( 'moowoodle_moodle_users_data', $user_data, $this->order );
	}

	/**
	 * Enrollment/suspend enrollment of an user in moodle.
	 * @param int $moodle_user_id
	 * @param int $suspend default 0
	 * @return void
	 */
	public function enrol_moodle_user( $moodle_user_id, $suspend = 0 ) {
		if ( empty( $moodle_user_id ) ) {
			return;
		}

		$enrolments = $this->get_enrollment_data( $moodle_user_id, $suspend );
		
		if ( empty( $enrolments ) ) {
			return;
		}

		$enrolment_data = $enrolments;

		// remove course meta not need on enrol process.
		foreach ( $enrolments as $key => $value ) {
			unset( $enrolments[ $key ][ 'linked_course_id' ] );
			unset( $enrolments[ $key ][ 'course_name' ] );
		}

		// enroll user to moodle course by core external function.
		MooWoodle()->external_service->do_request( 'enrol_users', [ 'enrolments' => $enrolments ] );
		
		$this->order->update_meta_data( 'moodle_user_enrolled', true );
		$this->order->update_meta_data( 'moodle_user_enrolment_date', time() );
		$this->order->save();

		/**
		 * Action hook after a user enroll in moodle.
		 * @var array $enrollment_data
		 * @var int $userid
		 */
		do_action( 'moowoodle_after_enrol_moodle_user', $enrolment_data, $this->order->get_user_id() );
	}

	/**
	 * Data required for enrollment.
	 * @param int $moodle_user_id (default: int)
	 * @param int $suspend (default: int)
	 * @return array
	 */
	private function get_enrollment_data( $moodle_user_id, $suspend = 0 ) {
		$enrolments = [];

		/**
		 * Filter for enrolled user roll id. Default is 5 (student).
		 * @var mixed
		 */
		$role_id = apply_filters( 'moowoodle_enrolled_user_role_id', 5 );

		foreach ( $this->order->get_items() as $item ) {
			// Get moowoodle course id
			$course_id = get_post_meta( $item->get_product_id(), 'moodle_course_id', true );
			
			// If product is not associate with moodle course.
			if ( empty( $course_id ) ) continue;

			$enrolments[] = [
				'courseid' 		   => intval( $course_id ),
				'userid'   		   => $moodle_user_id,
				'roleid'	 	   => $role_id,
				'suspend'		   => $suspend,
				'linked_course_id' => get_post_meta( $item->get_product_id(), 'linked_course_id', true ),
				'course_name'	   => get_the_title( $item->get_product_id() ),
			];
		}

		/**
		 * Filter after prepare enrollments data.
		 * @var $enrollment
		 */
		return apply_filters( 'moowoodle_moodle_enrolments_data', $enrolments );
	}

	/**
	 * Display WC order thankyou page containt.
	 * @param int $order_id
	 * @return void
	 */
	public function enrollment_modified_details( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( $order->get_status() == 'completed' ) {
			_e( 'Please check your mail or go to My Courses page to access your courses.', 'moowoodle' );
		} else {
			_e( 'Order status is :- ', 'moowoodle' ) . $order->get_status() . '<br>';
		}
	}

	/**
	 * Display course start and end date.
	 * @return void
	 */
	public function add_dates_with_product() {
		global $product;

		$startdate 		= get_post_meta( $product->get_id(), '_course_startdate', true );
		$enddate 		= get_post_meta( $product->get_id(), '_course_enddate', true );

		// Get start end date setting
		$start_end_date = MooWoodle()->setting->get_setting('start_end_date');
		$start_end_date = is_array( $start_end_date ) ? $start_end_date : [];
		$start_end_date = in_array(
			'start_end_date',
			$start_end_date
		);
		
		if ( $start_end_date ) {
			if ( $startdate ) {
				echo esc_html_e( "Start Date : ", 'moowoodle' ) . esc_html_e( gmdate( 'Y-m-d', $startdate ), 'moowoodle' );
				print_r("<br>");
			}

			if ( $enddate ) {
				echo esc_html_e( "End Date : ", 'moowoodle' ) . esc_html_e( gmdate( 'Y-m-d', $enddate ), 'moowoodle' );
			}
		}
	}

	/**
	 * Generate random password.
	 * @param int $length default length is 12.
	 * @return string generated password.
	 */
	private function generate_password( $length = 12 ) {
		$sets 	= [];
		$sets[] = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
		$sets[] = 'abcdefghjkmnpqrstuvwxyz';
		$sets[] = '23456789';
		$sets[] = '~!@#$%^&*(){}[],./?';

		$password = '';

		// Append a character from each set - gets first 4 characters
		foreach ( $sets as $set ) {
			$password .= $set[ array_rand( str_split( $set ) ) ];
		}

		//use all characters to fill up to $length
		while ( strlen( $password ) < $length ) {
			//get a random set
			$randomSet = $sets[ array_rand( $sets ) ];

			//add a random char from the random set
			$password .= $randomSet[ array_rand( str_split( $randomSet ) ) ];
		}

		//shuffle the password string before returning!
		return str_shuffle( $password );
	}
}
