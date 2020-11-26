<?php
class MooWoodle_Posttype_Product {

	public function __construct() {
		add_action( 'add_meta_boxes', array( &$this, 'add_meta_boxes' ) );
		add_action( 'save_post_product', array( &$this, 'save_product_callback' ) );
		
	}

		
	/**
   * Creates custom meta box for product types.
   *
   * @access public
   * @return void
   */  
  public function add_meta_boxes() {
  	global $MooWoodle;
  	
    add_meta_box( 'select_courses', __( 'Moodle Linked Course', 'moowoodle' ), array( &$this, 'add_product_meta_boxes' ), 'product', 'normal' );

  }
	
  /**
   * Add meta box content.
   *
   * @access public
   * @return void
   */ 


	public function add_product_meta_boxes() {
		global $post, $MooWoodle;
		
		$course_id = get_post_meta( $post->ID, '_course_id', true );
		$product_course_id = get_post_meta( $post->ID, 'product_course_id', true );
		$post_id = woodle_get_post_by_moodle_id( $course_id, 'course' );
		
		$courses = woodle_get_posts( array( 'post_type' => 'course', 'post_status' => 'publish' ) );
		?>
		<p>
			<label for="courses"><?php _e( 'Linked Course', 'moowoodle' ); ?></label>
			<select id="courses-select" name="course_id">
				<option value="0"><?php _e( 'Select course...', 'moowoodle' ); ?></option>
				<?php
				if( ! empty( $courses ) ) {
					foreach($courses as $course) {
						$id = get_post_meta( $course->ID, '_course_id', true );
						$course_short_name = get_post_meta( $course->ID, '_course_short_name', true );
						$category_id = get_post_meta( $course->ID, '_category_id', true );
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
						$course_name = '';
						if( ! empty( $course_path ) ) {
							$course_name = implode( ' / ', $course_path );
							$course_name .= ' - ';
						}
						$course_name .= $course->post_title;
						if( ! empty( $id ) ) {
							?>
							
							<option value="<?php echo $id; ?>" <?php if( ! empty( $product_course_id ) && $id == $product_course_id) echo 'selected="selected"'; ?>><?php echo $course_name; if( ! empty( $course_short_name ) ) echo " ( " . $course_short_name . " )"; ?></option>
							<?php
						}

					}
				}
				?>
			</select>
		</p>   
    <?php
    	echo "Cannot find your course in this list?"
    ?>
     <a href="<?php echo get_site_url().'/wp-admin/admin.php?page=moowoodle-synchronization' ?>" target="_blank"><?php _e('Synchronize Moodle Courses from here.', 'moowoodle'); ?></a>
    <?php

    // Nonce field (for security)
    echo '<input type="hidden" name="product_meta_nonce" value="' . wp_create_nonce() . '">';

	}	

	
	/**
   * Save product meta.
   *
   * @access public
   * @param int $post_id
   * @return void
   */  
	public function save_product_callback( $post_id ) {
		require_once( ABSPATH . 'wp-admin/includes/screen.php' );
		
		// Security check
	    if ( ! isset( $_POST[ 'product_meta_nonce' ] ) ) {
	        return $post_id;
	    }

	    //Verify that the nonce is valid.
	    if ( ! wp_verify_nonce( $_POST[ 'product_meta_nonce' ] ) ) {
	        return $post_id;
	    }

	    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	        return $post_id;
	    }

	    if ( ! current_user_can( 'edit_product', $post_id ) ) {
	        return $post_id;
	    }

	    if( isset($_POST[ 'course_id' ]) ) {
        	update_post_meta( $post_id, 'product_course_id', wp_kses_post($_POST[ 'course_id' ]) );
	    }

	}
}
