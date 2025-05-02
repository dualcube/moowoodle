<?php
namespace MooWoodle;
defined('ABSPATH') || exit;
/**
 * plugin Install
 *
 * @version     3.1.7
 * @package     MooWoodle
 * @author      DualCube
 */
class Installer {
    /**
     * Construct installation.
     * @return void
     */
    public function __construct() {
        if ( get_option( 'moowoodle_version' ) != MOOWOODLE_PLUGIN_VERSION ) {

            $this->set_default_settings();

            $this->create_databases();

            $this->run_default_migration();

            update_option( 'moowoodle_version', MOOWOODLE_PLUGIN_VERSION );
            
            do_action( 'moowoodle_updated' );
        }
    }

    /**
     * Database creation functions
     * @return void
     */
    public static function create_databases() {
        global $wpdb;

        $collate = '';

        if ($wpdb->has_cap('collation')) {
            $collate = $wpdb->get_charset_collate();
        }

        $wpdb->query(
            "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "moowoodle_enrollment` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) NOT NULL DEFAULT 0,
                `user_email` varchar(100) NOT NULL,
                `course_id` bigint(20) NOT NULL,
                `cohort_id` bigint(20) NOT NULL,
                `group_id` bigint(20) NOT NULL,
                `order_id` bigint(20) NOT NULL,
                `item_id` bigint(20) NOT NULL,
                `status` varchar(20) NOT NULL,
                `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `group_item_id` bigint(20) NOT NULL,
                PRIMARY KEY (`id`)
            ) $collate;"
        );

        $wpdb->query(
            "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "moowoodle_categories` (
                `moodle_category_id` bigint(20) NOT NULL,
                `name` varchar(255) NOT NULL,
                `parent_id` bigint(20) NOT NULL DEFAULT 0,
                PRIMARY KEY (`moodle_category_id`)
            ) $collate;"
        );
        

        $wpdb->query(
            "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "moowoodle_courses` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `moodle_course_id` bigint(20) NOT NULL,
                `shortname` varchar(255) NOT NULL,
                `category_id` bigint(20) NOT NULL,
                `fullname` text NOT NULL,
                `startdate` bigint(20) DEFAULT NULL,
                `enddate` bigint(20) DEFAULT NULL,
                `created` bigint(20) DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `moodle_course_id` (`moodle_course_id`)
            ) $collate;"
        ); 
    }

    /**
     * Migrate database
     * @return void
     */
    public static function run_default_migration() {
        $previous_version = get_option( 'moowoodle_version', '' );

        if ( version_compare( $previous_version, '3.2.12', '<' ) ) {
            self::migrate_categories();
            self::migrate_courses();
            self::migrate_enrollments();
        }
    }
	/**
	 * Migrate WordPress term data to the MooWoodle categories table.
	 * 
	 * This function reads terms from the 'course_cat' taxonomy that have Moodle category IDs
	 * stored in term meta (_category_id), and inserts or updates them in the moowoodle_categories table.
	 *
	 * @return void
	 */
	public static function migrate_categories() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'moowoodle_categories';

		// Get terms with '_category_id' meta and optional '_parent' meta for 'course_cat' taxonomy
		$query = $wpdb->prepare("
			SELECT 
				t.term_id,
				t.name,
				CAST(tm.meta_value AS UNSIGNED) AS moodle_category_id,
				COALESCE(CAST(pm.meta_value AS UNSIGNED), 0) AS parent_id
			FROM {$wpdb->terms} t
			INNER JOIN {$wpdb->term_taxonomy} tt 
				ON t.term_id = tt.term_id
			INNER JOIN {$wpdb->termmeta} tm 
				ON t.term_id = tm.term_id AND tm.meta_key = '_category_id' AND tm.meta_value > 0
			LEFT JOIN {$wpdb->termmeta} pm 
				ON t.term_id = pm.term_id AND pm.meta_key = '_parent'
			WHERE tt.taxonomy = %s
		", 'course_cat');

		$terms = $wpdb->get_results( $query, ARRAY_A );

		if ( empty( $terms ) ) {
			return;
		}

		foreach ( $terms as $term ) {
			$moodle_category_id = (int) $term['moodle_category_id'];
			$name               = sanitize_text_field( $term['name'] );
			$parent_id          = (int) $term['parent_id'];

			// Check if the category already exists in the custom table
			$existing = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT name, parent_id FROM `$table_name` WHERE moodle_category_id = %d",
					$moodle_category_id
				),
				ARRAY_A
			);

			if ( ! $existing ) {
				// Insert new category
				$wpdb->insert(
					$table_name,
					[
						'moodle_category_id' => $moodle_category_id,
						'name'               => $name,
						'parent_id'          => $parent_id,
					],
					[ '%d', '%s', '%d' ]
				);
			} elseif ( $existing['name'] !== $name || (int) $existing['parent_id'] !== $parent_id ) {
				// Update if name or parent has changed
				$wpdb->update(
					$table_name,
					[
						'name'      => $name,
						'parent_id' => $parent_id,
					],
					[ 'moodle_category_id' => $moodle_category_id ],
					[ '%s', '%d' ],
					[ '%d' ]
				);
			}
		}
	}

    /**
	 * Migrate courses from post table
	 */
	public static function migrate_courses() {
		global $wpdb;
	
		$courses = get_posts( [
			'post_type'      => 'course',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'meta_key'       => 'moodle_course_id',
		] );
	
		if ( empty( $courses ) ) return;
	
		foreach ( $courses as $course ) {
			$moodle_course_id = get_post_meta( $course->ID, 'moodle_course_id', true );
			if ( ! $moodle_course_id ) continue;
	
			$shortname   = get_post_meta( $course->ID, '_course_short_name', true );
			$category_id = get_post_meta( $course->ID, '_category_id', true );
			$startdate   = get_post_meta( $course->ID, '_course_startdate', true );
			$enddate     = get_post_meta( $course->ID, '_course_enddate', true );
			$product_id  = get_post_meta( $course->ID, 'linked_product_id', true );
	
			// Check if course already exists
			$exists = $wpdb->get_var( $wpdb->prepare(
				"SELECT id FROM {$wpdb->prefix}moowoodle_courses WHERE moodle_course_id = %d",
				$moodle_course_id
			) );
	
			$data = [
				'moodle_course_id' => (int) $moodle_course_id,
				'shortname'        => sanitize_text_field( $shortname ),
				'category_id'      => (int) $category_id,
				'fullname'         => sanitize_text_field( $course->post_title ),
				'startdate'        => $startdate ? (int) $startdate : null,
				'enddate'          => $enddate ? (int) $enddate : null,
				'created'          => time(),
			];
	
			if ( $exists ) {
				$wpdb->update(
					$wpdb->prefix . 'moowoodle_courses',
					$data,
					[ 'id' => $exists ]
				);
				$new_course_id = $exists;
			} else {
				$wpdb->insert(
					$wpdb->prefix . 'moowoodle_courses',
					$data
				);
				$new_course_id = $wpdb->insert_id;
			}
	
			if ( $product_id ) {
				update_post_meta( $product_id, 'linked_course_id', $new_course_id );
			}
	
			wp_delete_post( $course->ID, true );
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
     * Set default moowoodle admin settings.
     * @return void
     */
    private function set_default_settings() {
        $general_settings = [
            'moodle_url'          => '',
            'moodle_access_token' => '',
        ];
        // Default value for sso setting
        $sso_settings = [
            'moowoodle_sso_enable'     => [],
            'moowoodle_sso_secret_key' => '',
        ];
        // Default value for display setting
        $display_settings = [
            'start_end_date'                    => ['start_end_date'],
            'my_courses_priority'               => 0,
            'my_groups_priority'               => 1,
            'moowoodle_create_user_custom_mail' => [],
        ];
        // Default value for log setting
        $tool_settings = [
            'moowoodle_adv_log' => [],
            'moodle_timeout'    => 5,
            'schedule_interval' => 1
        ];
        // Default value sync course setting
        $course_settings = [
            'sync-course-options'      => [ 'sync_courses_category', 'sync_courses_sku' ],
            'product_sync_option'      => [ 'create', 'update' ],
        ];
        // Default value for sync user setting
        $user_settings = [
            'wordpress_user_role'  => ['customer'],
            'moodle_user_role'     => ['5'],
        ];
        // Update default settings
        update_option( 'moowoodle_general_settings', array_merge(
            $general_settings,
            get_option( 'moowoodle_general_settings', [] )
        ));
        update_option( 'moowoodle_sso_settings', array_merge(
            $sso_settings,
            get_option( 'moowoodle_sso_settings', [] )
        ));
        update_option( 'moowoodle_display_settings', $display_settings );
        update_option( 'moowoodle_tool_settings', $tool_settings );
        update_option( 'moowoodle_synchronize_course_settings', $course_settings );
        update_option( 'moowoodle_synchronize_user_settings',$user_settings );
    }
}
