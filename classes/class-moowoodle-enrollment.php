<?php
global $MooWoodle;

class MooWoodle_Enrollment {
	public $wc_order;

	public function __construct() {
		add_action( 'woocommerce_order_status_completed', array( &$this, 'process_order' ), 10, 1 );		
		add_action( 'woocommerce_subscription_status_updated', array( &$this, 'update_course_access' ), 10, 3 );
		add_action( 'wp_enqueue_scripts', array( &$this, 'frontend_styles' ) );
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
		global $MooWoodle;
		
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
		
		$wc_order = $this->wc_order;		
		$user_id = $wc_order->get_user_id();
		add_user_meta( $user_id, '_moodle_user_order_id', $wc_order->get_id() );
		$billing_email = $wc_order->get_billing_email(); 
		$moodle_user_id = 0;
		
		if ( $user_id ) {
			$moodle_user_id = get_user_meta( $user_id, '_moodle_user_id', true );
			if ( $moodle_user_id ){
				$moodle_user_id = intval( $moodle_user_id );
			} else {
				$moodle_user_id = intval( $user_id );
			}
			if ( ! $moodle_user_id ) {
				delete_user_meta( $user_id, '_moodle_user_id' );
			}
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
		global $MooWoodle;
		
		$moodle_user = moowoodle_moodle_core_function_callback( 'get_users', array( 'criteria' => array( array( 'key' => $field, 'value' => $values ) ) ) );
		if ( ! empty( $moodle_user ) && array_key_exists( 'users', $moodle_user ) && ! empty( $moodle_user[ 'users' ] ) ) {
			return $moodle_user[ 'users' ][ 0 ][ 'id' ];
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
		global $MooWoodle;
		
		$user_data = $this->get_user_data();
		$moodle_user = moowoodle_moodle_core_function_callback( 'create_users', array( 'users' => array( $user_data ) ) );
		if ( ! empty( $moodle_user ) && array_key_exists( 0, $moodle_user ) ) {
			$moodle_user_id = $moodle_user[ 0 ][ 'id' ];
			// send email with credentials
			do_action( 'moowoodle_after_create_moodle_user', $user_data );
		}
		
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
		
		$wc_order = $this->wc_order;

		$user_id = $wc_order->get_user_id();
		$user = ( $user_id != 0) ? get_userdata( $user_id ) : false;
		
		$billing_email = $wc_order->get_billing_email();
		$username = $billing_email;
		if ( $user ) {
			$username = $user->user_login;
		} else {
			$user = get_user_by( 'email', $billing_email );
			if ( $user ) {
				$username = $user->data->user_login;
			}
		}
		$username = str_replace( ' ', '', $username );
		$username = strtolower( $username );
		
		$user_data = array();
		if ( $moodle_user_id ) {
			$user_data[ 'id' ] = $moodle_user_id;
		} else {
			$user_data[ 'email' ] = ( $user && $user->user_email != $billing_email ) ? $user->user_email : $billing_email;
			$user_data[ 'username' ] = $username;
			$user_data[ 'password' ] = wp_generate_password( 8, true );
			$user_data[ 'auth' ] = 'manual';
			$a=get_locale();
			$b=strtolower( $a );
			$user_data[ 'lang' ] = substr( $b, 0, 2 );
		}
		
		$user_data[ 'firstname' ] = $wc_order->get_billing_first_name();
		$user_data[ 'lastname' ] = $wc_order->get_billing_last_name();
		$user_data[ 'city' ] = $wc_order->get_billing_city();
		$user_data[ 'country' ] = $wc_order->get_billing_country();
		
		return apply_filters( 'moowoodle_moodle_users_data', $user_data, $wc_order );
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
		global $MooWoodle;		
		
		if ( empty( $moodle_user_id ) || ! is_int( $moodle_user_id ) ) {
			return;
		}		
		$enrolments = $this->get_enrollment_data( $moodle_user_id, $suspend );
		
		if ( empty( $enrolments ) ) {
			return;
		}
		moowoodle_moodle_core_function_callback( 'enrol_users', array( 'enrolments' => $enrolments ) );
		// send confirmation email
		do_action( 'moowoodle_after_enrol_moodle_user', $enrolments );
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
		global $MooWoodle;

		$wc_order = $this->wc_order;
		$enrolments = array();
		$items = $wc_order->get_items();
		$role_id = apply_filters( 'moowoodle_enrolled_user_role_id', 5 );

		if ( ! empty( $items ) ) {
			foreach ( $items as  $item ) {
				$course_id = get_post_meta( $item[ 'product_id' ], 'moodle_course_id', true );
				if ( ! empty( $course_id ) ) {
					$enrolment = array();
					$enrolment[ 'courseid' ] = $course_id;
					$enrolment[ 'userid' ] = $moodle_user_id;
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
		$this->wc_order = $subscription->order;
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
		
		$moodle_user_id = $this->get_moodle_user_id( $create_moodle_user );
		$this->enrol_moodle_user( $moodle_user_id, $suspend );
	}
	
	public function frontend_styles() {
	    global $MooWoodle;
	    $suffix = defined( 'MOOWOODLE_SCRIPT_DEBUG' ) && MOOWOODLE_SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_style( 'admin_css1',  $MooWoodle->plugin_url . 'assets/frontend/css/frontend' . $suffix . '.css', array(), $MooWoodle->version );
	}

	public function moowoodle_generate_hyperlink( $course, $activity = 0 ) {
		// needs authentication; ensure userinfo globals are populated
		global $MooWoodle, $current_user;

		$wc_order = $MooWoodle->enrollment->wc_order;
		if ( isset( $wc_order ) ) {
			$user_id = $wc_order->get_user_id();
			$order_user = get_user_by( 'id', $user_id );
			$order_user_meta = get_user_meta( $user_id );
		} else {
			$order_user = $current_user;
			$order_user_meta = get_user_meta( $current_user->data->ID );
		}

		$order_user_firstname = ( $order_user_meta != false ) ? $order_user_meta[ 'first_name' ][ 0 ] : '';

		$order_user_lastname = ( $order_user_meta != false ) ? $order_user_meta[ 'last_name' ][ 0 ] : '';

		$order_user_email = $order_user->data->user_email;
		if ( empty( $order_user_email ) ) {
			$order_user_email = $order_user_meta[ 'billing_email' ][ 0 ];
		}

		$order_user_username = $order_user->data->user_login;
		if ( empty( $order_user_username ) ) {
			$order_user_username = $order_user_meta[ 'nickname' ][ 0 ];
		}
		
	    wp_get_current_user();

	    $enc = array(
			"offset" => rand( 1234, 5678 ),						// just some junk data to mix into the encryption
			"stamp" => time(),									// unix timestamp so we can check that the link isn't expired
			"firstname" => $order_user_firstname,		// first name
			"lastname" => $order_user_lastname,			// last name
			"email" => $order_user_email,				// email
			"username" => $order_user_username,			// username
			
			"passwordhash" => '1Admin@23',
			"idnumber" => $order_user->data->ID,					// int id of user in this db (for user matching on services, etc)
			"course" => $course,								// string containing course id, optional
			
			"updatable" => true,
			"activity" => $activity						// index of first [visible] activity to go to, if auto-open is enabled in moodle
		);

		// encode array as querystring
		$details = http_build_query( $enc );

		$conn_settings = $MooWoodle->options_general_settings;

		return rtrim( $conn_settings[ 'moodle_url' ], "/" ) . MOOWOODLE_MOODLE_PLUGIN_URL . $this->encrypt_string( $details, $conn_settings[ 'moodle_access_token' ] );
	}

	/**
	 * Given a string and key, return the encrypted version (openssl is "good enough" for this type of data, and comes with modern php)
	 */
	public function encrypt_string( $value, $key ) {
		if ( $this->moowoodle_is_base64( $key ) ) {
			$encryption_key = base64_decode( $key );
		} else {
			$encryption_key = $key;
		}
		$iv = openssl_random_pseudo_bytes( openssl_cipher_iv_length( 'aes-256-cbc' ) );
		$encrypted = openssl_encrypt( $value, 'aes-256-cbc', $encryption_key, 0, $iv );
		$result = str_replace( array( '+', '/', '='), array( '-', '_', '' ), base64_encode( $encrypted . '::' . $iv ) );
		return $result;
	}

	public function moowoodle_is_base64( $string ) {
	    $decoded = base64_decode( $string, true );
	    // Check if there is no invalid character in string
	    if ( ! preg_match( '/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string ) ) return false;
	    // Decode the string in strict mode and send the response
	    if ( ! base64_decode( $string, true ) ) return false;
	    // Encode and compare it to original one
	    if ( base64_encode( $decoded ) != $string ) return false;
	    return true;
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
	
}
