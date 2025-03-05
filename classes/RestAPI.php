<?php

namespace MooWoodle;

defined('ABSPATH') || exit;

/**
 * MooWoodle Rest API class
 * Creates Rest API end point. 
 */
class RestAPI {
    /**
     * RestAPI construct function
     */
    function __construct() {
        // If user is admin
        if ( current_user_can( 'manage_options' ) ) {
            add_action( 'rest_api_init', [ &$this, 'register' ] );
        }

        add_action( 'rest_api_init', [ &$this, 'register_user_api' ] );
    }

    /**
     * Rest api register function call on rest_api_init action hook.
     * @return void
     */
    public function register() {
        register_rest_route( MooWoodle()->rest_namespace, '/save-moowoodle-setting', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'save_moowoodle_setting' ],
            'permission_callback' =>[ $this, 'moowoodle_permission' ],
        ]);

        register_rest_route( MooWoodle()->rest_namespace, '/test-connection', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'test_connection' ],
            'permission_callback' =>[ $this, 'moowoodle_permission' ],
        ]);

        register_rest_route( MooWoodle()->rest_namespace, '/sync-course', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'synchronize_course' ],
            'permission_callback' =>[ $this, 'moowoodle_permission' ],
        ]);

        register_rest_route( MooWoodle()->rest_namespace, '/sync-status-course', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'get_sync_status' ],
            'permission_callback' =>[ $this, 'moowoodle_permission' ],
        ]);

        register_rest_route( MooWoodle()->rest_namespace, '/get-courses', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'get_courses' ],
            'permission_callback' =>[ $this, 'moowoodle_permission' ],
        ]);

        register_rest_route( MooWoodle()->rest_namespace, '/all-courses', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'get_all_courses' ],
            'permission_callback' =>[ MooWoodle()->restAPI, 'moowoodle_permission' ],
        ]);

        register_rest_route( MooWoodle()->rest_namespace, '/fetch-log', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'get_log' ],
            'permission_callback' =>[ $this, 'moowoodle_permission' ],
        ]);

        register_rest_route( MooWoodle()->rest_namespace, '/download-log', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'download_log' ],
            'permission_callback' =>[ $this, 'moowoodle_permission' ],
        ]);
    }

    public function register_user_api(){

        register_rest_route( MooWoodle()->rest_namespace, '/get-user-courses', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'get_user_courses' ],
            'permission_callback' => '__return_true',
        ]);
        register_rest_route( MooWoodle()->rest_namespace, '/classroom', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'get_user_groups' ],
            'permission_callback' => '__return_true',
        ]);
        register_rest_route( MooWoodle()->rest_namespace, '/get-user-enrollments-by-group-item-id', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'get_user_enrollments_by_group_item_id' ],
            'permission_callback' => '__return_true',
        ]);
        register_rest_route( MooWoodle()->rest_namespace, '/enroll-user', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'enroll_user' ],
            'permission_callback' => '__return_true',
        ]);


    }

    /**
     * MooWoodle api permission function.
     * @return bool
     */
    public function moowoodle_permission() {
        return current_user_can( 'manage_options' ) || true;
    }

    /**
     * Seve the setting set in react's admin setting page.
     * @param mixed $request rest api request object
     * @return \WP_Error | \WP_REST_Response
     */
    public function save_moowoodle_setting( $request ) {
        $nonce = $request->get_header( 'X-WP-Nonce' );
        if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            return new \WP_Error( 'invalid_nonce', __('Invalid nonce', 'multivendorx'), array( 'status' => 403 ) );
        }
        try {
            $all_details = [];
            $settings_data = $request->get_param( 'setting' );
            $settingsname = $request->get_param( 'settingName' );
            $settingsname = str_replace( "-", "_", "moowoodle_" . $settingsname . "_settings" );

            // save the settings in database
            MooWoodle()->setting->update_option( $settingsname, $settings_data );

            /**
             * Moodle after setting save.
             * @var $settingsname settingname
             * @var $settingdata settingdata
             */
            do_action( 'moowoodle_after_setting_save', $settingsname, $settings_data );

            $all_details[ 'error' ] = __( 'Settings Saved', 'moowoodle' );

            return $all_details;

        } catch ( \Exception $err ) {
            return rest_ensure_response( __( 'Unabled to Saved', 'moowoodle' ) );
        }
    }

    /**
     * Test Connection with Moodle server
     * @param mixed $request rest request object
     * @return \WP_Error| \WP_REST_Response
     */
    public function test_connection( $request ) {
        $nonce = $request->get_header( 'X-WP-Nonce' );
        if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            return new \WP_Error( 'invalid_nonce', __('Invalid nonce', 'multivendorx'), array( 'status' => 403 ) );
        }
        $action    = $request->get_param( 'action' );
        $user_id   = $request->get_param( 'user_id' );
        $course_id = $request->get_param( 'course_id' );
        $response  = [];

        switch( $action ) {
            case 'get_site_info':
                $response = TestConnection::get_site_info();
                break;
            case 'get_course':
                $response = TestConnection::get_course();
                break;
            case 'get_catagory':
                $response = TestConnection::get_catagory();
                break;
            case 'create_user':
                $response = TestConnection::create_user();
                break;
            case 'get_user':
                $response = TestConnection::get_user();
                break;
            case 'update_user':
                $response = TestConnection::update_user( $user_id );
                break;
            case 'enroll_user':
                $response = TestConnection::enrol_users( $user_id, $course_id );
                break;
            case 'unenroll_user':
                $response = TestConnection::unenrol_users( $user_id, $course_id );
                break;
            case 'delete_user':
                $response = TestConnection::delete_users( $user_id );
                break;
            default:
                $response = [ 'error' => $action . ' Test connection function is not defiend' ];
        }

        return rest_ensure_response( $response );
    }

    /**
     * Seve the setting set in react's admin setting page
     * @param mixed $request rest api request object
     * @return \WP_Error | \WP_REST_Response
     */
    public function synchronize_course( $request ) {
        $nonce = $request->get_header( 'X-WP-Nonce' );
        if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            return new \WP_Error( 'invalid_nonce', __('Invalid nonce', 'multivendorx'), array( 'status' => 403 ) );
        }
        // Flusk course sync status before sync start.
        Util::flush_sync_status( 'course' );

        set_transient( 'course_sync_running', true );

        $sync_setting = MooWoodle()->setting->get_setting( 'sync-course-options' );
        $sync_setting = is_array( $sync_setting ) ? $sync_setting : [];
        
        // update course and product categories.
        if ( in_array( 'sync_courses_category', $sync_setting ) ) {

            // get all category from moodle.
            $response   = MooWoodle()->external_service->do_request( 'get_categories' );
            $categories = $response[ 'data' ];

            Util::set_sync_status( [
                'action'    => __( 'Update Course Category', 'moowoodle' ),
                'total'     => count( $categories ),
                'current'   => 0
            ], 'course' );

            MooWoodle()->category->update_categories( $categories, 'course_cat' );

            Util::set_sync_status( [
                'action'    => __( 'Update Product Category', 'moowoodle' ),
                'total'     => count( $categories ),
                'current'   => 0
            ], 'course' );

            MooWoodle()->category->update_categories( $categories, 'product_cat' );
        }

		// get all caurses from moodle.
		$response = MooWoodle()->external_service->do_request( 'get_courses' );
        $courses  = $response[ 'data' ];

        // Update all course
        Util::set_sync_status( [
            'action'    => __( 'Update Course', 'moowoodle' ),
            'total'     => count( $courses ) - 1,
            'current'   => 0
        ], 'course' );

        MooWoodle()->course->update_courses( $courses );
        
        // Update all product
        Util::set_sync_status( [
            'action'    => __( 'Update Product', 'moowoodle' ),
            'total'     => count( $courses ) - 1,
            'current'   => 0
        ], 'course' );

        MooWoodle()->product->update_products( $courses );
        
        delete_transient( 'course_sync_running' );

        /**
         * Action hook after moowoodle course sync.
         */
        do_action( 'moowoodle_after_sync_course' );

        return rest_ensure_response( true );
    }

    /**
     * Get sync status
     * @param mixed $reques
     * @return \WP_Error|\WP_REST_Response
     */
    public function get_sync_status( $reques ) {
        $nonce = $reques->get_header( 'X-WP-Nonce' );
        if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            return new \WP_Error( 'invalid_nonce', __('Invalid nonce', 'multivendorx'), array( 'status' => 403 ) );
        }
        return rest_ensure_response([
            'status'  => Util::get_sync_status( 'course' ),
            'running' => get_transient( 'course_sync_running' ),
        ]);
    }

    /**
     * Fetch all course
     * @param mixed $request
     * @return \WP_Error|\WP_REST_Response
     */
    public function get_courses( $request ) {
        $count_courses  = $request->get_param( 'count' );
        $par_page       = $request->get_param( 'row' );
        $page           = $request->get_param( 'page' );
        $product_field  = $request->get_param( 'product' );
        $catagory_field = $request->get_param( 'catagory' );
        $search_action  = $request->get_param( 'searchaction' );
        $search_field   = $request->get_param( 'search');
        $nonce          = $request->get_header( 'X-WP-Nonce' );

        if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            return new \WP_Error( 'invalid_nonce', __('Invalid nonce', 'multivendorx'), array( 'status' => 403 ) );
        }

        // Prepare argument for database query
        $args = [
            'fields'         => 'ids',
            'numberposts'    => -1,
            'posts_per_page' => $par_page,
            'paged'          => $page
        ];

        // Filter by course
        if ( $search_action == 'course' ) {
            $args[ 's' ] = $search_field;
        }

        // Filter by category
        if ( ! empty( $catagory_field ) ) {
            $args[ 'meta_query' ] = [
                [
                    'key'   => '_category_id',
                    'value' => intval( $catagory_field ),
                ]
            ];
        }

        // Filter by product
        if ( ! empty( $product_field ) ) {
            $args[ 'meta_query' ] = [
                [
                    'key'   => 'linked_product_id',
                    'value' => intval( $product_field ),
                ]
            ];
        }

        // Filter by shortname.
        if ( $search_action == 'shortname' ) {
            $args[ 'meta_query' ] = [
                [
                    'key'     => '_course_short_name',
                    'value'   => $search_field,
                    'compare' => 'LIKE'
                ]
            ];
        }

        // Get the courses
        $course_ids = MooWoodle()->course->get_courses( $args );
        
        // Set response as number of courses if count request is set.
        if ( $count_courses ) {
            return rest_ensure_response( count( $course_ids ) );
        }

        $formatted_courses = [];

		foreach ( $course_ids as $course_id ) {
			// get course all post meta.
			$course_meta = array_map( 'current', get_post_meta( $course_id, '', true ) );

			//get term object by course category id.
			$term = MooWoodle()->category->get_category( $course_meta[ '_category_id' ], 'course_cat' );

            $products = wc_get_products([
                'meta_query' => [
                    [
                        'key'   => 'linked_course_id',
                        'value' => $course_id,
                    ]
                ]
            ]);

            $synced_products = [];
            $count_enrolment = 0;
            $product_image   = '';

			foreach ( $products as $product ) {
				$synced_products[ $product->get_name() ] = add_query_arg( [ 'post' => $product->get_id() , 'action' => 'edit' ], admin_url( 'post.php' ) );
                $count_enrolment += (int) $product->get_meta( 'total_sales' );
                $product_image   = wp_get_attachment_url( $product->get_image_id() );
			} 
            // Prepare date
            $course_startdate = $course_meta[ '_course_startdate' ];
            $course_enddate   = $course_meta[ '_course_enddate' ];

			if (!$course_startdate && !$course_enddate) {
                $date = 'NA';
            } else {
                $date = $course_startdate ? wp_date( 'M j, Y', $course_startdate ) :  __('Not Set', 'moowoodle');
                $date .= $course_enddate ? ' - ' . wp_date( 'M j, Y  ', $course_enddate ) :  __(' - Not Set', 'moowoodle');
            }

            // Get moowoodle course id
            $moodle_course_id = $course_meta[ 'moodle_course_id' ];

            // Prepare url
            $moodle_url    = trailingslashit( MooWoodle()->setting->get_setting( 'moodle_url' ) ) . 'course/edit.php?id=' . $moodle_course_id;
            $view_user_url = trailingslashit( MooWoodle()->setting->get_setting( 'moodle_url' ) ) . 'user/index.php?id=' . $moodle_course_id;
            $category_url  = add_query_arg( [ 'course_cat' => $term->slug, 'post_type' => 'course' ], admin_url( 'edit.php' ) );
            /**
             * Filter for add additional data.
             * @var array formatted course data.
             */
			$formatted_courses[] = apply_filters( 'moowoodle_formatted_course', [
				'id'                => $course_id,
				'moodle_url'        => $moodle_url,
				'moodle_course_id'  => $moodle_course_id,
				'course_short_name' => $course_meta[ '_course_short_name' ],
				'course_name'       => get_the_title( $course_id ),
				'products'          => $synced_products,
                'productimage'      => $product_image,
				'category_name'     => $term->name,
				'category_url'      => $category_url,
				'enroled_user'      => $count_enrolment,
                'view_users_url'    => $view_user_url,
				'date'              => $date,
			]);
		}
        return rest_ensure_response( $formatted_courses );
    }

    /**
     * get all courses
     * @param mixed $request
     * @return \WP_Error|\WP_REST_Response
     */
	public function get_all_courses($request) {
        $nonce = $request->get_header( 'X-WP-Nonce' );
        if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            return new \WP_Error( 'invalid_nonce', __('Invalid nonce', 'multivendorx'), array( 'status' => 403 ) );
        }
		$course_ids = MooWoodle()->course->get_courses([
            'fields'      => 'ids',
            'numberposts' => -1,
        ]);

		$all_courses = $all_products = $all_category = $all_short_name = [];
		foreach ( $course_ids as $course_id ) {
			$all_courses[$course_id] = get_the_title( $course_id );
            $products = wc_get_products([
                'meta_query' => [
                    [
                        'key'   => 'linked_course_id',
                        'value' => $course_id,
                    ]
                ]
            ]);
            foreach ( $products as $product ) {
				$all_products[$product->get_id()] = $product->get_name();
			}

            $course_meta = get_post_meta( $course_id, '_category_id', true );
            $term = MooWoodle()->category->get_category( $course_meta, 'course_cat' );
			$all_category[$course_meta] = $term->name;

            $course_meta = get_post_meta( $course_id, '_course_short_name', true );
			$all_short_name[] = $course_meta;
		}

        $all_data = [
            'courses'   => $all_courses,
            'products'   => $all_products,
            'category'   => $all_category,
            'shortname'   => $all_short_name
        ];
		
        return rest_ensure_response( $all_data );
	}

    /**
     * Save the setting set in react's admin setting page.
     * @param mixed $request
     * @return \WP_Error|\WP_REST_Response
     */
    public function get_log( $request ) {
        global $wp_filesystem;
        if ( ! $wp_filesystem ) {
            require_once ABSPATH . '/wp-admin/includes/file.php';
            WP_Filesystem();
        }

        $nonce = $request->get_header( 'X-WP-Nonce' );
        if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            return new \WP_Error( 'invalid_nonce', __('Invalid nonce', 'multivendorx'), array( 'status' => 403 ) );
        }

        $log_count = $request->get_param( 'logcount' );
        $log_count = $log_count ? $log_count : 100;

        $clear     = $request->get_param( 'clear' );

        if ( $clear ) {
            $wp_filesystem->delete( MooWoodle()->log_file );
            // delete the logfile name from options table
            delete_option( 'moowoodle_log_file' );

            return rest_ensure_response( true );
        }

        $logs = [];

        if ( file_exists( MooWoodle()->log_file ) ) {   
            // Get the contents of the log file using the filesystem API
            $log_content = $wp_filesystem->get_contents( MooWoodle()->log_file );
            if ( ! empty( $log_content ) ) {
                $logs = explode( "\n", $log_content );
            }
        }
        
        return rest_ensure_response( array_reverse( array_slice( $logs, - $log_count ) ) );
    }

    /**
     * Download the log.
     * @param mixed $request
     * @return \WP_Error|\WP_REST_Response
     */
    function download_log($request) {
        $nonce = $request->get_header( 'X-WP-Nonce' );
        if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            return new \WP_Error( 'invalid_nonce', __('Invalid nonce', 'multivendorx'), array( 'status' => 403 ) );
        } 
        // Get the file parameter from the request
        $file = $request->get_param('file');
        $file = basename($file);
        $filePath = MOOWOODLE_LOGS_DIR . '/' . $file;

        // Check if the file exists and has the right extension
        if (file_exists($filePath) && preg_match('/\.(txt|log)$/', $file)) {
            // Set headers to force download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
    
            // Clear output buffer and read the file
            ob_clean();
            flush();
            readfile($filePath);
            exit;
        } else {
            return new \WP_Error('file_not_found', 'File not found', array('status' => 404));
        }
    }

        /**
     * Fetch all course
     * @param mixed $request
     * @return \WP_Error|\WP_REST_Response
     */
    public function get_user_courses( $request ) {
        global $wpdb;
        $customer = wp_get_current_user();

        if ( !$customer->ID ) {
            return new \WP_Error( 'no_user', __( 'User not found', 'moowoodle' ), [ 'status' => 403 ] );
        }

        // Get pagination parameters
        $per_page = $request->get_param( 'row' ) ?: 10;
        $page     = $request->get_param( 'page' ) ?: 1;
        $offset   = ( $page - 1 ) * $per_page;

        // Define the table name
        $table_name = $wpdb->prefix . 'moowoodle_enrollment';

        // Query total courses
        $total_courses = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_name} WHERE user_id = %d AND status = 'enrolled'", 
            $customer->ID
        ));

        // Fetch paginated courses
        $courses = $wpdb->get_results( $wpdb->prepare(
            "SELECT course_id, order_id, item_id, date FROM {$table_name} 
            WHERE user_id = %d AND status = 'enrolled' 
            ORDER BY date DESC 
            LIMIT %d OFFSET %d", 
            $customer->ID, $per_page, $offset
        ));

        // Format courses
        $formatted_courses = [];
        foreach ( $courses as $course ) {
            $moodle_course_id = $course->course_id;
            $moodle_url       = trailingslashit( MooWoodle()->setting->get_setting( 'moodle_url' ) ) . 'course/view.php?id=' . $moodle_course_id;

            $formatted_courses[] = [
                'course_id'      => $course->course_id,
                'order_id'       => $course->order_id,
                'item_id'        => $course->item_id,
                'user_login'     => $customer->user_login,
                'password'       => get_user_meta( $customer->ID, 'moowoodle_moodle_user_pwd', true ),
                'enrolment_date' => date( 'M j, Y - H:i', strtotime( $course->date ) ),
                'moodle_url'     => $moodle_url,
            ];
        }

        return rest_ensure_response( [
            'total_courses' => $total_courses,
            'page'          => $page,
            'per_page'      => $per_page,
            'total_pages'   => ceil( $total_courses / $per_page ),
            'courses'       => $formatted_courses,
        ] );
    }
    /**
     * Fetch user groups and their items
     * @param mixed $request
     * @return \WP_Error|\WP_REST_Response
     */
    public function get_user_groups( $request ) {
        global $wpdb;
        $customer = wp_get_current_user();
    
        if ( !$customer->ID ) {
            return new \WP_Error( 'no_user', __( 'User not found', 'moowoodle' ), [ 'status' => 403 ] );
        }
    
        // Fetch all groups for the current user
        $groups = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}moowoodle_group WHERE user_id = %d ORDER BY created DESC", 
            $customer->ID
        ));
    
        if ( empty( $groups ) ) {
            return rest_ensure_response( [ 'groups' => [] ] );
        }
    
        $formatted_groups = [];
    
        foreach ( $groups as $group ) {
            // Fetch all items for this group
            $group_items = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}moowoodle_group_items WHERE group_id = %d",
                $group->id
            ));
    
            $formatted_items = [];
    
            foreach ( $group_items as $item ) {
                $product = wc_get_product( $item->product_id );
    
                if ( $product ) {
                    $formatted_items[] = [
                        'id'                => $item->id,
                        'group_id'          => $item->group_id,
                        'course_id'         => $item->course_id,
                        'product_id'        => $item->product_id,
                        'user_id'           => $item->user_id,
                        'total_quantity'    => $item->total_quantity,
                        'available_quantity'=> $item->available_quantity,
                        'status'            => $item->status,
                    ];
                }
            }
    
            $formatted_groups[] = [
                'group_id'   => $group->id,
                'group_name' => $group->name,
                'order_id'   => $group->order_id,
                'user_name'  => $group->user_name,
                'items'      => $formatted_items,
            ];
        }
    
        return rest_ensure_response( [ 'groups' => $formatted_groups ] );
    }
        
    /**
     * Fetch enrolled users by group_item_id
     * @param WP_REST_Request $request
     * @return WP_Error|WP_REST_Response
     */
    public function get_user_enrollments_by_group_item_id( $request ) {
        global $wpdb;
        $customer = wp_get_current_user();

        if ( !$customer->ID ) {
            return new \WP_Error( 'no_user', __( 'User not found', 'moowoodle' ), [ 'status' => 403 ] );
        }

        $group_item_id = $request->get_param('group_item_id');

        if ( empty( $group_item_id ) ) {
            return new \WP_Error( 'invalid_param', __( 'group_item_id is required', 'moowoodle' ), [ 'status' => 400 ] );
        }

        // Fetch enrollment details based on group_item_id
        $enrollments = $wpdb->get_results( $wpdb->prepare(
            "SELECT e.user_email, e.date 
            FROM {$wpdb->prefix}moowoodle_enrollment e
            WHERE e.group_item_id = %d",
            $group_item_id
        ));

        if ( empty( $enrollments ) ) {
            return rest_ensure_response( [ 'enrollments' => [] ] );
        }

        $formatted_enrollments = [];

        foreach ( $enrollments as $enrollment ) {
            $user = get_user_by( 'email', $enrollment->user_email );
            $username = $user ? $user->user_login : __( 'Unknown User', 'moowoodle' );

            $formatted_enrollments[] = [
                'name'  => $username,
                'email' => $enrollment->user_email,
                'date'  => date( 'Y-m-d H:i:s', strtotime( $enrollment->date ) ),
            ];
        }

        return rest_ensure_response( [ 'enrollments' => $formatted_enrollments ] );
    }
    /**
     * Enroll a user by group_item_id
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function enroll_user($request) {
        // Get parameters from request
        $params = $request->get_params();
        $email = sanitize_email($params['email'] ?? '');
        $name = sanitize_text_field($params['name'] ?? '');
        $group_item_id = intval($params['group_item_id'] ?? 0);
        $course_id = intval($params['course_id'] ?? 0);
        $order_id = intval($params['order_id'] ?? 0);

        // Validate required fields
        if (!$email || !$name || !$group_item_id) {
            return rest_ensure_response([
                'success' => false,
                'message' => __('Email, name, and group_item_id are required.', 'moowoodle'),
            ]);
        }

        // Validate email format
        if (!is_email($email)) {
            return rest_ensure_response([
                'success' => false,
                'message' => __('Invalid email address.', 'moowoodle'),
            ]);
        }

        // Check if user exists by email, otherwise create a new user
        $user = get_user_by('email', $email);
        if (!$user) {
            $password = wp_generate_password(12, false);
            $user_id = wp_create_user($name, $password, $email);

            if (is_wp_error($user_id)) {
                return rest_ensure_response([
                    'success' => false,
                    'message' => __('Failed to create user: ', 'moowoodle') . $user_id->get_error_message(),
                ]);
            }

            // Assign 'customer' role to new user
            wp_update_user(['ID' => $user_id, 'role' => 'customer']);
        } else {
            $user_id = $user->ID;
        }

        // Prepare enrollment data
        $enroll_data = [
            'purchaser_id'  => $user_id,
            'course_id'     => $course_id,
            'order_id'      => $order_id,
            'item_id'       => 0,
            'group_item_id' => $group_item_id,
            'suspend'       => 0,
        ];
        $response = Moowoodle()->enrollment->process_enrollment( $enroll_data );

        // Return success response with enrollment data
        return $response;
    }


    
}