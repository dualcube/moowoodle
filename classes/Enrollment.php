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
	 * Process WooCommerce order for Moodle enrollment.
	 *
	 * Creates user if gifted, then enrolls user in courses, groups, or cohorts
	 * based on the products in the order.
	 *
	 * @param int $order_id The ID of the WooCommerce order.
	 */
	public function process_order( $order_id ) {
		$order = new \WC_Order( $order_id );
		$this->order = $order;
	
		$gifted    = $order->get_meta( '_wc_billing/MooWoodle/gift_someone', true );
		$user_email = $gifted
			? trim( $order->get_meta( '_wc_billing/MooWoodle/email_address', true ) )
			: $order->get_billing_email();
	
		$user_id = $gifted
			? $this->create_wordpress_user(
				trim( $order->get_meta( '_wc_billing/MooWoodle/first_name', true ) ),
				trim( $order->get_meta( '_wc_billing/MooWoodle/last_name', true ) ),
				$user_email
			)
			: $order->get_customer_id();
	
		if ( empty( $user_id ) ) {
			Util::log( "[MooWoodle] Order processing failed for Order #$order_id - Moodle User ID is missing." );
			return;
		}
	
		foreach ( $order->get_items() as $item ) {
			if ( $item->get_quantity() > 1
				&& MooWoodle()->util->is_khali_dabba()
				&& in_array( 'group_purchase_enable', MooWoodle()->setting->get_setting( 'group_purchase_enable', [] ), true )
			) {
				do_action( 'moowoodle_process_multiple_products_purchase', $user_id, $order );
				return;
			}
		}
	
		$enroll_data_base = [
			'first_name'   => $gifted
				? trim( $order->get_meta( '_wc_billing/MooWoodle/first_name', true ) )
				: $order->get_billing_first_name(),
			'last_name'    => $gifted
				? trim( $order->get_meta( '_wc_billing/MooWoodle/last_name', true ) )
				: $order->get_billing_last_name(),
			'purchaser_id' => $user_id,
			'user_email'   => $user_email,
			'order_id'     => $order_id,
		];
	
		$email_data = [];
	
		foreach ( $order->get_items() as $item_id => $item ) {
			$product = $item->get_product();
			if ( ! $product ) {
				Util::log( "[MooWoodle] Skipping item #$item_id - Invalid product." );
				continue;
			}
	
			$enroll_data = array_merge( $enroll_data_base, [ 'item_id' => $item_id ] );
	
			if ( $product->is_type( 'variation' ) ) {
				$enroll_data = array_merge(
					$enroll_data,
					[
						'group_id'         => $product->get_meta( 'linked_group_id', true ),
						'moodle_group_id'  => $product->get_meta( 'moodle_group_id', true ),
						'moodle_course_id' => $product->get_meta( 'moodle_course_id', true ),
					]
				);
	
				do_action( 'moowoodle_handle_group_purchase', $enroll_data );
	
				if ( apply_filters( 'moowoodle_add_group_detail_for_email', false ) ) {
					$email_data['group'][] = $enroll_data['group_id'];
				}
			} elseif ( $product->get_meta( 'moodle_cohort_id', true ) ) {
				$enroll_data = array_merge(
					$enroll_data,
					[
						'cohort_id'        => $product->get_meta( 'linked_cohort_id', true ),
						'moodle_cohort_id' => $product->get_meta( 'moodle_cohort_id', true ),
					]
				);
	
				do_action( 'moowoodle_handle_cohort_purchase', $enroll_data );
	
				if ( apply_filters( 'moowoodle_add_cohort_detail_for_email', false ) ) {
					$email_data['cohort'][] = $enroll_data['cohort_id'];
				}
			} elseif ( $product->get_meta( 'moodle_course_id', true ) ) {
				$enroll_data = array_merge(
					$enroll_data,
					[
						'course_id'        => $product->get_meta( 'linked_course_id', true ),
						'moodle_course_id' => $product->get_meta( 'moodle_course_id', true ),
					]
				);
	
				$this->process_enrollment( $enroll_data );
	
				if ( apply_filters( 'moowoodle_add_course_detail_for_email', false ) ) {
					$email_data['course'][] = $enroll_data['course_id'];
				}
			}
		}
	
		if ( $gifted ) {
			$email_data['gift_email'][] = $order->get_billing_email();
		}
	
		do_action( 'moowoodle_after_successful_enrollments', $email_data, $user_id );
	}
	
		
	/**
	 * Create a WordPress user if not exists.
	 *
	 * @param string $first_name
	 * @param string $last_name
	 * @param string $user_email
	 * @return int|false User ID or false on failure
	 */
	public function create_wordpress_user( $first_name, $last_name, $user_email ) {

		// Check if the user already exists by email
		$user = get_user_by( 'email', $user_email );
	
		if ( ! $user ) {
			// Generate a secure password
			$password = $this->generate_password();
	
			// Use the part before @ as username
			$username = sanitize_user( strtolower( strstr( $user_email, '@', true ) ) );
	
			// Create the user
			$user_id = wp_create_user( $username, $password, $user_email );
	
			if ( is_wp_error( $user_id ) ) {
				Util::log( "[MooWoodle] WP user creation failed for {$user_email}: " . $user_id->get_error_message() );
				return false;
			}
	
			// Assign role and update user meta
			wp_update_user( [
				'ID'         => $user_id,
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'role'       => 'customer',
			] );
	
			update_user_meta( $user_id, 'moowoodle_wordpress_user_pwd', $password );
			update_user_meta( $user_id, 'moowoodle_wordpress_new_user_created', 'created' );
	
		} else {
			$user_id = $user->ID;
		}
	
		return $user_id;
	}
	
	/**
	 * Process the enrollment when the order status is complete or a user is added to a group.
	 *
	 * @param array $enroll_data 
	 * @return bool
	 */
	public function process_enrollment( $enroll_data ) {
		if ( empty( $enroll_data ) ) {
			return false;
		}
	
		$group_item_id = $enroll_data['group_item_id'] ?? false;
	
		if ( $group_item_id ) {
			// Apply the filter and check result
			if ( ! apply_filters( 'moowoodle_check_item', $enroll_data['group_item_id'] ) ) {
				return false;
			}
		}
	
		$moodle_user_id = $this->get_moodle_user_id( $enroll_data );
		if ( empty( $moodle_user_id ) ) {
			Util::log( "[MooWoodle] Missing Moodle user ID for purchaser {$enroll_data['purchaser_id']}." );
			return false;
		}
	
		$enroll_data['moodle_user_id'] = $moodle_user_id;
		$enroll_data['role_id']        = apply_filters( 'moowoodle_enrolled_user_role_id', 5 );
	
		$response = MooWoodle()->external_service->do_request(
			'enrol_users',
			[
				'enrolments' => [
					[
						'roleid'   => $enroll_data['role_id'],
						'suspend'  => $enroll_data['suspend'] ?? 0,
						'courseid' => (int) $enroll_data['moodle_course_id'],
						'userid'   => $moodle_user_id,
					],
				],
			]
		);
	
		if ( ! empty( $response['error'] ) ) {
			Util::log( "[MooWoodle] Enrollment failed for user {$enroll_data['purchaser_id']} in course {$enroll_data['course_id']}." );
			return false;
		}
	
		$enrollment_data = [
			'user_id'       => $enroll_data['purchaser_id'],
			'user_email'    => $enroll_data['user_email'],
			'course_id'     => $enroll_data['course_id'],
			'order_id'      => $enroll_data['order_id'],
			'item_id'       => $enroll_data['item_id'] ?? 0,
			'status'        => 'enrolled',
			'group_item_id' => $enroll_data['group_item_id'] ?? 0,
		];
	
		$existing_enrollment = $this->get_enrollments(
			[
				'user_email'    => $enroll_data['user_email'],
				'course_id'     => $enroll_data['course_id'],
				'group_item_id' => $enroll_data['group_item_id'] ?? 0,
			]
		);
	
		$existing_enrollment = reset( $existing_enrollment );
	
		if ( $existing_enrollment ) {
			self::update_enrollment( $existing_enrollment['id'], $enrollment_data );
		} else {
			self::add_enrollment( $enrollment_data );
		}
	
		if ( $group_item_id ) {
			do_action( 'moowoodle_seat_book', $enroll_data['group_item_id'] );
		}
	
		add_filter( 'moowoodle_add_course_detail_for_email', '__return_true' );
	
		return true;
	}
	
	/**
	 * Get the Moodle user ID for a given enrollment data.
	 * Creates or updates the Moodle user if needed.
	 *
	 * @param array $enroll_data Enrollment details including purchaser ID and email.
	 * @return int Moodle user ID or 0 if not found/created.
	 */
	public function get_moodle_user_id( $enroll_data ) {

		if ( ! $enroll_data['purchaser_id'] ) {
			return 0;
		}
	
		$moodle_user_id = get_user_meta( $enroll_data['purchaser_id'], 'moowoodle_moodle_user_id', true );
		$moodle_user_id = apply_filters( 'moowoodle_get_moodle_user_id_before_enrollment', $moodle_user_id, $enroll_data['purchaser_id'] );
	
		if ( $moodle_user_id ) {
			return $moodle_user_id;
		}
	
		$moodle_user_id = $this->search_for_moodle_user( 'email', $enroll_data['user_email'] );
	
		if ( ! $moodle_user_id ) {
			$moodle_user_id = $this->create_user( $enroll_data );
	
		} else {
			$settings = MooWoodle()->setting->get_setting( 'update_moodle_user', [] );
	
			if ( in_array( 'update_moodle_user', $settings, true ) ) {
				$this->update_moodle_user( $moodle_user_id, $enroll_data['purchaser_id'] );
			}
		}
	
		update_user_meta( $enroll_data['purchaser_id'], 'moowoodle_moodle_user_id', $moodle_user_id );

		return $moodle_user_id;
	}
	
	/**
	 * Search for a Moodle user by a specific key and value.
	 *
	 * @param string $key   The field to search by (e.g., 'email', 'username').
	 * @param string $value The value to search for.
	 * @return int          Moodle user ID if found, otherwise 0.
	 */
	private function search_for_moodle_user( $key, $value ) {
		$response = MooWoodle()->external_service->do_request(
			'get_moodle_users',
			[
				'criteria' => [
					[
						'key'   => $key,
						'value' => $value,
					],
				],
			]
		);
	
		return ! empty( $response['data']['users'] ) ? reset( $response['data']['users'] )['id'] : 0;
	}
	
	/**
	 * Create a Moodle user based on enrollment data.
	 *
	 * Checks and uses stored passwords if available, generates username from email,
	 * sends the data to Moodle to create the user, and returns the Moodle user ID.
	 *
	 * @param array $enroll_data Enrollment data including purchaser ID, email, first and last names.
	 * @return int Moodle user ID on success, 0 on failure.
	 */
	public function create_user( $enroll_data ) {
		$user_id = absint( $enroll_data['purchaser_id'] ?? 0 );
		if ( ! $user_id ) {
			return 0;
		}
	
		try {
			$new_wordpress_user = get_user_meta( $user_id, 'moowoodle_wordpress_new_user_created', true );
	
			if ( $new_wordpress_user ) {
				$password = get_user_meta( $user_id, 'moowoodle_wordpress_user_pwd', true );
				add_user_meta( $user_id, 'moowoodle_moodle_user_pwd', $password );
			} else {
				$password = get_user_meta( $user_id, 'moowoodle_moodle_user_pwd', true );
	
				if ( ! $password ) {
					$password = $this->generate_password();
					add_user_meta( $user_id, 'moowoodle_moodle_user_pwd', $password );
				}
			}
	
			$email      = sanitize_email( $enroll_data['user_email'] ?? '' );
			$first_name = sanitize_text_field( $enroll_data['first_name'] ?? 'User' );
			$last_name  = sanitize_text_field( $enroll_data['last_name'] ?? 'User' );
	
			if ( ! $email ) {
				return 0;
			}
	
			// Generate username from email
			$username = sanitize_user( explode( '@', $email )[0] );
	
			$user_data = [
				'email'       => $email,
				'username'    => $username,
				'password'    => $password,
				'auth'        => 'manual',
				'firstname'   => $first_name,
				'lastname'    => $last_name,
				'preferences' => [
					[
						'type'  => 'auth_forcepasswordchange',
						'value' => 1,
					],
				],
			];
	
			$response = MooWoodle()->external_service->do_request( 'create_users', [ 'users' => [ $user_data ] ] );
	
			if ( empty( $response['data'] ) ) {
				throw new \Exception( "Invalid response from Moodle while creating user." );
			}
	
			$moodle_user = reset( $response['data'] );
	
			if ( isset( $moodle_user['id'] ) ) {
				update_user_meta( $user_id, 'moowoodle_moodle_new_user_created', 'created' );
				return $moodle_user['id'];
			}
	
			throw new \Exception( "Unable to create user in Moodle." );
	
		} catch ( \Exception $e ) {
			Util::log( "[MooWoodle] Moodle user creation error for user ID {$user_id}: " . $e->getMessage() );
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
	/**
	 * Update Moodle user details with data from the WordPress purchaser.
	 *
	 * Fetches user data and sends an update request to Moodle for the given Moodle user ID.
	 *
	 * @param int $moodle_user_id Moodle user ID to update.
	 * @param int $purchaser_id WordPress user ID of the purchaser.
	 * @return int Returns the Moodle user ID after update.
	 */
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
		$start_end_date = MooWoodle()->setting->get_setting('start_end_date', []);
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
	 * Add new enrollment
	 * @param array $args
	 * @return bool|int|null
	 */
	public static function add_enrollment( $args ) {
		global $wpdb;

		if ( empty( $args ) || empty( $args['user_email'] ) ) {
			return false;
		}
		$table = $wpdb->prefix . Util::TABLES['enrollment'];

		try {
			$inserted = $wpdb->insert(
				$table,
				$args
			);

			return $inserted ? $wpdb->insert_id : false;

		} catch ( \Exception $error ) {
			return null;
		}
	}
	/**
	 * Update a particular enrollment
	 * @param int   $id
	 * @param array $args
	 * @return bool|int
	 */
	public static function update_enrollment( $id, $args ) {
		global $wpdb;

		if ( ! $id || empty( $args ) ) {
			return false;
		}
		$table = $wpdb->prefix . Util::TABLES['enrollment'];

		return $wpdb->update(
			$table,
			$args,
			[ 'id' => $id ]
		);
	}
	/**
	 * Get enrollment records based on filters
	 *
	 * @param array $where Filters like user_id, course_id, status, etc.
	 * @return array|null
	 */
	public static function get_enrollments( $where ) {
		global $wpdb;

		$table = $wpdb->prefix . Util::TABLES['enrollment'];
		$query_segments = [];

		// Default SELECT clause
		$select = '*';

		// Handle custom SELECT fields
		if ( isset( $where['select'] ) ) {
			$select = esc_sql( $where['select'] );
		}

		// Apply filters
		if ( isset( $where['id'] ) ) {
			$query_segments[] = $wpdb->prepare( "id = %d", $where['id'] );
		}

		if ( isset( $where['user_id'] ) ) {
			$query_segments[] = $wpdb->prepare( "user_id = %d", $where['user_id'] );
		}

		if ( isset( $where['user_email'] ) ) {
			$query_segments[] = $wpdb->prepare( "user_email = %s", $where['user_email'] );
		}

		if ( isset( $where['course_id'] ) ) {
			$query_segments[] = $wpdb->prepare( "course_id = %d", $where['course_id'] );
		}
		if ( isset( $where['course_id_not'] ) ) {
			$query_segments[] = $wpdb->prepare( "course_id != %d", $where['course_id_not'] );
		}
		if ( isset( $where['cohort_id'] ) ) {
			$query_segments[] = $wpdb->prepare( "cohort_id = %d", $where['cohort_id'] );
		}
		if ( isset( $where['cohort_id_not'] ) ) {
			$query_segments[] = $wpdb->prepare( "cohort_id != %d", $where['cohort_id_not'] );
		}
		if ( isset( $where['group_id'] ) ) {
			$query_segments[] = $wpdb->prepare( "group_id = %d", $where['group_id'] );
		}
		if ( isset( $where['group_id_not'] ) ) {
			$query_segments[] = $wpdb->prepare( "group_id != %d", $where['group_id_not'] );
		}
		if ( isset( $where['order_id'] ) ) {
			$query_segments[] = $wpdb->prepare( "order_id = %d", $where['order_id'] );
		}

		if ( isset( $where['group_item_id'] ) ) {
			$query_segments[] = $wpdb->prepare( "group_item_id = %d", $where['group_item_id'] );
		}

		if ( isset( $where['status'] ) ) {
			$query_segments[] = $wpdb->prepare( "status = %s", $where['status'] );
		}

		if ( isset( $where['date'] ) ) {
			$query_segments[] = $wpdb->prepare( "date = %s", $where['date'] );
		}

		if ( isset( $where['ids'] ) && is_array( $where['ids'] ) ) {
			$ids = implode( ',', array_map( 'intval', $where['ids'] ) );
			$query_segments[] = "id IN ($ids)";
		}

		if ( isset( $where['group_item_ids'] ) && is_array( $where['group_item_ids'] ) ) {
			$ids = implode( ',', array_map( 'intval', $where['group_item_ids'] ) );
			$query_segments[] = "group_item_id IN ($ids)";
		}

		// Start building the query
		$query = "SELECT $select FROM $table";

		// WHERE clause
		if ( !empty( $query_segments ) ) {
			$query .= " WHERE " . implode( ' AND ', $query_segments );
		}

		// LIMIT and OFFSET
		if ( isset( $where['limit'] ) && isset( $where['offset'] ) ) {
			$limit = intval( $where['limit'] );
			$offset = intval( $where['offset'] );
			$query .= $wpdb->prepare( " LIMIT %d OFFSET %d", $limit, $offset );
		}

		return $wpdb->get_results( $query, ARRAY_A );
	}

}