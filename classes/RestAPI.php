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

        register_rest_route( MooWoodle()->rest_namespace, '/fetch-all-courses', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'fetch_all_courses' ],
            'permission_callback' =>[ $this, 'moowoodle_permission' ],
        ]);

        register_rest_route( MooWoodle()->rest_namespace, '/sync-course-options', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'synchronize' ],
            'permission_callback' =>[ $this, 'moowoodle_permission' ],
        ]);

        register_rest_route( MooWoodle()->rest_namespace, '/fetch-mw-log', [
            'methods'             => \WP_REST_Server::ALLMETHODS,
            'callback'            =>[ $this, 'mw_get_log' ],
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

		// // update all course and product.
		// foreach ( $courses as $course ) {
		// 	$course_ids = $product_ids = [];

		// 	// sync courses post data.
		// 	$course_id = MooWoodle()->Course->update_course( $course );
		// 	if($course_id) $course_ids[] = $course_id;

		// 	// sync product if enable.
		// 	if ($sync_now_options['sync_all_product']) {
		// 		$product_id= MooWoodle()->Product->update_product( $course );
		// 		if($product_id) $product_ids[] = $product_id;
		// 	}
		// }

		// // remove courses that not exist in moodle.
		// MooWoodle()->Course->remove_exclude_ids($course_ids);


		// if ($sync_now_options['sync_all_product']) {
		// 	// remove product that not exist in moodle.
		// 	MooWoodle()->Product->remove_exclude_ids($product_ids);
		// }

        // return rest_ensure_response(apply_filters('moowoodle_after_sync','success',$courses, $sync_now_options));
    }

    /**
     * Seve the setting set in react's admin setting page.
     * @param mixed $request
     * @return array
     */
    public function fetch_all_courses() {
        $response = MooWoodle()->Course->fetch_all_courses(['numberposts' => -1, 'fields' => 'ids']);
        return rest_ensure_response($response);
    }

    /**
     * Seve the setting set in react's admin setting page.
     * @param mixed $request
     * @return array
     */
    public function mw_get_log() {
        $logs = [];
        if (file_exists(MW_LOGS . "/error.txt")) {
            $logs = explode("\n", wp_remote_retrieve_body(wp_remote_get(get_site_url(null, str_replace(ABSPATH, '', MW_LOGS) . "/error.txt"))));
        }
        rest_ensure_response($logs);
    }
}
