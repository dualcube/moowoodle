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
	 * Get product from course.
	 * @param mixed $course_id moowoodle course id.
	 * @return null | \WC_Product return null if product not exist.
	 */
	public static function get_product_from_moodle_course( $course_id ) {
		$products = wc_get_products([
            'meta_query' => [
                [
                    'key'   => 'moodle_course_id',
                    'value' => $course_id,
					'compare' => '='
                ]
            ]
        ]);

		if ( empty( $products ) ) {
			return null;
		}

		return reset( $products );
	}

    /**
     * Update All product
     * @param mixed $courses
     * @return void
     */
    public static function update_products( $courses ) {
        $updated_ids = [];

        foreach ( $courses as $course ) {
            $product_id = self::update_product( $course );

            if ( $product_id ) {
                $updated_ids[] = $product_id;
            }

			\MooWoodle\Util::increment_sync_count();
		}

        self::remove_exclude_ids( $updated_ids );
    }

    /**
	 * Update moodle product data in WordPress WooCommerce.
	 * If product not exist create new product
	 * @param array $course (moodle course data)
	 * @return int course id
	 */
	public static function update_product( $course ) {
		if ( empty( $course ) || $course[ 'format' ] == 'site' ) return 0;

		// Manage setting of product sync option.
		$product_sync_setting = MooWoodle()->setting->get_setting( 'product_sync_option' );
		$product_sync_setting = is_array( $product_sync_setting ) ? $product_sync_setting : [];

		$create_product = array_intersect( $product_sync_setting, [ 'create_update', 'create' ] );
		$update_product = array_intersect( $product_sync_setting, [ 'create_update', 'update' ] );

		// None of the option is choosen.
		if ( ! $create_product && ! $update_product ) return 0;

		$product = self::get_product_from_moodle_course( $course[ 'id' ] );

        // create a new product if not exist.
        if( ! $product && $create_product ) {
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
        $product->set_sold_individually( true );
        $product->set_category_ids( [ $term->term_id ] );
        $product->set_virtual( true );
        $product->set_catalog_visibility( $course[ 'visible' ] ? 'visible' : 'hidden' );

		// get the course id linked with moodle.
        $linked_course_id = MooWoodle()->course->get_courses([
            'meta_key' 		=> 'moodle_course_id',
            'meta_value' 	=> $course[ 'id' ],
            'meta_compare' 	=> '=',
            'fields'	 	=> 'ids'
		])[0];

        // Set product meta data.
        $product->update_meta_data( '_course_startdate', $course[ 'startdate' ] );
        $product->update_meta_data( '_course_enddate', $course[ 'enddate' ] );
        $product->update_meta_data( 'moodle_course_id', $course[ 'id' ] );
        $product->update_meta_data( 'linked_course_id', $linked_course_id );
		$product->save();

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
            'status' => 'publish',
            'return' => 'ids',
        ]);

		// delete product.
		foreach ( $product_ids as $product_id ) {
            $product = wc_get_product( $product_id );
            $product->delete( true );
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
				if ( MooWoodle()->util->is_pro_active() ) {
					echo '<div class="notice notice-warning is-dismissible"><p>' . __('WooComerce Subbcription and WooComerce Product Bundles is supported only with ', 'moowoodle') . '<a href="' . MOOWOODLE_PRO_SHOP_URL . '">' . __('MooWoodle Pro', 'moowoodle') . '</></p></div>';
				}
			});
		}

		return $classnames;
	}

	/**
	 * Save product meta.
	 * @param int $post_id
	 * @return int | void
	 */
	public function save_product_meta_data( $post_id ) {
		// Security check
		if (
			! filter_input( INPUT_POST, 'product_meta_nonce', FILTER_DEFAULT ) === null
			|| ! wp_verify_nonce( filter_input( INPUT_POST, 'product_meta_nonce', FILTER_DEFAULT ) )
			|| ! current_user_can( 'edit_product', $post_id ) ) {
			return $post_id;
		}

		$course_id = filter_input( INPUT_POST, 'course_id', FILTER_DEFAULT );

		if ( $course_id ) {
			update_post_meta( $post_id, 'linked_course_id', wp_kses_post( $course_id ) );
			update_post_meta( $post_id, '_sku', 'course-' . get_post_meta( $course_id, '_sku', true ) );
			update_post_meta( $post_id, 'moodle_course_id', get_post_meta( $course_id, 'moodle_course_id', true ) );
		}
	}
}
