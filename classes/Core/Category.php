<?php

namespace MooWoodle\Core;

class Category {

	/**
	 * Returns term by moodle category id
	 * @param int $category_id
	 * @param string $taxonomy (default: null)
	 * @param string $meta_key (default: null)
	 * @return object | null
	 */
	public static function get_category( $category_id, $taxonomy = '' ) {
		if ( ! $category_id || empty( $taxonomy ) || ! taxonomy_exists( $taxonomy ) ) {
			return null;
		}

		// Get the trermes basesd on moodle category id.
		$terms = get_terms( [
			'taxonomy' 	 => $taxonomy,
			'hide_empty' => false,
			'meta_query' => [
				[
					'key' 	  => '_category_id',
					'value'   => $category_id,
					'compare' => '='
				]
			]
		]);
		
		// Check no category found.
		if ( is_wp_error( $terms ) ) {
			return null;
		}
		
		return $terms[0];
	}

    /**
	 * Update moodle course categories in Wordpress site.
	 * @param array $categories
	 * @param string $taxonomy
	 * @return void
	 */
	public static function update_categories( $categories, $taxonomy ) {
		if ( empty( $taxonomy ) || ! taxonomy_exists( $taxonomy ) ) {
			return;
		}

		$updated_ids = [];

		if ( $categories ) {
			foreach ( $categories as $category ) {
				// Update category
				$categorie_id = self::update_category( $category, $taxonomy );
				
				// Store updated category id
				if ( $categorie_id ) {
					$updated_ids[] = $categorie_id;
				}

				\MooWoodle\Util::increment_sync_count( 'course' );
			}
		}

		// Remove all term exclude updated ids
		self::remove_exclude_ids( $updated_ids, $taxonomy );
	}

	/**
	 * Update a single category. If category not exist create new category.
	 * @param array $category
	 * @param string $taxonomy
	 * @return int | null catagory id
	 */
	public static function update_category( $category, $taxonomy ) {
		
		$term = self::get_category( $category[ 'id' ], $taxonomy );

		// If term is exist update it.
		if ( $term ) {
			$term = wp_update_term(
				$term->term_id,
				$taxonomy,
				[
					'name' 		  => $category['name'],
					'slug' 		  => "{$category['name']} {$category['id']}",
					'description' => $category['description']
				]
			);
		} else {
			// term not exist create it.
			$term = wp_insert_term(
				$category[ 'name' ],
				$taxonomy,
				[
					'description' => $category['description'],
					'slug' 		  => "{$category['name']} {$category['id']}"
				]
			);

			if ( ! is_wp_error( $term ) )
				add_term_meta( $term[ 'term_id' ], '_category_id', $category[ 'id' ], false );
		}

		// In success on update or insert sync meta data.
		if ( ! is_wp_error( $term ) ) {
			update_term_meta( $term[ 'term_id' ], '_parent', $category[ 'parent' ], '' );
			update_term_meta( $term[ 'term_id' ], '_category_path', $category[ 'path' ], false);

			return $category[ 'id' ];
		} else {
			//g( "moowoodle url:" . $term->get_error_message() . "\n");
		}

		return null;
	}

	/**
	 * Remove all category exclude provided ids
	 * @param array $exclude_ids
	 * @param string $taxonomy
	 * @return void
	 */
	private static function remove_exclude_ids( $exclude_ids, $taxonomy ) {

		$terms = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => false ] );

		if ( is_wp_error( $terms ) ) return;

		// Link with parent or delete term
		foreach ( $terms as $term ) {
			$category_id = get_term_meta( $term->term_id, '_category_id', true );
			
			if ( in_array( $category_id, $exclude_ids ) ) {
				
				$parent_category_id = get_term_meta( $term->term_id, '_parent', true );

				// get parent term id and continue if not exist
				$parent_term = self::get_category( $parent_category_id, $taxonomy );
				if( empty( $parent_term ) ) continue;

				// sync parent term with term
				wp_update_term( $term->term_id, $taxonomy, [ 'parent' => $parent_term->term_id ] );

			} else {
				// delete term if category is not moodle category.
				wp_delete_term( $term->term_id, $taxonomy );
			}
		}
	}

	/**
	 * Store Moodle categories in the database if not already present or update if changed.
	 * 
	 * @param array $categories_data An array of categories with 'id', 'name', and 'parent' fields.
	 * @return void
	 */
	public static function save_categories( $categories_data ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'moowoodle_categories';

		foreach ( $categories_data as $category ) {
			// Validate required fields.
			if ( empty( $category['id'] ) || empty( $category['name'] ) || ! isset( $category['parent'] ) ) {
				continue;
			}

			// Prepare data.
			$moodle_category_id = intval( $category['id'] );
			$name               = sanitize_text_field( $category['name'] );
			$parent_id          = intval( $category['parent'] );

			// Check if the category already exists.
			$existing = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT name, parent_id FROM `$table_name` WHERE `moodle_category_id` = %d",
					$moodle_category_id
				)
			);

			if ( null === $existing ) {
				// Insert new category.
				$wpdb->insert(
					$table_name,
					[
						'moodle_category_id' => $moodle_category_id,
						'name'               => $name,
						'parent_id'          => $parent_id,
					]
				);
			} elseif ( $existing->name !== $name || intval( $existing->parent_id ) !== $parent_id ) {
				// Update if data has changed.
				$wpdb->update(
					$table_name,
					[
						'name'      => $name,
						'parent_id' => $parent_id,
					],
					[ 'moodle_category_id' => $moodle_category_id ]
				);
			}

			// Increment sync count.
			\MooWoodle\Util::increment_sync_count( 'course' );
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

}
