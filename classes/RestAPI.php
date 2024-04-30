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

        register_rest_route( MooWoodle()->rest_namespace, '/sync-course-options', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'synchronize' ],
            'permission_callback' =>[ $this, 'moowoodle_permission' ],
        ]);

        register_rest_route( MooWoodle()->rest_namespace, '/courses', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'get_courses' ],
            'permission_callback' =>[ $this, 'moowoodle_permission' ],
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

            do_action( 'moowoodle_settings_after_save', $settingsname, $settings_data );

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

        Util::log($response);

        return rest_ensure_response( $response );
    }

    /**
     * Seve the setting set in react's admin setting page
     * @param mixed $request rest api request object
     * @return \WP_Error | \WP_REST_Response
     */
    public function synchronize( $request ) {

        // get all category from moodle.
        $response   = MooWoodle()->external_service->do_request( 'get_categories' );
        $categories = $response[ 'data' ];

        // update course and product categories.
        MooWoodle()->category->update_categories( $categories, 'course_cat' );
        MooWoodle()->category->update_categories( $categories, 'product_cat' );

		// get all caurses from moodle.
		$response = MooWoodle()->external_service->do_request( 'get_courses' );
        $courses  = $response[ 'data' ];

        MooWoodle()->course->update_courses( $courses );
        MooWoodle()->product->update_products( $courses );

        return rest_ensure_response( true );
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

        return rest_ensure_response( $formatted_courses );
    }

    /**
     * Seve the setting set in react's admin setting page.
     * @param mixed $request
     * @return \WP_Error|\WP_REST_Response
     */
    public function get_log() {
        $logs = [];
        if ( file_exists( MW_LOGS . "/error.txt" ) ) {
            $logs = explode( "\n", wp_remote_retrieve_body(wp_remote_get(get_site_url(null, str_replace(ABSPATH, '', MW_LOGS) . "/error.txt"))));
        }
        
        return rest_ensure_response($logs);
    }
}