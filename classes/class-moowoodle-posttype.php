<?php
class MooWoodle_PostType {
  	private $labels = array();
	public $course;
	public function __construct() {
		$this->labels['course'] = array(
			'singular' => __( 'Course', MOOWOODLE_TEXT_DOMAIN ),
			'plural' 	=> __( 'Courses', MOOWOODLE_TEXT_DOMAIN ), 
			'menu' 	=> __( 'Courses', MOOWOODLE_TEXT_DOMAIN ) 
		);
		$this->setup_course_post_type();
	}
	public function setup_course_post_type() {
		$args = array(
			'labels'             => array(
				'name'               => sprintf( _x( '%s', 'post type general name', MOOWOODLE_TEXT_DOMAIN ), $this->labels[ 'course' ][ 'plural' ] ),
				'singular_name'      => sprintf( _x( '%s', 'post type singular name', MOOWOODLE_TEXT_DOMAIN ), $this->labels[ 'course' ][ 'singular' ] ),
				'add_new'            => sprintf( _x( 'Add New %s', 'course', MOOWOODLE_TEXT_DOMAIN ), $this->labels[ 'course' ][ 'singular' ] ),
				'add_new_item'       => sprintf( __( 'Add New %s', MOOWOODLE_TEXT_DOMAIN ), $this->labels[ 'course' ][ 'singular' ] ),
				'edit_item'          => sprintf( __( 'Edit %s', MOOWOODLE_TEXT_DOMAIN ), $this->labels[ 'course' ][ 'singular' ] ),
				'new_item'           => sprintf( __( 'New %s', MOOWOODLE_TEXT_DOMAIN ), $this->labels[ 'course' ][ 'singular' ] ),
				'all_items'          => sprintf( __( '%s', MOOWOODLE_TEXT_DOMAIN ), $this->labels[ 'course' ][ 'plural' ] ),
				'view_item'          => sprintf( __( 'View %s', MOOWOODLE_TEXT_DOMAIN ), $this->labels[ 'course' ][ 'singular' ] ),
				'search_items'       => sprintf( __( 'Search %s', MOOWOODLE_TEXT_DOMAIN ), $this->labels[ 'course' ][ 'plural' ] ),
				'not_found'          => sprintf( __( 'No %s found', MOOWOODLE_TEXT_DOMAIN ), strtolower( $this->labels[ 'course' ][ 'plural' ] ) ),
				'not_found_in_trash' => sprintf( __( 'No %s found in Trash', MOOWOODLE_TEXT_DOMAIN ), strtolower( $this->labels[ 'course' ][ 'plural' ] ) ),
			),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'query_var'          => true,
			'rewrite'            => true,
			'map_meta_cap'       => false,
			'has_archive'        => false,
			'hierarchical'       => false,
			'show_in_menu' 		 => false,
			'supports'           => array( 'title', 'editor' ),
			'capability_type' 	 => 'post',
			'capabilities'		 => array( 'create_posts'	=> false,
       									   'delete_posts' => false
										 )
		);
		register_post_type( 'course', $args );
	}
	private function setup_post_type_labels_base() {
	  	
	}
}