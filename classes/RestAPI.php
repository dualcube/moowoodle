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

        register_rest_route( MooWoodle()->rest_namespace, '/sync-status', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'get_sync_status' ],
            'permission_callback' =>[ $this, 'moowoodle_permission' ],
        ]);

        register_rest_route( MooWoodle()->rest_namespace, '/courses', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'get_courses' ],
            'permission_callback' =>[ $this, 'moowoodle_permission' ],
        ]);

        register_rest_route( MooWoodle()->rest_namespace, '/all-courses', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'get_all_courses' ],
            'permission_callback' =>[ MooWoodle()->restAPI, 'moowoodle_permission' ],
        ]);

        register_rest_route( MooWoodle()->rest_namespace, '/all-products', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'get_all_products' ],
            'permission_callback' =>[ MooWoodle()->restAPI, 'moowoodle_permission' ],
        ]);

        register_rest_route( MooWoodle()->rest_namespace, '/all-category', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'get_all_category' ],
            'permission_callback' =>[ MooWoodle()->restAPI, 'moowoodle_permission' ],
        ]);

        register_rest_route( MooWoodle()->rest_namespace, '/fetch-log', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'get_log' ],
            'permission_callback' =>[ $this, 'moowoodle_permission' ],
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
        try {
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

            return rest_ensure_response( __( 'Settings Saved', 'woocommerce-stock-manager' ) );

        } catch ( \Exception $err ) {
            return rest_ensure_response( __( 'Unabled to Saved', 'woocommerce-stock-manager' ) );
        }
    }

    /**
     * Test Connection with Moodle server
     * @param mixed $request rest request object
     * @return \WP_Error| \WP_REST_Response
     */
    public function test_connection( $request ) {
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
        Util::flush_sync_status();

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
            ] );

            MooWoodle()->category->update_categories( $categories, 'course_cat' );

            Util::set_sync_status( [
                'action'    => __( 'Update Product Category', 'moowoodle' ),
                'total'     => count( $categories ),
                'current'   => 0
            ] );

            MooWoodle()->category->update_categories( $categories, 'product_cat' );
        }

		// get all caurses from moodle.
		$response = MooWoodle()->external_service->do_request( 'get_courses' );
        $courses  = $response[ 'data' ];

        if ( in_array( 'sync_courses', $sync_setting ) ) {
            Util::set_sync_status( [
                'action'    => __( 'Update Course', 'moowoodle' ),
                'total'     => count( $courses ),
                'current'   => 0
            ] );

            MooWoodle()->course->update_courses( $courses );
        }
        
        Util::set_sync_status( [
            'action'    => __( 'Update Product', 'moowoodle' ),
            'total'     => count( $courses ),
            'current'   => 0
        ] );
        
        MooWoodle()->product->update_products( $courses );
        
        /**
         * Action hook after moowoodle course sync.
         */
        do_action( 'moowoodle_after_sync_course' );
        
        // Retrive the sync status and flush it
        $sync_status = Util::get_sync_status();
        Util::flush_sync_status();

        return rest_ensure_response( $sync_status );
    }

    /**
     * Get sync status
     * @param mixed $reques
     * @return \WP_Error|\WP_REST_Response
     */
    public function get_sync_status( $reques ) {
        return rest_ensure_response( Util::get_sync_status() );
    }

    /**
     * Fetch all course
     * @param mixed $request
     * @return \WP_Error|\WP_REST_Response
     */
    public function get_courses( $request ) {
        $count_courses = $request->get_param( 'count' );
        $page          = $request->get_param( 'page' );
        $per_page      = $request->get_param( 'perpage' );

        $courseField          = $request->get_param( 'course' );
        $productField         = $request->get_param( 'product' );
        $catagoryField          = $request->get_param( 'catagory' );
        $shortnameField          = $request->get_param( 'shortname' );
        
        // Get the courses
        $course_ids = MooWoodle()->course->get_courses([
            'fields'      => 'ids',
            'numberposts' => -1,
        ]);

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

			foreach ( $products as $product ) {
				$synced_products[ $product->get_name() ] = add_query_arg( [ 'post' => $product->get_id() , 'action' => 'edit' ], admin_url( 'post.php' ) );
                $count_enrolment += (int) $product->get_meta( 'total_sales' );
			}

            // Prepare date
            $course_startdate = $course_meta[ '_course_startdate' ];
            $course_enddate   = $course_meta[ '_course_enddate' ];

			$date = wp_date( 'M j, Y', $course_startdate );
			
            // Append course end date if exist.
            if ( $course_enddate ) {
				$date .= ' - ' . wp_date( 'M j, Y  ', $course_enddate );
			}

            // Get moowoodle course id
            $moodle_course_id = $course_meta[ 'moodle_course_id' ];

            // Prepare url
            $moodle_url   = esc_url( MooWoodle()->setting->get_setting( 'moodle_url' ) ) . '/course/edit.php?id=' . $moodle_course_id;
            $category_url = add_query_arg( [ 'course_cat' => $term->slug, 'post_type' => 'course' ], admin_url( 'edit.php' ) );

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
				'category_name'     => $term->name,
				'category_url'      => $category_url,
				'enroled_user'      => $count_enrolment,
				'date'              => $date,
			]);
		}
        $filtered_courses = [];
        foreach ( $formatted_courses as $courses ) {
			if ( $courseField && $courses[ 'id' ] != $courseField ) {
				continue;
			}
			if ( $productField && $courses[ 'products' ] != get_the_title($productField) ) {
				continue;
			}
		
			$filtered_courses[] = $courses;
		}
        return rest_ensure_response( $filtered_courses );
    }

    /**
     * get all courses
     * @param mixed $request
     * @return \WP_Error|\WP_REST_Response
     */
	function get_all_courses() {
		$course_ids = MooWoodle()->course->get_courses([
            'fields'      => 'ids',
            'numberposts' => -1,
        ]);

		$all_courses = [];
		foreach ( $course_ids as $course_id ) {
			$all_courses[$course_id] = get_the_title( $course_id );
		}
		
        return rest_ensure_response( $all_courses );
	}

    /**
     * get all products
     * @param mixed $request
     * @return \WP_Error|\WP_REST_Response
     */
	function get_all_products() {
		$course_ids = MooWoodle()->course->get_courses([
            'fields'      => 'ids',
            'numberposts' => -1,
        ]);

		$all_products = [];
		foreach ( $course_ids as $course_id ) {
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
		}		
        return rest_ensure_response( $all_products );
	}

    /**
     * get all category
     * @param mixed $request
     * @return \WP_Error|\WP_REST_Response
     */
	function get_all_category() {
		$course_ids = MooWoodle()->course->get_courses([
            'fields'      => 'ids',
            'numberposts' => -1,
        ]);

		$all_category = [];
		foreach ( $course_ids as $course_id ) {
			$course_meta = array_map( 'current', get_post_meta( $course_id, '', true ) );
            $term = MooWoodle()->category->get_category( $course_meta[ '_category_id' ], 'course_cat' );
			$all_category[$term->term_id] = $term->name;
		}	
        return rest_ensure_response( $all_category );
    }

    /**
     * Save the setting set in react's admin setting page.
     * @param mixed $request
     * @return \WP_Error|\WP_REST_Response
     */
    public function get_log( $request ) {
        $log_count = $request->get_param( 'logcount' );
        $log_count = $log_count ? $log_count : 100;

        $clear     = $request->get_param( 'clear' );

        if ( $clear ) {
            wp_delete_file( MOOWOODLE_LOGS );
            return rest_ensure_response( true );
        }

        $logs = [];

        if ( file_exists( MOOWOODLE_LOGS ) ) {
            $logs = explode( "\n", wp_remote_retrieve_body( wp_remote_get( get_site_url( null, str_replace( ABSPATH, '', MOOWOODLE_LOGS ) ) ) ) );
        }
        
        return rest_ensure_response( array_reverse( array_slice( $logs, - $log_count ) ) );
    }
}
