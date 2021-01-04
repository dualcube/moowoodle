<?php

class MooWoodle_PostType {

  	private $labels = array();
	public $course;

	public function __construct() {
		
		$this->setup_post_type_labels_base();
		$this->setup_course_post_type();

		if ( is_admin() ) {
			$this->load_class( 'posttype-product' );
			$this->course = new MooWoodle_PostType_Product();
		}
		
		if ( is_admin() ) {
			global $pagenow;
			if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) ) {
				add_filter( 'post_updated_messages', array( &$this, 'setup_post_type_messages' ) );
			}
		}
	}

	public function setup_course_post_type() {
		
		$args = array(
			'labels'             => $this->create_post_type_labels( 'course', $this->labels[ 'course' ][ 'singular' ], $this->labels[ 'course' ][ 'plural' ], $this->labels[ 'course' ][ 'menu' ] ),
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
	
	private function create_post_type_labels( $token, $singular, $plural, $menu ) {
	  
		$labels = array(
			'name'               => sprintf( _x( '%s', 'post type general name', 'moowoodle' ), $plural ),
			'singular_name'      => sprintf( _x( '%s', 'post type singular name', 'moowoodle' ), $singular ),
			'add_new'            => sprintf( _x( 'Add New %s', $token, 'moowoodle' ), $singular ),
			'add_new_item'       => sprintf( __( 'Add New %s', 'moowoodle' ), $singular ),
			'edit_item'          => sprintf( __( 'Edit %s', 'moowoodle' ), $singular ),
			'new_item'           => sprintf( __( 'New %s', 'moowoodle' ), $singular ),
			'all_items'          => sprintf( __( '%s', 'moowoodle' ), $plural ),
			'view_item'          => sprintf( __( 'View %s', 'moowoodle' ), $singular ),
			'search_items'       => sprintf( __( 'Search %s', 'moowoodle' ), $plural ),
			'not_found'          => sprintf( __( 'No %s found', 'moowoodle' ), strtolower( $plural ) ),
			'not_found_in_trash' => sprintf( __( 'No %s found in Trash', 'moowoodle' ), strtolower( $plural ) ),
			'parent_item_colon'  => ''
		);
		return $labels;
	}

	public function setup_post_type_messages ( $messages ) {
		
		$messages[ 'course'] = $this->create_post_type_messages( 'course' );
		return $messages;
	}

	private function create_post_type_messages( $post_type ) {
		global $post, $post_ID;

		if ( ! isset( $this->labels[ $post_type ] ) ) {
			return array();
		}

		$messages = array(
			0  => '',
			1  => sprintf( __( '%s updated.' ), esc_attr( $this->labels[ $post_type ]['singular'] ) ),
			2  => __( 'Custom field updated.', 'moowoodle' ),
			3  => __( 'Custom field deleted.', 'moowoodle' ),
			4  => sprintf( __( '%s updated.', 'moowoodle' ), esc_attr( $this->labels[ $post_type ]['singular' ] ) ),
			5  => isset( $_GET[ 'revision' ] ) ? sprintf( __( '%2$s restored to revision from %1$s', 'moowoodle' ), 
																											 wp_post_revision_title( (int) $_GET[ 'revision' ], false ), 
																											 esc_attr( $this->labels[ $post_type ][ 'singular' ] ) ) : false,
			6  => sprintf( __( '%2$s published.' ), esc_url( get_permalink( $post_ID ) ), esc_attr( $this->labels[ $post_type ][ 'singular' ] ) ),
			7  => sprintf( __( '%s saved.', 'moowoodle' ), esc_attr( $this->labels[ $post_type ][ 'singular' ] ) ),
			8  => sprintf( __( '%2$s submitted.', 'moowoodle' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ), 
													esc_attr( $this->labels[ $post_type ][ 'singular' ] ) ),
			9  => sprintf( __( '%s scheduled for: <strong>%1$s</strong>.', 'moowoodle' ),
													date_i18n( __( ' M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ), 
													strtolower( esc_attr( $this->labels[ $post_type ][ 'singular' ] ) ) ),
			10 => sprintf( __( '%s draft updated.', 'moowoodle' ), esc_attr( $this->labels[ $post_type ][ 'singular' ] ) ),
		);
		return $messages;
	}

	private function setup_post_type_labels_base() {
	  	$this->labels['course'] = array( 'singular' => __( 'Course', 'moowoodle' ),
										 'plural' 	=> __( 'Courses', 'moowoodle' ), 
										 'menu' 	=> __( 'Courses', 'moowoodle' ) 
										);
	}
	
	/**
	 * Load class file
	 *
	 * @access public
	 * @param string $class_name (default: null)
	 * @return void
	 */
	public function load_class( $class_name = '' ) {
		global $MooWoodle;
		
		if ( '' != $class_name && '' != $MooWoodle->token ) {
			require_once ( 'posttypes/class-' . $MooWoodle->token . '-' . $class_name . '.php' );
		}
	}
}