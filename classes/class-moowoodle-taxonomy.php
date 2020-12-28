<?php

class MooWoodle_Toxonomy {
	
	public function __construct() {
		$this->register_course_cat_taxonomy();
	}
	
	/**
	 * course_cat Taxonomy
	**/
 	function register_course_cat_taxonomy() {
    	
    	register_taxonomy( 'course_cat', 'course',
      						array(
						        'labels'        => $this->create_taxonomy_labels( 'course' ),
						        'show_ui'       => false,
						        'show_tagcloud' => false,
						        'hierarchical'  => true,
						        'query_var' 		=> true
      						)
    					);
 	}
  
	private function create_taxonomy_labels( $post_type_name = '' ) {
		
		$labels = array(
					'name'              => sprintf( _x( '%s category', 'moowoodle' ), ucfirst( $post_type_name ) ),
					'singular_name'   	=> sprintf( _x( '%s category', 'moowoodle' ), ucfirst( $post_type_name ) ),
					'add_new_item'      => sprintf( _x( 'Add new %s category', 'moowoodle' ), $post_type_name ),
					'new_item_name'     => sprintf( _x( 'New %s category', 'moowoodle' ), $post_type_name ),
					'menu_name'			=> sprintf( _x( '%s category', 'moowoodle' ), ucfirst( $post_type_name ) ),//'Categories',
					'search_items' 	  	=> sprintf( _x( 'Search %s categories', 'moowoodle' ), $post_type_name ),//'Search Course Categories',
					'all_items' 	    => sprintf( _x( 'All %s categories', 'moowoodle' ), $post_type_name ),//'All Course Categories',
					'parent_item' 		=> sprintf( _x( 'Parent %s category', 'moowoodle' ), $post_type_name ),//'Parent Course Category',
					'parent_item_colon' => sprintf( _x( 'Parent %s category', 'moowoodle' ), $post_type_name ),//'Parent Course Category:',
					'edit_item' 		=> sprintf( _x( 'Edit %s category', 'moowoodle' ), $post_type_name ),//'Edit Course Category',
					'update_item' 	   	=> sprintf( _x( 'New %s category name', 'moowoodle' ), $post_type_name ),//'New Course Category Name'
				);		
		return $labels;
	}
}