<?php

class DC_Woodle_Posttype_Course {

	public function __construct() {
		add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ) );
		add_action( 'manage_course_posts_custom_column', array( &$this, 'render_course_columns' ), 10, 2 );
		add_action( 'wp_trash_post', array( &$this, 'trash_course_callback' ) );
	
		add_filter( 'manage_edit-course_columns', array( &$this, 'course_column' ) );
		add_filter( 'manage_edit-course_sortable_columns', array( &$this, 'course_sortable_columns' ) );
		add_filter( 'bulk_actions-edit-course', array( &$this, 'course_bulk_actions' ) );
	}
	
	/**
	 * Add meta box for post type course.
	 *
	 * @access public
	 * @return void
	 */
	public function add_meta_box() {
	  global $DC_Woodle;
	  
		add_meta_box( 'course_details', __( 'Course Details', $DC_Woodle->text_domain ), array( &$this, 'metabox_course_details' ), 'course', 'normal' );
	}
	
	/**
	 * Content of meta box course details.
	 *
	 * @access public
	 * @return void
	 */
	public function metabox_course_details() {
		global $post, $DC_Woodle;
		$course_id = get_post_meta( $post->ID, '_course_id', true );
		$course_short_name = get_post_meta( $post->ID, '_course_short_name', true );
		$visibility = get_post_meta( $post->ID, '_visibility', true );
		$visibility_status = array( 'visible' => 'Visible',
	  														'hidden' => 'Hidden'
	  													);
		?>
		<div class="woodle_options_panel">
			<p>
				<label for="course_id"><?php _e( 'Course ID', $DC_Woodle->text_domain ); ?></label>
				<input type="text" id="course_id" name="course_id" class="short" value="<?php if( ! empty( $course_id ) ) echo $course_id; ?>" readonly>
			</p>
			<p>
				<label for="course_short_name"><?php _e( 'Course Short Name', $DC_Woodle->text_domain ); ?></label>
				<input type="text" id="course_short_name" name="course_short_name" class="short" value="<?php if( ! empty( $course_short_name ) ) echo $course_short_name; ?>" readonly>
			</p>
			<p>
				<label for="course_visibility"><?php _e( 'Visibility', $DC_Woodle->text_domain ); ?></label>
				<input type="text" id="course_visibility" name="course_visibility" class="short" value="<?php if( ! empty( $visibility ) ) echo $visibility_status[$visibility]; ?>" readonly>
			</p>
		</div>
		<?php
	}
	
	/**
	 * Values of columns.
	 *
	 * @access public
	 * @param array $column
	 * @param int $post_id
	 * @return void
	 */
	public function render_course_columns( $column, $post_id ) {
	  $post = get_post( $post_id );
	  $visibility_status = array( 'visible' => 'Visible',
	  														'hidden' => 'Hidden'
	  													);
	  switch( $column ) {
	  	// trideep_courseid
	    case 'courseid' :
	      $id = get_post_meta( $post_id, '_course_id', true );
	      echo $id;
	      break;
      case 'short_name':
        $post_author = get_post_meta( $post_id, '_course_short_name', true );
        echo $post_author;
        break;
			case 'category':
				$category_id = get_post_meta( $post_id, '_category_id', true );
				$term_id = woodle_get_term_by_moodle_id( $category_id, 'course_cat', 'woodle_term' );
				$course_category_path = get_woodle_term_meta( $term_id, '_category_path', true );
				$category_ids = explode( '/', $course_category_path );
				$course_path = array();
				
				if( ! empty( $category_ids ) ) {
					foreach( $category_ids as $cat_id ) {
						if( ! empty( $cat_id ) ) {
							$term_id = woodle_get_term_by_moodle_id( intval( $cat_id ), 'course_cat', 'woodle_term' );
							$term = get_term( $term_id, 'course_cat' );
							$course_path[] = $term->name;
						}
					}
				}
				
				if( ! empty( $course_path ) ) {
					$course_path = implode( ' / ', $course_path );
				}
				
				$term = get_term_by( 'id', $term_id, 'course_cat' );
				$sort_url = '';
				if( ! empty( $term ) ) {
					$sort_url = '<a href="' . admin_url( 'edit.php?course_cat=' . $term->slug . '&post_type=course' ) . '">' . $course_path . '</a>';
				}
				echo $sort_url;
				break;
			case 'visibility': 
				$visibility = get_post_meta( $post_id, '_visibility', true );
				echo ( ! empty( $visibility ) ) ? $visibility_status[$visibility] : '';
	      break;
			case 'idnumber':
				$idnumber = get_post_meta( $post_id, '_course_idnumber', true );
				echo $idnumber;
				break;
	  }
	}

	/**
	 * Add columns.
	 *
	 * @access public
	 * @param array $columns
	 * @return array
	 */
	public function course_column( $columns ) {
	  unset( $columns['date'] );
	  unset( $columns['title'] );
		return $columns + array( 'courseid' 	=> 'Course ID',
														 'title' 			=> 'Course Name',
														 'short_name' => 'Short Name',
														 'category' 	=> 'Category',
														 'visibility' => 'Visibility',
														 'idnumber'		=> 'Course ID Number'
													 );
	}
	
	/**
	 * Add sortable columns.
	 *
	 * @access public
	 * @param array $columns
	 * @return array
	 */
	public function course_sortable_columns( $columns ) {
		$custom = array(
			'courseid'   => 'courseid',
			'short_name' => 'short_name',
			'category'   => 'category',
			'name'       => 'title',
			'visibility' => 'visibility',
			'idnumber'	 => 'idnumber'
		);
		
		return wp_parse_args( $custom, $columns );
	}
	
	/**
	 * Trash related product.
	 *
	 * @access public
	 * @param int $post_id
	 * @return void
	 */
	public function trash_course_callback( $post_id ) {
		$post = get_post( $post_id );
		if( $post && $post->post_type == 'course' ) {
			$course_id = get_post_meta( $post_id, '_course_id', true );
			$product_id = woodle_get_post_by_moodle_id( $course_id, 'product' );
			wp_delete_post( $product_id, false );
		}
	}
	
	function course_bulk_actions( $actions ) {
		unset( $actions[ 'edit' ] );
		return $actions;
	}
}