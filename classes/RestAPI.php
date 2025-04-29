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
        register_rest_route( MooWoodle()->rest_namespace, '/get-cohorts', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'get_cohorts' ],
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

        register_rest_route( MooWoodle()->rest_namespace, '/courses', [
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            =>[ $this, 'get_user_courses' ],
            'permission_callback' =>[ $this, 'user_has_api_permission' ],
        ]);

    }

    /**
     * MooWoodle api permission function.
     * @return bool
     */
    public function moowoodle_permission() {
        return current_user_can( 'manage_options' );
    }

    /**
     * Check if the current user has API access based on allowed roles.
     *
     * @return bool True if the user is an administrator or customer, otherwise false.
     */
    public function user_has_api_permission() {
        return current_user_can( 'customer' ) || current_user_can( 'manage_options' );
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
            return new \WP_Error( 'invalid_nonce', __('Invalid nonce', 'moowoodle'), array( 'status' => 403 ) );
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
            return new \WP_Error( 'invalid_nonce', __('Invalid nonce', 'moowoodle'), array( 'status' => 403 ) );
        }
        // Flusk course sync status before sync start.
        Util::flush_sync_status( 'course' );

        set_transient( 'course_sync_running', true );
 
        $sync_setting = MooWoodle()->setting->get_setting( 'sync-course-options' );
        $sync_setting = is_array( $sync_setting ) ? $sync_setting : [];
        
        // get all category from moodle.
        $response   = MooWoodle()->external_service->do_request( 'get_categories' );
        $categories = $response[ 'data' ];
        // update course and product categories.
        if ( in_array( 'sync_courses_category', $sync_setting ) ) {

            Util::set_sync_status( [
                'action'    => __( 'Update Course Category', 'moowoodle' ),
                'total'     => count( $categories ),
                'current'   => 0
            ], 'course' );

            MooWoodle()->category->store_moodle_categories( $categories );
            
            Util::set_sync_status( [
                'action'    => __( 'Update Product Category', 'moowoodle' ),
                'total'     => count( $categories ),
                'current'   => 0
            ], 'course' );

            MooWoodle()->category->update_categories( $categories, 'product_cat' );

        } else {

            Util::set_sync_status( [
                'action'    => __( 'Store Moodle Course Category', 'moowoodle' ),
                'total'     => count( $categories ),
                'current'   => 0
            ], 'course' );

            MooWoodle()->category->store_moodle_categories( $categories );
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
        
        MooWoodle()->product->update_products( $courses );

        do_action( 'moowoodle_save_cohorts' );

        do_action( 'moowoodle_sync_all_course_groups' );

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
        global $wpdb;
    
        $nonce = $request->get_header( 'X-WP-Nonce' );
        if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            return new \WP_Error( 'invalid_nonce', __( 'Invalid nonce', 'moowoodle' ), [ 'status' => 403 ] );
        }
    
        $count_courses  = $request->get_param( 'count' );
        $limit          = max( intval( $request->get_param( 'row' ) ), 10 );
        $page           = max( intval( $request->get_param( 'page' ) ), 1 );
        $offset         = ( $page - 1 ) * $limit;
        $category_field = $request->get_param( 'catagory' );
        $search_action  = $request->get_param( 'searchaction' );
        $search_field   = $request->get_param( 'search' );
    
        $course_table   = $wpdb->prefix . 'moowoodle_courses';
        $category_table = $wpdb->prefix . 'moowoodle_categories';
    
        $where_clauses = [ "1=1" ];
        $query_params  = [];
    
        if ( $search_action === 'course' && $search_field ) {
            $where_clauses[] = "fullname LIKE %s";
            $query_params[]  = '%' . $wpdb->esc_like( $search_field ) . '%';
        } elseif ( $search_action === 'shortname' && $search_field ) {
            $where_clauses[] = "shortname LIKE %s";
            $query_params[]  = '%' . $wpdb->esc_like( $search_field ) . '%';
        }
    
        if ( $category_field ) {
            $where_clauses[] = "category_id = %d";
            $query_params[]  = intval( $category_field );
        }
    
        $where_sql = implode( ' AND ', $where_clauses );
    
        if ( $count_courses ) {
            $sql = "SELECT COUNT(*) FROM $course_table WHERE $where_sql";
            return rest_ensure_response( (int) $wpdb->get_var( $wpdb->prepare( $sql, $query_params ) ) );
        }
    
        $sql = "SELECT c.*, cat.name as category_name FROM $course_table c
                LEFT JOIN $category_table cat ON c.category_id = cat.moodle_category_id
                WHERE $where_sql ORDER BY c.id DESC LIMIT %d OFFSET %d";
        $query_params[] = $limit;
        $query_params[] = $offset;
    
        $courses = $wpdb->get_results( $wpdb->prepare( $sql, $query_params ) );
    
        if ( empty( $courses ) ) {
            return rest_ensure_response( [] );
        }
    
        $course_ids = array_map( fn( $c ) => $c->id, $courses );
    
        // Product map by course
        $product_map = [];
        $product_query = new \WC_Product_Query([
            'limit'      => -1,
            'status'     => 'publish',
            'meta_query' => [
                [
                    'key'     => 'linked_course_id',
                    'value'   => $course_ids,
                    'compare' => 'IN',
                ],
            ],
        ]);
        $products = $product_query->get_products();
    
        foreach ( $products as $product ) {
            $linked_id = (int) get_post_meta( $product->get_id(), 'linked_course_id', true );
            $product_map[ $linked_id ][] = $product;
        }
    
        // Enrolled count
        $placeholders = implode( ',', array_fill( 0, count( $course_ids ), '%d' ) );
        $enrollment_sql = "
            SELECT course_id, COUNT(*) as enrolled_count
            FROM {$wpdb->prefix}moowoodle_enrollment
            WHERE status = 'enrolled' AND course_id IN ($placeholders)
            GROUP BY course_id
        ";
        $enrollment_counts = $wpdb->get_results( $wpdb->prepare( $enrollment_sql, $course_ids ) );
    
        $enrollment_map = [];
        foreach ( $enrollment_counts as $row ) {
            $enrollment_map[ (int) $row->course_id ] = (int) $row->enrolled_count;
        }
    
        $formatted_courses = [];
    
        foreach ( $courses as $course ) {
            $course_id = (int) $course->id;
    
            $synced_products = [];
            $product_image   = '';
    
            if ( isset( $product_map[ $course_id ] ) ) {
                foreach ( $product_map[ $course_id ] as $product ) {
                    $synced_products[ $product->get_name() ] = add_query_arg( [ 'post' => $product->get_id(), 'action' => 'edit' ], admin_url( 'post.php' ) );
                    if ( ! $product_image ) {
                        $product_image = wp_get_attachment_url( $product->get_image_id() );
                    }
                }
            }
    
            $start = $course->startdate ? wp_date( 'M j, Y', $course->startdate ) : __( 'Not Set', 'moowoodle' );
            $end   = $course->enddate ? wp_date( 'M j, Y', $course->enddate ) : __( 'Not Set', 'moowoodle' );
            $date  = ( $course->startdate || $course->enddate ) ? "$start - $end" : 'NA';
    
            $moodle_url    = trailingslashit( MooWoodle()->setting->get_setting( 'moodle_url' ) ) . "course/edit.php?id={$course->moodle_course_id}";
            $view_user_url = trailingslashit( MooWoodle()->setting->get_setting( 'moodle_url' ) ) . "user/index.php?id={$course->moodle_course_id}";
    
            $formatted_courses[] = apply_filters( 'moowoodle_formatted_course', [
                'id'                => $course_id,
                'moodle_url'        => $moodle_url,
                'moodle_course_id'  => $course->moodle_course_id,
                'course_short_name' => $course->shortname,
                'course_name'       => $course->fullname,
                'products'          => $synced_products,
                'productimage'      => $product_image,
                'category_name'     => $course->category_name ?: __( 'Uncategorized', 'moowoodle' ),
                'enroled_user'      => $enrollment_map[ $course_id ] ?? 0,
                'view_users_url'    => $view_user_url,
                'date'              => $date,
            ] );
        }
    
        return rest_ensure_response( $formatted_courses );
    }
    public function get_cohorts( $request ) {
        global $wpdb;
    
        $nonce = $request->get_header( 'X-WP-Nonce' );
        if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            return new \WP_Error( 'invalid_nonce', __( 'Invalid nonce', 'moowoodle' ), [ 'status' => 403 ] );
        }
    
        $count_cohorts = $request->get_param( 'count' );
        $limit         = max( intval( $request->get_param( 'row' ) ), 10 );
        $page          = max( intval( $request->get_param( 'page' ) ), 1 );
        $offset        = ( $page - 1 ) * $limit;
        $search_action = $request->get_param( 'searchaction' );
        $search_field  = $request->get_param( 'search' );
    
        $cohort_table = $wpdb->prefix . 'moowoodle_cohorts';
    
        $where_clauses = [ "1=1" ];
        $query_params  = [];
    
        if ( $search_action === 'cohort' && $search_field ) {
            $where_clauses[] = "cohort_name LIKE %s";
            $query_params[]  = '%' . $wpdb->esc_like( $search_field ) . '%';
        } elseif ( $search_action === 'id' && $search_field ) {
            $where_clauses[] = "cohort_id LIKE %s";
            $query_params[]  = '%' . $wpdb->esc_like( $search_field ) . '%';
        }
    
        $where_sql = implode( ' AND ', $where_clauses );
    
        if ( $count_cohorts ) {
            $sql = "SELECT COUNT(*) FROM $cohort_table WHERE $where_sql";
            return rest_ensure_response( (int) $wpdb->get_var( $wpdb->prepare( $sql, $query_params ) ) );
        }
    
        $sql = "SELECT * FROM $cohort_table
                WHERE $where_sql ORDER BY id DESC LIMIT %d OFFSET %d";
    
        $query_params[] = $limit;
        $query_params[] = $offset;
    
        $cohorts = $wpdb->get_results( $wpdb->prepare( $sql, $query_params ) );
    
        if ( empty( $cohorts ) ) {
            return rest_ensure_response( [] );
        }
    
        $cohort_ids = array_map( fn( $c ) => $c->id, $cohorts );
    
        // Synced WooCommerce products by cohort
        $product_map = [];
        $product_query = new \WC_Product_Query([
            'limit'      => -1,
            'status'     => 'publish',
            'meta_query' => [
                [
                    'key'     => 'linked_cohort_id',
                    'value'   => $cohort_ids,
                    'compare' => 'IN',
                ],
            ],
        ]);
        $products = $product_query->get_products();
    
        foreach ( $products as $product ) {
            $linked_id = (int) get_post_meta( $product->get_id(), 'linked_cohort_id', true );
            $product_map[ $linked_id ][] = $product;
        }
    
        // Enrollment count for cohorts
        $placeholders = implode( ',', array_fill( 0, count( $cohort_ids ), '%d' ) );
        $enrollment_sql = "
            SELECT cohort_id, COUNT(*) as enrolled_count
            FROM {$wpdb->prefix}moowoodle_enrollment
            WHERE status = 'enrolled' AND cohort_id IN ($placeholders)
            GROUP BY cohort_id
        ";
        $enrollment_counts = $wpdb->get_results( $wpdb->prepare( $enrollment_sql, $cohort_ids ) );
    
        $enrollment_map = [];
        foreach ( $enrollment_counts as $row ) {
            $enrollment_map[ (int) $row->cohort_id ] = (int) $row->enrolled_count;
        }
    
        $formatted_cohorts = [];
    
        foreach ( $cohorts as $cohort ) {
            $cohort_id = (int) $cohort->id;
    
            $synced_products = [];
            $product_image   = '';
    
            if ( isset( $product_map[ $cohort_id ] ) ) {
                foreach ( $product_map[ $cohort_id ] as $product ) {
                    $synced_products[ $product->get_name() ] = add_query_arg( [ 'post' => $product->get_id(), 'action' => 'edit' ], admin_url( 'post.php' ) );
                    if ( ! $product_image ) {
                        $product_image = wp_get_attachment_url( $product->get_image_id() );
                    }
                }
            }
    
            $moodle_url    = trailingslashit( MooWoodle()->setting->get_setting( 'moodle_url' ) ) . "cohort/edit.php?id={$cohort->cohort_id}";
            $view_user_url = trailingslashit( MooWoodle()->setting->get_setting( 'moodle_url' ) ) . "cohort/index.php?id={$cohort->cohort_id}";
    
            $formatted_cohorts[] = [
                'id'             => $cohort_id,
                'moodle_url'     => $moodle_url,
                'cohort_id'      => $cohort->cohort_id,
                'cohort_name'    => $cohort->cohort_name,
                'products'       => $synced_products,
                'productimage'   => $product_image,
                'enroled_user'   => $enrollment_map[ $cohort_id ] ?? 0,
                'view_users_url' => $view_user_url,
                'date'           => wp_date( 'M j, Y', strtotime( $cohort->created_at ) ),
            ];
        }
    
        return rest_ensure_response( $formatted_cohorts );
    }
    

    /**
     * Get all courses
     * @param mixed $request
     * @return \WP_Error|\WP_REST_Response
     */
    public function get_all_courses( $request ) {
        global $wpdb;
    
        // Verify nonce
        $nonce = $request->get_header('X-WP-Nonce');
        if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            return new \WP_Error( 'invalid_nonce', __( 'Invalid nonce', 'moowoodle' ), [ 'status' => 403 ] );
        }
    
        // Fetch all courses
        $courses = $wpdb->get_results("
            SELECT id, moodle_course_id, shortname, category_id, fullname
            FROM {$wpdb->prefix}moowoodle_courses
        ");
    
        if ( empty( $courses ) ) {
            return rest_ensure_response([
                'courses'   => [],
                'products'  => [],
                'category'  => [],
                'shortname' => []
            ]);
        }
    
        // Extract course and category IDs
        $course_ids   = wp_list_pluck( $courses, 'id' );
        $category_ids = array_unique( wp_list_pluck( $courses, 'category_id' ) );
    
        // Fetch products linked to courses
        $linked_products = wc_get_products([
            'limit'      => -1,
            'meta_query' => [[
                'key'     => 'linked_course_id',
                'value'   => $course_ids,
                'compare' => 'IN',
            ]]
        ]);
    
        $all_products = [];
        foreach ( $linked_products as $product ) {
            $all_products[ $product->get_id() ] = $product->get_name();
        }
    
        // Securely fetch all relevant categories
        $all_category = [];
        if ( ! empty( $category_ids ) ) {
            $placeholders = implode( ',', array_fill( 0, count( $category_ids ), '%d' ) );
            $sql = $wpdb->prepare("
                SELECT moodle_category_id, name FROM {$wpdb->prefix}moowoodle_categories
                WHERE moodle_category_id IN ($placeholders)
            ", ...$category_ids );
    
            $category_rows = $wpdb->get_results( $sql );
    
            foreach ( $category_rows as $cat ) {
                $all_category[ $cat->moodle_category_id ] = $cat->name;
            }
        }
    
        // Collect course titles and shortnames
        $all_courses = [];
        $all_short_name = [];
    
        foreach ( $courses as $course ) {
            $all_courses[ $course->id ] = $course->fullname ?: "Course {$course->id}";
            $all_short_name[] = $course->shortname;
        }
    
        return rest_ensure_response([
            'courses'   => $all_courses,
            'products'  => $all_products,
            'category'  => $all_category,
            'shortname' => $all_short_name
        ]);
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
        $filePath = MooWoodle()->moowoodle_logs_dir . '/' . $file;

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
     * Fetch all enrolled courses for the current user.
     *
     * @param WP_REST_Request $request The REST API request.
     * 
     * @return WP_REST_Response|\WP_Error JSON response containing enrolled courses and pagination details.
     */
    public function get_user_courses( $request ) {
        global $wpdb;
    
        $user = wp_get_current_user();
    
        if ( empty( $user->ID ) ) {
            Util::_log( "[MooWoodle] get_user_courses(): No logged-in user found." );
    
            return rest_ensure_response([
                'data' => [],
                'pagination' => null,
                'status' => 'error',
                'message' => __( 'User not found', 'moowoodle' )
            ]);
        }
    
        $per_page = max( 1, (int) $request->get_param( 'row' ) ?: 10 );
        $page     = max( 1, (int) $request->get_param( 'page' ) ?: 1 );
        $offset   = ( $page - 1 ) * $per_page;
    
        $total_courses = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}moowoodle_enrollment WHERE user_id = %d AND status = 'enrolled'",
            $user->ID
        ) );
    
        $courses = $wpdb->get_results( $wpdb->prepare(
            "SELECT course_id, date 
             FROM {$wpdb->prefix}moowoodle_enrollment 
             WHERE user_id = %d AND status = 'enrolled' 
             ORDER BY date DESC 
             LIMIT %d OFFSET %d",
            $user->ID, $per_page, $offset
        ) );
    
        if ( ! $courses ) {
            Util::_log( "[MooWoodle] No enrolled courses found for user #{$user->ID}." );
        }
    
        $data = array_map( function( $course ) use ( $user ) {
            $linked_product_id = get_post_meta( $course->course_id, 'linked_product_id', true );
            $moodle_course_id  = get_post_meta( $linked_product_id, 'moodle_course_id', true );
            $passwordMoowoodle = get_user_meta( $user->ID, 'moowoodle_moodle_user_pwd', true );
    
            if ( ! $linked_product_id || ! $moodle_course_id ) {
                Util::_log( "[MooWoodle] Course #{$course->course_id} has missing metadata (linked_product_id or moodle_course_id)." );
            }
    
            return [
                'user_name'      => $user->user_login,
                'course_name'    => get_the_title( $linked_product_id ) ?: __( 'Unknown Course', 'moowoodle' ),
                'enrolment_date' => date( 'M j, Y - H:i', strtotime( $course->date ) ),
                'password'       => $passwordMoowoodle,
                'moodle_url'     => $moodle_course_id
                    ? apply_filters(
                        'moodle_course_view_url',
                        trailingslashit( MooWoodle()->setting->get_setting( 'moodle_url' ) ) . "course/view.php?id={$moodle_course_id}",
                        $moodle_course_id
                    )
                    : null,
            ];
            
        }, $courses );
    
        return rest_ensure_response([
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page'     => $per_page,
                'total_items'  => $total_courses,
                'total_pages'  => max( 1, ceil( $total_courses / $per_page ) ),
                'next_page'    => $page < ceil( $total_courses / $per_page ) ? $page + 1 : null,
                'prev_page'    => $page > 1 ? $page - 1 : null
            ],
            'status' => 'success',
            'message' => __( 'Courses fetched successfully', 'moowoodle' )
        ]);
    }
    
    
    

}