<?php
class DC_Woodle_Enrollment {
	public $wc_order;

	public function __construct() {
		add_action( 'woocommerce_order_status_completed', array( &$this, 'process_order' ), 10, 1 );		
		add_action( 'woocommerce_subscription_status_updated', array( &$this, 'update_course_access' ), 10, 3 );
	}
	
	/**
	 * Process the oreder when order status is complete.
	 *
	 * @access public
	 * @param int $order_id
	 * @return void
	 */
	public function process_order( $order_id ) {
		global $DC_Woodle;
		
		$this->wc_order = new WC_Order( $order_id );
		$this->process_enrollment();
	}
	
	/**
	 * Perform enrollment to moodle
	 *
	 * @access private
	 * @return void
	 */
	private function process_enrollment() {
		global $DC_Woodle;
		
		$wc_order = $this->wc_order;		
		$user_id = $wc_order->get_user_id();
		
		$moodle_user_id = $this->get_moodle_user_id( true );
		$this->enrol_moodle_user( $moodle_user_id );
	}
	
	/**
	 * Get moodle user id. If the user does not exist in moodle then creats an user in moodle.
	 *
	 * @access private
	 * @param bool $create_moodle_user (default: bool)
	 * @return int
	 */
	private function get_moodle_user_id( $create_moodle_user = false ) {
		global $DC_Woodle;
		
		$wc_order = $this->wc_order;		
		$user_id = $wc_order->get_user_id();
		$email = $wc_order->billing_email;
		$moodle_user_id = 0;
		
		if( $user_id ) {
			$moodle_user_id = get_user_meta( $user_id, '_moodle_user_id', true );
			$moodle_user_id = intval( $moodle_user_id );
			$moodle_user_id = $this->search_for_moodle_user( 'id', array ( $moodle_user_id ) );
			if( ! $moodle_user_id ) {
				delete_user_meta( $user_id, '_moodle_user_id' );
			}
		}
		
		if( ! $moodle_user_id ) {
			$moodle_user_id = $this->search_for_moodle_user( 'email', array ( $email ) );
			if( $moodle_user_id && $user_id ) {
				add_user_meta( $user_id, '_moodle_user_id', $moodle_user_id );
			}
		}
		
		if( ! $moodle_user_id && $create_moodle_user ) {
			$moodle_user_id = $this->create_moodle_user();
			if( $moodle_user_id && $user_id ) {
				add_user_meta( $user_id, '_moodle_user_id', $moodle_user_id );
			}
		} else if( woodle_get_settings( 'update_user_info', 'dc_woodle_general' ) == 'yes' ) {
			$this->update_moodle_user( $moodle_user_id );
		}
		
		return $moodle_user_id;
	}
	
	/**
	 * Searches for an user in moodle by a specific field.
	 *
	 * @access private
	 * @param string $field
	 * @param string $values
	 * @return int
	 */
	private function search_for_moodle_user( $field, $values ) {
		global $DC_Woodle;
		
		$moodle_user = woodle_moodle_core_function_callback( $DC_Woodle->moodle_core_functions['get_users_by_field'], array( $field, $values ) );
		
		if( ! empty( $moodle_user ) && array_key_exists( 0, $moodle_user ) ) {
			return $moodle_user[0]['id'];
		}
		
		return 0;
	}
	
	/**
	 * Creates an user in moodle.
	 *
	 * @access private
	 * @param int $moodle_user_id (default: int)
	 * @return int
	 */
	private function create_moodle_user( $moodle_user_id = 0 ) {
		global $DC_Woodle;
		
		$user_data = $this->get_user_data();
		$moodle_user = woodle_moodle_core_function_callback( $DC_Woodle->moodle_core_functions['create_users'], array( array( $user_data ) ) );
		if( ! empty( $moodle_user ) && array_key_exists( 0, $moodle_user ) ) {
			$moodle_user_id = $moodle_user[0]['id'];
			// send email with credentials
			do_action( 'woodle_after_create_moodle_user', $user_data );
		}
		
		return $moodle_user_id;
	}
	
	/**
	 * Updates an user info in moodle.
	 *
	 * @access private
	 * @param int $moodle_user_id (default: int)
	 * @return int
	 */
	private function update_moodle_user( $moodle_user_id = 0 ) {
		global $DC_Woodle;
		
		$user_data = $this->get_user_data( $moodle_user_id );
		woodle_moodle_core_function_callback( $DC_Woodle->moodle_core_functions['update_users'], array( array( $user_data ) ) );
		
		return $moodle_user_id;
	}
	
	/**
	 * Info about an user to be created/updated in moodle.
	 *
	 * @access private
	 * @param int $moodle_user_id (default: int)
	 * @return array
	 */
	private function get_user_data( $moodle_user_id = 0 ) {
		global $DC_Woodle;
		
		$wc_order = $this->wc_order;
		
		$user_id = $wc_order->get_user_id();
		$user = ( $user_id != 0) ? get_userdata( $user_id ) : false;
		
		$username = $wc_order->billing_email;
		if( $user ) {
			$username = $user->user_login;
		} else {
			$user = get_user_by( 'email', $wc_order->billing_email );
			if( $user ) {
				$username = $user->data->user_login;
			}
		}
		
		$user_data = array();
		if( $moodle_user_id ) {
			$user_data['id'] = $moodle_user_id;
		} else {
			$user_data['email'] = ( $user && $user->user_email != $wc_order->billing_email ) ? $user->user_email : $wc_order->billing_email;
			$user_data['username'] = $username;
			$user_data['password'] = wp_generate_password( 8, true );
			$user_data['auth'] = 'manual';
			$user_data['lang'] = 'en';
		}
		
		$user_data['firstname'] = $wc_order->billing_first_name;
		$user_data['lastname'] = $wc_order->billing_last_name;
		$user_data['city'] = $wc_order->billing_city;
		$user_data['country'] = $wc_order->billing_country;
		
		return apply_filters( 'woodle_moodle_users_data', $user_data );
	}
	
	/**
	 * Enrollment/suspend enrollment of an user in moodle.
	 *
	 * @access private
	 * @param int $moodle_user_id (default: int)
	 * @param int $suspend (default: int)
	 * @return void
	 */
	private function enrol_moodle_user( $moodle_user_id, $suspend = 0 ) {
		global $DC_Woodle;		
		
		if( empty( $moodle_user_id ) || ! is_int( $moodle_user_id ) ) {
			return;
		}
		
		$enrolments = $this->get_enrollment_data( $moodle_user_id, $suspend );
		
		if( empty( $enrolments ) ) {
			return;
		}
		woodle_moodle_core_function_callback( $DC_Woodle->moodle_core_functions['enrol_users'], array( $enrolments ) );
		// send confirmation email
		do_action( 'woodle_after_enrol_moodle_user', $enrolments );
	}
		
	/**
	 * Data required for enrollment.
	 *
	 * @access private
	 * @param int $moodle_user_id (default: int)
	 * @param int $suspend (default: int)
	 * @return array
	 */
	private function get_enrollment_data( $moodle_user_id, $suspend = 0 ) {
		global $DC_Woodle;
		
		$wc_order = $this->wc_order;
		$enrolments = array();
		$items = $wc_order->get_items();
		$role_id = woodle_get_settings( 'moodle_role_id', 'dc_woodle_general' );
		$role_id = ( empty( $role_id ) || ! intval( $role_id ) ) ? 5 : intval( $role_id );
		if( ! empty( $items ) ) {
			foreach( $items as  $item ) {
				$course_id = get_post_meta( $item['product_id'], '_course_id', true );
				if( ! empty( $course_id ) ) {
					$enrolment = array();
					$enrolment['courseid'] = $course_id;
					$enrolment['userid'] = $moodle_user_id;
					$enrolment['roleid'] =  $role_id;
					$enrolment['suspend'] = $suspend;
					
					$enrolments[] = $enrolment;
				}
			}
		}
		
		return apply_filters( 'woodle_moodle_enrolments_data', $enrolments );
	}
	
	/**
	 * Update user access to a course in moodle.
	 *
	 * @access public
	 * @param object $subscription
	 * @param string $new_status
	 * @param string $old_status
	 * @return void
	 */
	public function update_course_access( $subscription, $new_status, $old_status ) {
		$this->wc_order = $subscription->order;
		$suspend_for_status = apply_filters( 'woodle_suspend_course_access_for_subscription', array( 'on-hold', 'cancelled', 'expired' ) );
		
		if( $old_status == 'active' && in_array( $new_status , $suspend_for_status ) ) {
			$create_moodle_user = false;
			$suspend = 1;
		} else if( $new_status == 'active' ) {
			$create_moodle_user = true;
			$suspend = 0;
		}
		
		$moodle_user_id = $this->get_moodle_user_id( $create_moodle_user );
		$this->enrol_moodle_user( $moodle_user_id, $suspend );
	}
}
