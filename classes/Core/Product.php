<?php

namespace MooWoodle\Core;

class Product {
    /**
     * Product class constructor function.
     */
    public function __construct() {
        // Add subcription product notice.
		add_filter( 'woocommerce_product_class', [ &$this, 'product_type_warning' ], 10, 2);
		
		// Course meta save with WooCommerce product save.
		add_action( 'woocommerce_process_product_meta', [ &$this, 'save_product_meta_data' ] );

		// Support for woocommerce product custom metadata query
		add_filter( 'woocommerce_product_data_store_cpt_get_products_query', [ &$this, 'handling_custom_meta_query_keys' ], 10, 3 );
    }

	/**
	 * Custom metadata query support for woocommerce product.
	 * @param mixed $wp_query_args
	 * @param mixed $query_vars
	 * @param mixed $data_store_cpt
	 * @return mixed
	 */
	public function handling_custom_meta_query_keys( $wp_query_args, $query_vars, $data_store_cpt ) {
		if ( ! empty( $query_vars[ 'meta_query' ] ) ) {
			$wp_query_args[ 'meta_query' ][] = $query_vars[ 'meta_query' ];
		}
			
		return $wp_query_args;
	}

	/**
	 * Retrieves a WooCommerce product associated with a Moodle course ID.
	 *
	 * @param int|string $course_id The Moodle course ID.
	 * @return \WC_Product|null The associated product, or null if no product is found.
	 */
	public static function get_product_from_moodle_course($course_id) {
		// Sanitize course ID
		$course_id = absint($course_id);

		// Query products with matching moodle_course_id
		$products = wc_get_products([
			'meta_query' => [
				[
					'key'     => 'moodle_course_id',
					'value'   => $course_id,
					'compare' => '=',
				],
			],
			'limit' => 1,
		]);

		// Return the first product or null
		return reset($products) ?: null;
	}

    /**
     * Update All product
     * @param mixed $courses
     * @return void
     */
	public static function update_products( $courses ) {
		$updated_ids = [];
	
		// Manage setting of product sync option.
		$product_sync_setting = MooWoodle()->setting->get_setting( 'product_sync_option', [] );
	
		$create_product = in_array( 'create', $product_sync_setting );
		$update_product = in_array( 'update', $product_sync_setting );
	
		// None of the options are chosen.
		if ( ! $create_product && ! $update_product ) {
			return;
		}
	
		// Update all products
		\MooWoodle\Util::set_sync_status( [
			'action'    => __( 'Update Product', 'moowoodle' ),
			'total'     => count( $courses ) - 1,
			'current'   => 0
		], 'course' );
	
		foreach ( $courses as $course ) {
	
			// Do nothing when the course is a site course.
			if ( $course['format'] == 'site' ) {
				continue;
			}
	
			$product_id = self::update_product( $course, $create_product );
	
			if ( $product_id ) {
				$updated_ids[] = $product_id;
			}
	
			\MooWoodle\Util::increment_sync_count( 'course' );
		}
	
		self::remove_exclude_ids( $updated_ids );
	}
	

    /**
	 * Update moodle product data in WordPress WooCommerce.
	 * If product not exist create new product
	 * @param array $course (moodle course data)
	 * @param bool $fource_create 
	 * @return int course id
	 */
	public static function update_product( $course, $fource_create = true ) {
		if ( empty( $course ) || $course[ 'format' ] == 'site' ) return 0;

		$product = self::get_product_from_moodle_course( $course[ 'id' ] );

        // create a new product if not exist.
        if( ! $product && $fource_create ) {
            $product = new \WC_Product_Simple();
        } 
		
		// Product is not exist
		if ( ! $product ) {
			return 0;
		}

        // get category term
        $term = MooWoodle()->category->get_category( $course[ 'categoryid' ], 'product_cat' );

        // Set product properties
        $product->set_name( $course[ 'fullname' ] );
        $product->set_slug( $course[ 'shortname'] );
        $product->set_description( $course[ 'summary' ] );
        $product->set_status( 'publish' );
        $product->set_category_ids( [ $term->term_id ] );
        $product->set_virtual( true );
        $product->set_catalog_visibility( $course[ 'visible' ] ? 'visible' : 'hidden' );

		// Set product's squ
		try {
			$product->set_sku( $course[ 'idnumber' ] );
		} catch ( \Exception $error ) {
			\MooWoodle\Util::log( "Unable to set product's( id=" . $product->get_id() . ") SQU." );
		}

		// get the course id linked with moodle.
        $wp_course = MooWoodle()->course->get_course([
			'moodle_course_id' => $course['id']
		]);


		// Extract the course ID from the result.
		$wp_course_id = isset($wp_course[0]['id']) ? (int) $wp_course[0]['id'] : 0;

        // Set product meta data.
        $product->update_meta_data( '_course_startdate', $course[ 'startdate' ] );
        $product->update_meta_data( '_course_enddate', $course[ 'enddate' ] );
        $product->update_meta_data( 'moodle_course_id', $course[ 'id' ] );
        $product->update_meta_data( 'linked_course_id', $wp_course_id );
		$product->set_status( 'publish' );
		$product->save();

		MooWoodle()->course->update_course( $course['id'], [ 'product_id' => $product->get_id() ] );

		return $product->get_id();
	}
	
    
	/**
	 * Delete all the product which id is not prasent in $exclude_ids array.
	 * @param array $exclude_ids (product ids)
	 * @return void
	 */
	public static function remove_exclude_ids( $exclude_ids ) {
        // get all product except $exclude_ids array
		$product_ids = \wc_get_products([
			'exclude' => $exclude_ids,
			'status'  => 'publish',
			'return'  => 'ids',
			'meta_query' => [
				[
					'key'     => 'linked_course_id',
					'compare' => 'EXISTS',
				],
			],
		]);

		// delete product.
		foreach ( $product_ids as $product_id ) {
            $product = wc_get_product( $product_id );
            $product->set_status( 'draft' );
			$product->save();
		}
	}

    /**
	 * Add meta box panal.
	 * @return void
	 */
	public function product_type_warning( $classnames, $product_type ) {
		// Get all active plugins
		$active_plugins = get_option( 'active_plugins', [] );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', [] ) );
		}

		if (
			in_array( 'woocommerce-subscriptions/woocommerce-subscriptions.php', $active_plugins )
			|| array_key_exists( 'woocommerce-product/woocommerce-subscriptions.php', $active_plugins )
			|| in_array('woocommerce-product-bundles/woocommerce-product-bundles.php', $active_plugins)
			|| array_key_exists('woocommerce-product-bundles/woocommerce-product-bundles.php', $active_plugins)
		) {
			add_action( 'admin_notices', function() {
				if ( MooWoodle()->util->is_khali_dabba() ) {
					echo '<div class="notice notice-warning is-dismissible"><p>' . __('WooComerce Subbcription and WooComerce Product Bundles is supported only with ', 'moowoodle') . '<a href="' . MOOWOODLE_PRO_SHOP_URL . '">' . __('MooWoodle Pro', 'moowoodle') . '</a></p></div>';
				}
			});
		}

		return $classnames;
	}

	/**
	 * Link course to a WooCommerce product (Free version).
	 *
	 * @param int $product_id
	 * @return int
	 */
	public function save_product_meta_data( $product_id ) {
		// Verify nonce
		$nonce = filter_input( INPUT_POST, 'product_meta_nonce' );
		if ( ! $nonce || ! wp_verify_nonce( $nonce ) ) {
			return $product_id;
		}

		$link_type = sanitize_text_field( filter_input( INPUT_POST, 'link_type' ) ?: '' );
		$link_item = absint( filter_input( INPUT_POST, 'linked_item' ) ?: 0 );

		// Only process if it's a course link
		if ( $link_type !== 'course' ) {
			return $product_id;
		}

		// Unlink previous course if exists
		$prev_course_id = absint( get_post_meta( $product_id, 'linked_course_id', true ) );
		if ( $prev_course_id ) {
			delete_post_meta( $product_id, 'linked_course_id' );
			delete_post_meta( $product_id, 'moodle_course_id' );

			$course = MooWoodle()->course->get_course([ 'product_id' => $product_id ]);
			if ( ! empty( $course[0]['id'] ) ) {
				MooWoodle()->course->update_course( $course[0]['moodle_course_id'], [ 'product_id' => 0 ] );
			}
		}

		// Link new course if provided
		if ( $link_item ) {
			update_post_meta( $product_id, 'linked_course_id', $link_item );

			$course = MooWoodle()->course->get_course([ 'id' => $link_item ]);

			if ( ! empty( $course[0]['moodle_course_id'] ) ) {
				update_post_meta( $product_id, 'moodle_course_id', (int) $course[0]['moodle_course_id'] );
				MooWoodle()->course->update_course( (int) $course[0]['moodle_course_id'], [ 'product_id' => $product_id ] );
			}
		}

		return $product_id;
	}



}
