<?php

class MooWoodle_Enrollment {
	public $wc_order;

	public function __construct() {
		add_action( 'woocommerce_order_status_completed', array( &$this, 'process_order' ), 10, 1 );		
		add_action( 'woocommerce_subscription_status_updated', array( &$this, 'update_course_access' ), 10, 3 );
		add_action( 'woocommerce_thankyou', array( &$this, 'enrollment_modified_details' ) );
		add_action( 'woocommerce_after_shop_loop_item_title', array( &$this, 'add_dates_with_product' ) );
	}
	
	/**
	 * Process the oreder when order status is complete.
	 *
	 * @access public
	 * @param int $order_id
	 * @return void
	 */
	public function process_order( $order_id ) {		
		if ( get_post_meta( $order_id, 'moodle_user_enrolled', true ) != "true" ) {
			$this->wc_order = new WC_Order( $order_id );
			$this->process_enrollment();			
		}		
	}
	
	/**
	 * Perform enrollment to moodle
	 *
	 * @access private
	 * @return void
	 */
	private function process_enrollment() {		
		$moowoodle_moodle_user_id = $this->get_moodle_user_id( true );
		$this->enrol_moodle_user( intval( $moowoodle_moodle_user_id ) );		
	}
	
	/**
	 * Get moodle user id. If the user does not exist in moodle then creats an user in moodle.
	 *
	 * @access private
	 * @param bool $create_moodle_user (default: bool)
	 * @return int
	 */
	private function get_moodle_user_id( $create_moodle_user = false ) {
		global $MooWoodle;	
		$wc_order = $this->wc_order;
		$user = $wc_order->get_user();
		$user_id = $user->ID;
		$moowoodle_moodle_user_id = 0;
		$conn_settings = $MooWoodle->options_general_settings;
		if ( $user_id ) {
			$moodle_user_id_meta = get_user_meta ( $user_id, 'moowoodle_moodle_user_id', true );
			if ( empty( $moodle_user_id_meta ) ) { //Metadata not found
				$moowoodle_moodle_user_id = $this->search_for_moodle_user( 'email', $user->user_email );				
				if ( $moowoodle_moodle_user_id > 0 ) { //User exist in Moodle
					if ( isset( $conn_settings[ 'update_moodle_user' ] ) && $conn_settings[ 'update_moodle_user' ] == "Enable" ) { //Allow Moodle user to be updated
						$moowoodle_moodle_user_id = $this->update_moodle_user( $moowoodle_moodle_user_id );
					}					
				} else { //User does not exist in Moodle
					$moowoodle_moodle_user_id = $this->create_moodle_user();				
				}
				update_user_meta( $user_id, 'moowoodle_moodle_user_id', $moowoodle_moodle_user_id );
			} else { //Metadata found				
				$moowoodle_moodle_user_id = $moodle_user_id_meta;				
			}
		}
		return $moowoodle_moodle_user_id;
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
		$users = moowoodle_moodle_core_function_callback( 'get_moodle_users', array( 'criteria' => array( array( 'key' => $field, 'value' => $values ) ) ) );
		if( ! empty( $users ) && ! empty( $users['users'] ) ) {
			return $users['users'][0]['id'];
		}		
		return 0;
	}

	/**
	 * Creates an user in moodle.
	 *
	 * @access private
	 * @param int $moowoodle_moodle_user_id (default: int)
	 * @return int
	 */
	private function create_moodle_user( $moowoodle_moodle_user_id = 0 ) {		
		$user_data = $this->get_user_data();
		$moodle_user = moowoodle_moodle_core_function_callback( 'create_users', array( 'users' => array( $user_data ) ) );		
		if( ! empty( $moodle_user ) && array_key_exists( 0, $moodle_user ) ) {
			$moowoodle_moodle_user_id = $moodle_user[0]['id'];
			// send email with credentials
			do_action( 'moowoodle_after_create_moodle_user', $user_data );
		}		
		return $moowoodle_moodle_user_id;
	}

	/**
	 * Info about an user to be created/updated in moodle.
	 *
	 * @access private
	 * @param int $moowoodle_moodle_user_id (default: int)
	 * @return array
	 */
	private function get_user_data( $moowoodle_moodle_user_id = 0 ) {		
		$wc_order = $this->wc_order;
		$user_id = $wc_order->get_user_id();
		$user = ( $user_id != 0) ? get_userdata( $user_id ) : false;		
		$billing_email = $wc_order->get_billing_email();
		$username = $billing_email;
		if( $user ) {
			$username = $user->user_login;
		} else {
			$user = get_user_by( 'email', $billing_email );
			if( $user ) {
				$username = $user->data->user_login;
			}
		}
		$username = str_replace( ' ', '', $username );
		$username = strtolower( $username );
		$moodle_pwd_meta = get_user_meta( $user_id, 'moowoodle_moodle_user_pwd', true );
		$pwd = '';
		if ( empty( $moodle_pwd_meta ) )  {
			$pwd = $this->password_generator();
			add_user_meta( $user_id, 'moowoodle_moodle_user_pwd', $pwd );
		} else {
			$pwd = $moodle_pwd_meta;
		}	
		$user_data = array();
		if( $moowoodle_moodle_user_id ) {
			$user_data['id'] = $moowoodle_moodle_user_id;
		} else {
			$user_data['email'] = ( $user && $user->user_email != $billing_email ) ? $user->user_email : $billing_email;
			$user_data['username'] = $username;
			$user_data['password'] = $pwd;
			$user_data['auth'] = 'manual';
			$a=get_locale();
			$b=strtolower($a);
			$user_data['lang'] = substr($b,0,2);
		}		
		$user_data['firstname'] = $wc_order->get_billing_first_name();
		$user_data['lastname'] = $wc_order->get_billing_last_name();
		$user_data['city'] = $wc_order->get_billing_city();
		$user_data['country'] = $wc_order->get_billing_country();
		$user_data['preferences'][0]['type'] = "auth_forcepasswordchange";
		$user_data['preferences'][0]['value'] = 1;

		return apply_filters( 'moowoodle_moodle_users_data', $user_data, $wc_order );
	}

	/**
	 * Updates an user info in moodle.
	 *
	 * @access private
	 * @param int $moowoodle_moodle_user_id (default: int)
	 * @return int
	 */
	private function update_moodle_user( $moowoodle_moodle_user_id = 0 ) {
		
		$user_data = $this->get_user_data( $moowoodle_moodle_user_id );

		moowoodle_moodle_core_function_callback( 'update_users', array( 'users' => array( $user_data ) ) );
		return $moowoodle_moodle_user_id;
	}
	
	/**
	 * Enrollment/suspend enrollment of an user in moodle.
	 *
	 * @access private
	 * @param int $moowoodle_moodle_user_id (default: int)
	 * @param int $suspend (default: int)
	 * @return void
	 */
	private function enrol_moodle_user( $moowoodle_moodle_user_id, $suspend = 0 ) {
		$wc_order = $this->wc_order;
		if ( empty( $moowoodle_moodle_user_id ) || ! is_int( $moowoodle_moodle_user_id ) ) {
			return;
		}		
		$enrolments = $this->get_enrollment_data( $moowoodle_moodle_user_id, $suspend );
		if ( empty( $enrolments ) ) {
			return;
		}
		$enrolment_data = $enrolments;
		foreach ( $enrolments as $key => $value ) {
			unset( $enrolments[ $key ][ 'linked_course_id' ] );
			unset( $enrolments[ $key ][ 'course_name' ] );
		}		
		moowoodle_moodle_core_function_callback( 'enrol_users', array( 'enrolments' => $enrolments ) );
		add_post_meta( $wc_order->id, 'moodle_user_enrolled', "true" );
		add_post_meta( $wc_order->id, 'moodle_user_enrolment_date', time() );
		// send confirmation email
		do_action( 'moowoodle_after_enrol_moodle_user', $enrolment_data );
	}
		
	/**
	 * Data required for enrollment.
	 *
	 * @access private
	 * @param int $moowoodle_moodle_user_id (default: int)
	 * @param int $suspend (default: int)
	 * @return array
	 */
	private function get_enrollment_data( $moowoodle_moodle_user_id, $suspend = 0 ) {
		$wc_order = $this->wc_order;
		$enrolments = array();
		$items = $wc_order->get_items();
		$role_id = apply_filters( 'moowoodle_enrolled_user_role_id', 5 );
		if ( ! empty( $items ) ) {
			foreach ( $items as  $item ) {
				$course_id = get_post_meta( $item[ 'product_id' ], 'moodle_course_id', true );
				if ( ! empty( $course_id ) ) {
					$enrolment = array();
					$enrolment[ 'courseid' ] = intval( $course_id );
					$enrolment[ 'userid' ] = $moowoodle_moodle_user_id;
					$enrolment[ 'roleid' ] =  $role_id;
					$enrolment[ 'suspend' ] = $suspend;
					$enrolment[ 'linked_course_id' ] =  get_post_meta( $item[ 'product_id' ], 'linked_course_id', true );
					$enrolment[ 'course_name' ] = get_the_title( $item[ 'product_id' ] );					
					$enrolments[] = $enrolment;
				}
			}
		}		
		return apply_filters( 'moowoodle_moodle_enrolments_data', $enrolments );
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
		$suspend_for_status = apply_filters( 'moowoodle_suspend_course_access_for_subscription', array( 'on-hold', 'cancelled', 'expired' ) );
		$create_moodle_user = false;
		$suspend = 0;		
		if ( $old_status == 'active' && in_array( $new_status , $suspend_for_status ) ) {
			$create_moodle_user = false;
			$suspend = 1;
		} else if ( $new_status == 'active' ) {
			$create_moodle_user = true;
			$suspend = 0;
		}		
		$moowoodle_moodle_user_id = $this->get_moodle_user_id( $create_moodle_user );
		$this->enrol_moodle_user( $moowoodle_moodle_user_id, $suspend );
	}
	
	public function enrollment_modified_details( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( $order->get_status() == 'completed' ) {
			echo esc_html_e( 'Please check your mail or go to My Courses page to access your courses.', 'moowoodle' );
		} else {
			echo esc_html_e( 'Order status is :- ', 'moowoodle' ) . $order->get_status() . '<br>';
		}
	}

	public function add_dates_with_product() {
		global $product, $MooWoodle;
		$startdate = get_post_meta( $product->get_id(), '_course_startdate', true );
		$enddate = get_post_meta( $product->get_id(), '_course_enddate', true );
		$display_settings = $MooWoodle->options_display_settings;
		if ( isset( $display_settings[ 'start_end_date' ] ) && $display_settings[ 'start_end_date' ] == "Enable" ) {
			if ( $startdate ) {
				echo esc_html_e( "Start Date : ", 'moowoodle' ) . esc_html_e( date( 'Y-m-d', $startdate ), 'moowoodle' );
			}
			print_r( "<br>" );
			if ( $enddate ) {
				echo esc_html_e( "End Date : ", 'moowoodle' ) . esc_html_e( date( 'Y-m-d', $enddate ), 'moowoodle' );
			}
		}
	}

	function password_generator() {
		$length = 8;
		$sets = array();
	    $sets[] = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
	    $sets[] = 'abcdefghjkmnpqrstuvwxyz';
	    $sets[] = '23456789';
	    $sets[]  = '~!@#$%^&*(){}[],./?';
	    $password = '';    
	    //append a character from each set - gets first 4 characters
	    foreach ($sets as $set) {
	        $password .= $set[array_rand(str_split($set))];
	    }
	    //use all characters to fill up to $length
	    while(strlen($password) < $length) {
	        //get a random set
	        $randomSet = $sets[array_rand($sets)];	        
	        //add a random char from the random set
	        $password .= $randomSet[array_rand(str_split($randomSet))]; 
	    }
	    //shuffle the password string before returning!
    	return str_shuffle($password);
	}	
}
