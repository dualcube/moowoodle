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
			MooWoodle()->util->log( "moowoodle url:" . $term->get_error_message() . "\n");
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

		$terms = get_terms( [ 
			'taxonomy' => $taxonomy, 
			'hide_empty' => false,
			'meta_query' => [
				[
					'key'     => '_category_id',
					'compare' => 'EXISTS',
				],
			] ] );
		
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
}
