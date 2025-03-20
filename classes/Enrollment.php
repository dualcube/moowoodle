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

	public function fetch_course_quantity( $order ) {
		$count = 0;

		foreach( $order->get_items() as $item_id => $item ) {
			$product = $item->get_product();
			if ( $product->get_meta( 'moodle_course_id', true ) ) {
				$count+= $item->get_quantity();
			}
			if( $count > 1 ) return $count;
		}

		return $count;
	}

	/**
	 * Process the order when order status is complete.
	 * @param int $order_id
	 * @return void
	 */
	public function process_order( $order_id ) {
		$order      = new \WC_Order( $order_id );
		$this->order = $order;
		$user_id    = $order->get_customer_id();
		$user_email = $order->get_billing_email();
		$user_name  = trim($order->get_billing_first_name() . " " . $order->get_billing_last_name());
		$quantity   = $this->fetch_course_quantity( $order ); // Using your qty function
		$gifted     = $order->get_meta( "_wc_billing/MooWoodle/gift_someone", true );
	
		if ( $gifted ) {
			$user_name  = trim($order->get_meta( "_wc_billing/MooWoodle/full_name", true ));
			$user_email = $order->get_meta( "_wc_billing/MooWoodle/email_address", true );
			$user_id    = $this->get_or_create_wp_user( $user_name, $user_email );
		}
	
		if ( empty( $user_id ) ) {
			error_log("MooWoodle: Failed to process order #$order_id - User ID is missing.");
			return;
		}
	
		if ( $quantity == 1 ) {
			$items = $order->get_items();
			$item  = reset($items);

			$product = $item ? $item->get_product() : null;
	
			if ( !$product ) {
				error_log("MooWoodle: Failed to process order #$order_id - No valid product found.");
				return;
			}
	
			$this->process_enrollment( [
				"purchaser_id"     => $user_id,
				"user_email"       => $user_email,
				"course_id"        => $product->get_meta( "linked_course_id", true ),
				"moodle_course_id" => $product->get_meta( "moodle_course_id", true ),
				"order_id"         => $order_id,
				"item_id"          => key( $order->get_items() ),
				"group_item_id"    => 0,
				"suspend"          => 0
			] );
		} else {
			do_action( 'moowoodle_after_classroom_purchase', $user_id, $user_name, $order );
		}
	}

	public function get_or_create_wp_user( $user_name, $email ) {

		// Check if the user already exists
		$user = get_user_by('email', $email);
	
		if ( ! $user ) {
			// Generate a password
			$password = wp_generate_password(12, false);
			
			// Convert username to lowercase, remove spaces, and sanitize
			$sanitized_username = sanitize_user( strtolower( str_replace(' ', '_', $user_name) ) );
			$unique_username    = $sanitized_username . '_' . time();
	
			// Create the user
			$user_id = wp_create_user( $unique_username, $password, $email );
	
			if ( ! is_wp_error( $user_id ) ) {
				// Assign 'customer' role
				wp_update_user(['ID' => $user_id, 'role' => 'customer']);
			}
		} else {
			$user_id = $user->ID;
		}
	
		return $user_id;
	}
	
	/**
	 * Process the enrollment when the order status is complete and when adding any user to any group
	 *
	 * @param array $enroll_data 
	 * @return void
	 */
	public function process_enrollment( $enroll_data ) {
		if ( empty( $enroll_data ) || ! is_array( $enroll_data ) ) {
			error_log("MooWoodle: Invalid or missing enrollment data.");
			return;
		}
	
		$purchaser_id = $enroll_data['purchaser_id'];
		$course_id    = $enroll_data['course_id'];
	
		// Retrieve Moodle User ID
		$moodle_user_id = $this->get_moodle_user_id( $purchaser_id );
		if ( ! $moodle_user_id ) {
			error_log("MooWoodle: No Moodle user ID found for User #{$purchaser_id}.");
			return;
		}
	
		// Prepare enrollment data
		$enroll_data['moodle_user_id'] = $moodle_user_id;
		$enroll_data['role_id']        = apply_filters( 'moowoodle_enrolled_user_role_id', 5 );
	
		// Check if the user is already enrolled
		$previous_enrolled_courses = self::get_previous_enrollments( $purchaser_id );
		if ( in_array( $course_id, $previous_enrolled_courses ) ) {
			error_log("MooWoodle: User #{$purchaser_id} is already enrolled in Course #{$course_id}.");
			return;
		}
	
		// Moodle enrollment request
		$enrolments = [[
			'roleid'   => $enroll_data['role_id'],
			'suspend'  => $enroll_data['suspend'],
			'courseid' => (int) $enroll_data['moodle_course_id'],
			'userid'   => $moodle_user_id,
		]];
	
		$response = MooWoodle()->external_service->do_request( 'enrol_users', [ 'enrolments' => $enrolments ] );
	
		//file_put_contents( WP_CONTENT_DIR . '/mo_file_log.txt', 'response:enroll' . var_export( $response, true ) . "\n", FILE_APPEND );
	
		if ( ! $response || isset( $response['error'] ) ) {
			error_log("MooWoodle: Enrollment failed for User #{$purchaser_id} in Course #{$course_id}. Error: " . json_encode($response));
			return;
		}
	
		// Store enrollment record
		self::add_enrollment([
			'user_id'       => $purchaser_id,
			'user_email'    => $enroll_data['user_email'],
			'course_id'     => $course_id,
			'order_id'      => $enroll_data['order_id'],
			'item_id'       => $enroll_data['item_id'],
			'status'        => 'enrolled',
			'group_item_id' => $enroll_data['group_item_id'],
		]);
	
		// Update user's enrolled courses
		update_user_meta( $purchaser_id, 'moowoodle_moodle_course_enroll', array_merge( $previous_enrolled_courses, [ $course_id ] ) );
	
		// Trigger custom action after enrollment
		do_action( 'moowoodle_after_enrol_moodle_user', $enrolments, $purchaser_id );
	}
	


	public function get_moodle_user_id( $purchaser_id ) {
		if ( ! $purchaser_id ) {
			return $purchaser_id; // Return if guest user
		}
	
		// Check if Moodle user ID is already stored
		$moodle_user_id = get_user_meta( $purchaser_id, 'moowoodle_moodle_user_id', true );
	
		// Allow filtering before Moodle user creation/update
		$moodle_user_id = apply_filters( 'moowoodle_get_moodle_user_id_before_enrollment', $moodle_user_id, $purchaser_id );
	
		if ( $moodle_user_id ) {
			return $moodle_user_id; // Return if already exists
		}
	
		$purchaser_details = get_userdata( $purchaser_id );
	
		if ( ! $purchaser_details ) {
			return 0; // Return 0 if user data is not found
		}
	
		// Search for Moodle user by email
		$moodle_user_id = $this->search_for_moodle_user( 'email', $purchaser_details->user_email );
	
		if ( ! $moodle_user_id ) {
			// Create Moodle user if not found
			$moodle_user_id = $this->create_user( $purchaser_details );
		} else {
			// Check if Moodle user should be updated
			$should_update = MooWoodle()->setting->get_setting( 'update_moodle_user', [] );
			if ( is_array( $should_update ) && in_array( 'update_moodle_user', $should_update, true ) ) {
				$this->update_moodle_user( $moodle_user_id, $purchaser_id );
			}
		}
	
		if ( $moodle_user_id ) {
			update_user_meta( $purchaser_id, 'moowoodle_moodle_user_id', $moodle_user_id );
		}
	
		return $moodle_user_id;
	}
	
	private function search_for_moodle_user( $key, $value ) {
		$response = MooWoodle()->external_service->do_request(
			'get_moodle_users',
			[ 'criteria' => [ [ 'key' => $key, 'value' => $value ] ] ]
		);
	
		return ! empty( $response['data']['users'] ) ? reset( $response['data']['users'] )['id'] : 0;
	}
	
	public function create_user( $purchaser_details ) {
		if ( ! $purchaser_details ) {
			return 0;
		}
	
		try {
			$user_id = $purchaser_details->ID;
			$password = get_user_meta( $user_id, 'moowoodle_moodle_user_pwd', true );
	
			if ( ! $password ) {
				$password = $this->generate_password();
				add_user_meta( $user_id, 'moowoodle_moodle_user_pwd', $password );
			}
	
			$user_data = [
				'email'     => $purchaser_details->user_email,
				'username'  => $purchaser_details->user_login,
				'password'  => $password,
				'auth'      => 'manual',
				'firstname' => $purchaser_details->display_name,
				'lastname'  => ! empty( $purchaser_details->last_name ) ? $purchaser_details->last_name : 'User',
				'preferences' => [
					[ 'type' => 'auth_forcepasswordchange', 'value' => 1 ]
				]
			];
	
			$response = MooWoodle()->external_service->do_request( 'create_users', [ 'users' => [ $user_data ] ] );
	
			if ( empty( $response['data'] ) ) {
				throw new \Exception( "Invalid response from Moodle while creating user." );
			}
	
			$moodle_users = reset( $response['data'] );
	
			if ( isset( $moodle_users['id'] ) ) {
				update_user_meta( $user_id, 'moowoodle_moodle_new_user_created', 'created' );
				return $moodle_users['id'];
			}
	
			throw new \Exception( "Unable to create user in Moodle." );
		} catch ( \Exception $e ) {
			Util::log( "Moodle user creation error: " . $e->getMessage() );
		}
	
		return 0;
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

	private function update_moodle_user( $moodle_user_id, $purchaser_id  ) {

		$purchaser_data  = $this->get_user_data(  $purchaser_id , $moodle_user_id );

		// update user data on moodle.
		MooWoodle()->external_service->do_request(
			'update_users',
			[
				'users' => [ $purchaser_data ]
			]
		);

		return $moodle_user_id;
	}

    /**
	 * Info about an user to be created/updated in moodle.
	 * @param int $moodle_user_id
	 * @return array
	 */
	private function get_user_data( $purchaser_id, $moodle_user_id = 0 ) {
		// Prepare user data.
		$purchaser_details 	  = ( $purchaser_id  ) ? get_userdata( $purchaser_id ) : false;
		$username = ( $purchaser_details ) ? $purchaser_details ->user_login : '';
		$username = str_replace( ' ', '', strtolower( $username ) );
		$password = get_user_meta( $purchaser_id, 'moowoodle_moodle_user_pwd', true );

		// If password not exist create a password.
		if ( ! $password ) {
			$password = $this->generate_password();
			add_user_meta( $purchaser_id, 'moowoodle_moodle_user_pwd', $password );
		}

		$user_data = [];

		// Moodle user 
		if ( $moodle_user_id ) {
			$user_data[ 'id' ] = $moodle_user_id;
		} else {
			$user_data[ 'email' ] 	 = ( $purchaser_details ) ? $purchaser_details->user_email:'';
			$user_data[ 'username' ] = $username;
			$user_data[ 'password' ] = $password;
			$user_data[ 'auth' ] 	 = 'manual';
		}
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
	 * Get previous enrollments for a user
	 */
	public static function get_previous_enrollments( $purchaser_id ) {
		$courses = get_user_meta($purchaser_id, 'moowoodle_moodle_course_enroll', true);
		return is_array($courses) ? $courses : [];
	}
	
	
	/**
	 * Standardized response handler
	 */
	private static function send_response( $success, $message ) {
		return [
			'success' => $success,
			'message' => $message
		];
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
	 * Migrate enrollment data from order to our custom table
	 * @return void
	 */
	public static function migrate_enrollments() {
		// Get all enrollment data
		$order_ids = wc_get_orders( [
			'status' 	  => 'completed',
			'meta_query'  => [
				[
					'key'     => 'moodle_user_enrolled',
					'value'   => 1,
					'compare' => '=',
				],
			],
			'return' => 'ids',
		]);

		// Migrate all orders
        foreach ( $order_ids as $order_id ) {
			$order = wc_get_order( $order_id );
			self::migrate_enrollment( $order );
		}
	}

	/**
	 * Migrate all enrollment from a order
	 * @param mixed $order
	 * @return void
	 */
	public static function migrate_enrollment( $order ) {
		// Get all unenrolled courses of the order
		$unenrolled_courses = $order->get_meta( '_course_unenroled', true );
		$unenrolled_courses = $unenrolled_courses ? explode( ',', $unenrolled_courses ) : [];

		foreach ( $order->get_items() as $enrolment ) {

			$customer = $order->get_user();

			if ( ! $customer ) continue;

			$product = $enrolment->get_product();

			if ( ! $product ) continue;

			// Get linked course id
			$linked_course_id = $product->get_meta( 'linked_course_id', true );
			
			// Get enrollment date
			$enrollment_date   = $order->get_meta( 'moodle_user_enrolment_date', true );
			if ( is_numeric( $enrollment_date) ) {
				$enrollment_date = date( "Y-m-d H:i:s", $enrollment_date );
			}
			
			// Get the enrollment status
			$enrollment_status = in_array( $linked_course_id, $unenrolled_courses ) ? 'unenrolled' : 'enrolled';

			self::add_enrollment([
				'user_id' 	 => $customer->ID,
				'user_email' => $customer->user_email,
				'course_id'  => $linked_course_id,
				'order_id'   => $order->get_id(),
				'item_id'    => $enrolment->get_id(),
				'status'     => $enrollment_status,
				'date'	     => $enrollment_date,
			]);
		}
	}

	/**
	 * Add new enrollment
	 * @param mixed $args
	 * @return bool|int|null
	 */
	public static function add_enrollment( $args ) {
		file_put_contents( WP_CONTENT_DIR . '/mo_file_log.txt', 'responsefdg' . var_export( $args, true ) . "\n", FILE_APPEND );

		global $wpdb;

		try {
			// insert data 
			return $wpdb->insert( "{$wpdb->prefix}moowoodle_enrollment", $args );
		} catch ( \Exception $error ) {
			return null;
		}
	}

	/**
	 * Get a particular enrollment
	 * @param mixed $id
	 * @return array|object|null
	 */
	public static function get_enrollment( $id ) {
		global $wpdb;

		try {
			// get data 
			return $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}moowoodle_enrollment WHERE id = '$id'", ARRAY_A );
		} catch ( \Exception $error ) {
			return null;
		}
	}

	/**
	 * Get a particular enrollment
	 * @param mixed $key
	 * @param mixed $value
	 * @return array|object|null
	 */
	public static function get_enrollment_by_field( $key, $value ) {
		global $wpdb;

		try {
			// get data 
			return $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}moowoodle_enrollment WHERE $key = '$value'", ARRAY_A );
		} catch ( \Exception $error ) {
			return null;
		}
	}

	/**
	 * Get enrollment by filter
	 * @param mixed $filter
	 * @return array|object|null
	 */
	public static function get_enrollments( $filter = [] ) {
		global $wpdb;

        // Handle limit and offset seperatly
        $page       = $filter['page'] ?? 0;
        $perpage    = $filter['perpage'] ?? 0;

		// Remove page and perpage after retrive the data
        unset( $filter['page'] );
        unset( $filter['perpage'] );

        // Preaper predicate
        $predicate = [];
        foreach( $filter as $column => $value ) {

            // BETWEEN or IN condition
            if ( is_array( $value ) ) {

                // Check for BETWEEN 
                if ( $value['compare'] === "BETWEEN" ) {
                    $start_value    = $value['value'][0];
                    $end_value      = $value['value'][1]; 
                    $predicate[]    = "{$column} BETWEEN '$start_value' AND '$end_value'";
                }

                // Check for IN or NOT IN
                if ( $value['compare'] === "IN" || $value['compare'] === "NOT IN" ) {
                    $compare        = $value['compare'];
                    $in_touple      = " (" . implode( ', ', array_map( function($value) { return "'$value'"; }, $value['value'] ) ) . ") ";
                    $predicate[]    = "{$column} {$compare} {$in_touple}";
                }
            } else {
                $predicate[] = "{$column} = '$value'";
            }
        }

        // Preaper query
        $query = "SELECT * FROM {$wpdb->prefix}moowoodle_enrollment";

        if ( !empty( $predicate ) ) {
            $query .= " WHERE " . implode( " AND ", $predicate );
        }

        // Pagination support
        if ( $page && $perpage && $perpage != -1 ) {
            $limit  = $perpage;
            $offset = ( $page - 1 ) * $perpage;
            $query .= " LIMIT {$limit} OFFSET {$offset}";
        }
        
		try {
			// Database query for enrollment
			$enrollments = $wpdb->get_results( $query, ARRAY_A );
			return $enrollments;
		} catch ( \Exception $error ) {
			return null;
		}
	}

	/**
	 * Update a particular enrollment
	 * @param mixed $id
	 * @param mixed $args
	 * @return bool|int
	 */
	public static function update_enrollment(  $id, $args ) {
		global $wpdb;

        // Check missing arguments
        if ( ! $id || ! $args ) {
            return false;
        }

        // Update the row
        $update = $wpdb->update(
            $wpdb->prefix . "moowoodle_enrollment",
            $args,
            [ 'id' => $id ],
        );

        return $update;
	}

	/**
	 * Delete a particular enrollment
	 * @param mixed $id
	 * @return bool|int
	 */
	public static function delete_enrollment( $id ) {
		global $wpdb;

        // Check missing arguments
        if ( ! $id ) {
            return false;
        }

        // Delete the row
        $delete = $wpdb->delete(
            $wpdb->prefix . "moowoodle_enrollment",
            [ 'id' => $id ],
        );

        return $delete;
	}
}