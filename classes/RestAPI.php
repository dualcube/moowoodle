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

        // If user is admin or customer
        if ( current_user_can( 'customer' ) || current_user_can( 'manage_options' ) ) {
            add_action( 'rest_api_init', [ &$this, 'register_user_api' ] );
        }
    }

    /**
     * Rest api register function call on rest_api_init action hook.
     * @return void
     */
    public function register() {
        register_rest_route( MooWoodle()->rest_namespace, '/settings', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'set_settings' ],
            'permission_callback' =>[ $this, 'moowoodle_permission' ],
        ]);

        register_rest_route( MooWoodle()->rest_namespace, '/test-connection', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'test_connection' ],
            'permission_callback' =>[ $this, 'moowoodle_permission' ],
        ]);

        register_rest_route( MooWoodle()->rest_namespace, '/sync', [
            [
                'methods'             => 'POST',
                'callback'            =>[ $this, 'synchronize' ],
                'permission_callback' =>[ $this, 'moowoodle_permission' ],
            ],
            [
                'methods'             => 'GET',
                'callback'            =>[ $this, 'get_sync_status' ],
                'permission_callback' =>[ $this, 'moowoodle_permission' ],
            ]

        ]);

        register_rest_route( MooWoodle()->rest_namespace, '/courses', [
            'methods'             => 'GET',
            'callback'            =>[ $this, 'get_courses' ],
            'permission_callback' =>[ $this, 'moowoodle_permission' ],
        ]);
        register_rest_route( MooWoodle()->rest_namespace, '/all-filters', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'get_all_filters' ],
            'permission_callback' =>[ MooWoodle()->restAPI, 'moowoodle_permission' ],
        ]);

        register_rest_route( MooWoodle()->rest_namespace, '/logs', [
            'methods'             => 'GET',
            'callback'            =>[ $this, 'get_log' ],
            'permission_callback' =>[ $this, 'moowoodle_permission' ],
        ]);

    }

    public function register_user_api(){

        register_rest_route( MooWoodle()->rest_namespace, '/my-acc-courses', [
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            =>[ $this, 'get_user_courses' ],
            'permission_callback' =>[ $this, 'user_can_access_api' ],
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
    public function user_can_access_api()
    {
        return current_user_can( 'customer' ) || current_user_can( 'manage_options' );
    }
    
    /**
     * Seve the setting set in react's admin setting page.
     * @param mixed $request rest api request object
     * @return \WP_Error | \WP_REST_Response
     */
    public function set_settings( $request ) {
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

    public function synchronize( $request ) {
        $parameter = $request->get_param('parameter');

        if ($parameter == 'course') {
            $this->synchronize_course($request);
        } elseif ($parameter == 'user') {
            do_action( 'moowoodle_sync_all_users', $request );
        } elseif ($parameter == 'cohort') {
            // Pro feature: Sync all cohorts in MooWoodle
            do_action( 'moowoodle_sync_all_cohorts' );
        } else {
            do_action( 'moowoodle_sync' );
        }
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

            MooWoodle()->category->save_categories( $categories );
            
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
     * @param mixed $request
     * @return \WP_Error|\WP_REST_Response
     */
    public function get_sync_status( $request ) {
        $nonce = $request->get_header( 'X-WP-Nonce' );
        if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            return new \WP_Error( 'invalid_nonce', __('Invalid nonce', 'multivendorx'), array( 'status' => 403 ) );
        }

        $response = [
            'status'  => [],
            'running' => false,
        ];

        $status = $request->get_param('parameter');

        if ($status == 'course') {
            $response = [
                'status'  => Util::get_sync_status( 'course' ),
                'running' => get_transient( 'course_sync_running' ),
            ];
        }

        if ($status == 'user') {
            $response = apply_filters( 'moowoodle_sync_user_status', $response );

        }

        return rest_ensure_response($response);
    }

    /**
     * Fetch all courses
     * 
     * @param mixed $request
     * @return \WP_Error|\WP_REST_Response
     */
    public function get_courses( $request ) {

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

        // If count_courses is requested, get the course count
        if ( $count_courses ) {
            $courses = MooWoodle()->course->get_course( [] );
            return rest_ensure_response( count( $courses ) );
        }

        // Base filter array
        $filters = [
            'limit'       => $limit,
            'offset'      => $offset,
        ];

        if ( ! empty( $category_field ) ) {
            $filters['category_id'] = $category_field;
        }
        
        // Add search filter
        if ( $search_action === 'course' ) {
            $filters['fullname'] = $search_field;
        } elseif ( $search_action === 'shortname' ) {
            $filters['shortname'] = $search_field;
        }

        // Get paginated courses
        $courses = MooWoodle()->course->get_course( $filters );

        if ( empty( $courses ) ) {
            return rest_ensure_response( [] );
        }

        $formatted_courses = [];

        foreach ( $courses as $course ) {
            $course_id        = (int) $course['id'];
            $product_id       = (int) ( $course['product_id'] );
            $synced_products  = [];
            $product_image    = '';

            if ( $product_id ) {
                $product = wc_get_product( $product_id );
                if ( $product ) {
                    $synced_products[ $product->get_name() ] = add_query_arg(
                        [ 'post' => $product->get_id(), 'action' => 'edit' ],
                        admin_url( 'post.php' )
                    );
                    $product_image = wp_get_attachment_url( $product->get_image_id() );
                }
            }

            $start = $course['startdate'] ? wp_date( 'M j, Y', $course['startdate'] ) : __( 'Not Set', 'moowoodle' );
            $end   = $course['enddate'] ? wp_date( 'M j, Y', $course['enddate'] ) : __( 'Not Set', 'moowoodle' );
            $date  = ( $course['startdate'] || $course['enddate'] ) ? "$start - $end" : 'NA';

            $moodle_url    = trailingslashit( MooWoodle()->setting->get_setting( 'moodle_url' ) ) . "course/edit.php?id={$course['moodle_course_id']}";
            $view_user_url = trailingslashit( MooWoodle()->setting->get_setting( 'moodle_url' ) ) . "user/index.php?id={$course['moodle_course_id']}";

            // Get categories
            $categories = MooWoodle()->category->get_filtered_categories([
                'moodle_category_id' => $course['category_id']
            ]);
            $category_name = ! empty( $categories ) ? $categories[0]['name'] : __( 'Uncategorized', 'moowoodle' );

            // Get enrolled users count
            $enroled_user = MooWoodle()->enrollment->get_enrollments([
                'course_id' => $course['id']
            ]);

            $formatted_courses[] = apply_filters( 'moowoodle_formatted_course', [
                'id'                => $course_id,
                'moodle_url'        => $moodle_url,
                'moodle_course_id'  => $course['moodle_course_id'],
                'course_short_name' => $course['shortname'],
                'course_name'       => $course['fullname'],
                'products'          => $synced_products,
                'productimage'      => $product_image,
                'category_name'     => $category_name,
                'enroled_user'      => count( $enroled_user ),
                'view_users_url'    => $view_user_url,
                'date'              => $date,
            ]);
        }

        return rest_ensure_response( $formatted_courses );
    }

    

    public function get_all_filters( $request ) {
    
        // Verify nonce
        $nonce = $request->get_header('X-WP-Nonce');
        if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            return new \WP_Error( 'invalid_nonce', __( 'Invalid nonce', 'moowoodle' ), [ 'status' => 403 ] );
        }
    
        // Fetch all courses
        $courses = MooWoodle()->course->get_course([]);
        if ( empty( $courses ) ) {
            return rest_ensure_response([
                'courses'   => [],
                'category'  => [],
            ]);
        }
    
        // Extract unique category IDs
        $category_ids = array_unique( wp_list_pluck( $courses, 'category_id' ) );
    
        // Fetch categories
        $category = MooWoodle()->category->get_filtered_categories([
            'category_ids' => $category_ids,
        ]);
    
        // Prepare formatted course list
        $all_courses = [];
        foreach ( $courses as $course ) {
            $all_courses[ $course['id'] ] = $course['fullname'] ?: "Course {$course['id']}";
        }
    
        // Prepare formatted category list
        $all_category = [];
        foreach ( $category as $cat ) {
            $all_category[ $cat['moodle_category_id'] ] = $cat['name'] ?: "Category {$cat['moodle_category_id']}";
        }
    
        return rest_ensure_response( apply_filters( 'moowoodle_filters', [
            'courses'  => $all_courses,
            'category' => $all_category,
        ] ) );
                
    }
    
       
    /**
     * Save the setting set in react's admin setting page.
     * @param mixed $request
     * @return \WP_Error|\WP_REST_Response
     */
    public function get_log( $request ) {
        global $wp_filesystem;
        $action = $request->get_param('action');
        
        if ($action == 'download') {
            $this->download_log($request);
        }

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

        $user = wp_get_current_user();
    
        if ( empty( $user->ID ) ) {
            Util::log( "[MooWoodle] get_user_courses(): No logged-in user found." );
            return rest_ensure_response([
                'status' => 'error',
            ]);
        }
        $count    = $request->get_param( 'count' );
        
        if( $count ) {
            $all_enrollments = MooWoodle()->enrollment->get_enrollments([
                'user_id' => $user->ID,
                'status'  => 'enrolled'
            ]);
            return rest_ensure_response( count( $all_enrollments ));
        }

        $limit = max( 1, (int) $request->get_param( 'row' ) ?: 10 );
        $page     = max( 1, (int) $request->get_param( 'page' ) ?: 1 );
        $offset   = ( $page - 1 ) * $limit;

        $all_enrollments = MooWoodle()->enrollment->get_enrollments([
            'user_id' => $user->ID,
            'status'  => 'enrolled',
            'limit'   => $limit,
            'offset'  => $offset,

        ]);


        if ( empty( $all_enrollments ) ) {
            return rest_ensure_response([
                'data' => [],
                'status' => 'success',
            ]);
        }
    
        $data = array_map( function( $course ) use ( $user ) {
            $course_data = MooWoodle()->course->get_course([
                'id' => $course['course_id']
            ]);
            $course_data = is_array( $course_data ) ? reset( $course_data ) : $course_data;
    
            $passwordMoowoodle = get_user_meta( $user->ID, 'moowoodle_moodle_user_pwd', true );
    
            return [
                'user_name'      => $user->user_login,
                'course_name'    => $course_data['fullname'] ?? '',
                'enrolment_date' => date( 'M j, Y - H:i', strtotime( $course['date'] ) ),
                'password'       => $passwordMoowoodle,
                'moodle_url'     => ! empty( $course_data['moodle_course_id'] )
                    ? apply_filters(
                        'moodle_course_view_url',
                        trailingslashit( MooWoodle()->setting->get_setting( 'moodle_url' ) ) . "course/view.php?id={$course_data['moodle_course_id']}",
                        $course_data['moodle_course_id']
                    )
                    : null,
            ];
        }, $all_enrollments );
    
        $data = apply_filters( 'moowoodle_user_courses_data', $data, $user );
        
        return rest_ensure_response([
            'data' => $data,
            'status' => 'success',
        ]);
    }
    

}